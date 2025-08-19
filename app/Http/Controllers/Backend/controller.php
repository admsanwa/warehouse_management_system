<?php

namespace Modules\Article\Http\Controllers;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Track;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\DB;
use Modules\Article\Entities\Page;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isNull;
use Modules\Article\Entities\Category;
use Modules\Article\Entities\Signature;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sign;
use PhpParser\Node\Stmt\Else_;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{

    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index(Request $request, $isTrashed = false)
    {
        Log::info('Request Type: ' . gettype($request));

        if (is_null($this->user) || !$this->user->can('category.view')) {
            $message = 'You are not allowed to access this category!';
            return view('errors.403', compact('message'));
        }

        if ($request->ajax()) {
            $query = Category::select([
                'categories.id',
                'categories.request_by',
                'categories.department_name',
                'categories.applied_to_position',
                'categories.priority',
                'categories.qty_request',
                'categories.doc_number',
                'categories.file_upload_req',
                DB::raw('COUNT(DISTINCT CASE WHEN resume.deleted_at IS NULL THEN resume.candidate_name END) AS count_candidate'),
                DB::raw('CASE WHEN categories.approve IS NULL THEN 1 ELSE 0 END AS approve_null'),
                DB::raw('CASE WHEN categories.approve = "1" THEN "Approved" WHEN categories.approve = "0" THEN "Rejected" ELSE "Open" END AS approval_form'),
                DB::raw('CASE WHEN categories.approve != "1" THEN 1 ELSE 0 END AS approve_ok')
            ])
                ->leftJoin('resume', 'categories.doc_number', '=', 'resume.doc_number') // Ensure the join is on doc_number
                ->groupBy('categories.id', 'categories.request_by', 'categories.department_name', 'categories.applied_to_position', 'categories.priority', 'categories.qty_request', 'categories.doc_number')
                ->orderBy('categories.id', 'desc');

            // role users
            $roles = Auth::user()->getRoleNames();
            $department = Auth::user()->department_name;
            if ($roles->contains('Users')) {
                $query->where('categories.department_name', 'like', '%' . $department . '%');
            }

            if ($isTrashed) {
                $query->onlyTrashed();
            }

            $categories = $query->get();

            Log::info('Categories data: ', $categories->toArray());
            // button index
            function generateButton($route, $icon, $color, $title, $label)
            {
                return '
                    <div class="text-center mx-1">
                        <a class="btn waves-effect waves-light btn-' . $color . ' btn-sm btn-circle" title="' . $title . '" href="' . $route . '">
                            <i class="fa ' . $icon . '"></i>
                        </a>
                        <div class="mt-1">' . $label . '</div>
                    </div>';
            }

            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('candidate_actions', function ($row) {
                    $createCandidateRoute = route('admin.pages.create', ['doc_number' => $row->doc_number]);
                    $indexRoute = route('admin.pages.index', ['doc_number' => $row->doc_number]);
                    $updateRoute = route('admin.pages.create', ['doc_number' => $row->doc_number]); // Assuming a distinct route for updating
                    $roles = Auth::user()->getRoleNames();

                    if ($roles->contains('Users')) {
                        return '
                            <div class="d-flex justify-content-start align-items-center">' .
                            generateButton($indexRoute, 'fa-eye', 'secondary', 'Candidates List', 'List') .
                            '</div>';
                    } else {
                        return '
                            <div class="d-flex justify-content-start align-items-center">' .
                            generateButton($createCandidateRoute, 'fa-plus', 'primary', 'Create Candidate', 'Create') .
                            generateButton($indexRoute, 'fa-eye', 'secondary', 'Candidates List', 'List') .
                            generateButton($updateRoute, 'fa-edit', 'success', 'Update Candidates', 'Update') .
                            '</div>';
                    }
                })
                ->addColumn('action', function ($row) use ($isTrashed) {
                    $csrf = csrf_field();
                    $method_delete = method_field("delete");
                    $html = "";

                    $html .= '<div class="d-flex justify-content-start align-items-center">
                            <div class="text-center mx-1"> 
                                    <a class="btn waves-effect waves-light btn-secondary btn-sm btn-circle" 
                                    title="View Form Details" href="' . route('admin.categories.show', $row->id) . '"> <i class="fa fa-eye"></i>
                                    </a>
                                    <div class="mt-1">View</div>
                            </div>';

                    if (is_null($row->deleted_at)) {
                        $deleteRoute = route('admin.categories.destroy', [$row->id]);
                        $roles = Auth::user()->getRoleNames();
                        if ($roles->contains('Users')) {
                            if ($row->approve_ok) {
                                if ($this->user->can('category.edit')) {
                                    $html .= '<div class="text-center mx-1">
                                            <a class="btn waves-effect waves-light btn-success btn-sm btn-circle ml-1" title="Edit Form Details" href="' . route('admin.categories.edit', $row->id) . '">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <div class="mt-1">Edit</div>
                                        </div>';
                                }
                            }
                        } else {
                            if ($this->user->can('category.edit')) {
                                $html .= '<div class="text-center mx-1">
                                            <a class="btn waves-effect waves-light btn-success btn-sm btn-circle ml-1" title="Edit Form Details" href="' . route('admin.categories.edit', $row->id) . '">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <div class="mt-1">Edit</div>
                                        </div>';
                            }
                        }

                        if ($this->user->can('category.delete')) {
                            $html .= '<div class="text-center mx-1">
                                        <a class="btn waves-effect waves-light btn-danger btn-sm btn-circle ml-1 text-white" title="Delete Form" id="deleteItem' . $row->id . '">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <div class="mt-1">Delete</div>
                                    </div>';
                            $html .= '
                            <script>
                            $("#deleteItem' . $row->id . '").click(function(){
                                swal.fire({ 
                                    title: "Are you sure?",
                                    text: "Form will be removed for display!",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Yes, delete it!"
                                }).then((result) => { 
                                    if (result.value) {
                                        $("#deleteForm' . $row->id . '").submit();
                                    }
                                });
                            });
                            </script>';

                            $html .= '
                            <form id="deleteForm' . $row->id . '" action="' . $deleteRoute . '" method="post" style="display:none">' . $csrf . $method_delete . '
                            </form>';
                        }

                        // download data
                        $html .= '<div class="text-center mx-1">';
                        if (!is_null($row->file_upload_req)) {
                            $downloadRoute = route('admin.files.download', ['filename' => basename($row->file_upload_req)]);
                            $html .= '<a href="' . $downloadRoute . '" class="btn waves-effect waves-light btn-info btn-sm btn-circle ml-1 text-white" title="Download Form" id="downloadForm' . $row->id . '">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <div class="mt-1">Download</div>
                                    </div>';
                        } else {
                            $html .= '<a class="btn waves-effect waves-light btn-info btn-sm btn-circle" title="Download Form" href="javascript:void(0);" data-toggle="modal" data-target="#downloadForm">
                                            <i class="fa fa-download"></i>
                                       </a>';
                        }
                        $html .= '</div>';
                        $html .= '</div>';
                    } else {
                        // not run delete permanent
                        $deleteRoutePermanent = route('admin.categories.trashed.destroy', [$row->id]);

                        if ($this->user->can('category.delete')) {
                            $html .= '<a class="btn waves-effect waves-light btn-danger btn-sm btn-circle ml-1 text-white" title="Delete Form Permanently" id="deleteItemPermanent' . $row->id . '"><i class="fa fa-trash"></i></a>';

                            $html .= '
                            <script>
                            $("#deleteItemPermanent' . $row->id . '").click(function(){
                                swal.fire({ 
                                    title: "Are you sure?",
                                    text: "Form will be deleted permanently!",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Yes, delete it!"
                                }).then((result) => { 
                                    if (result.value) {
                                        $("#deletePermanentForm' . $row->id . '").submit();
                                    }
                                });
                            });
                            </script>';

                            $html .= '
                            <form id="deletePermanentForm' . $row->id . '" action="' . $deleteRoutePermanent . '" method="post" style="display:none">' . $csrf . $method_delete . '
                            </form>';
                        }
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('count_candidate', function ($row) {
                    return $row->count_candidate;
                })
                ->editColumn('approval_form', function ($row) {
                    return $row->approval_form;
                })
                ->rawColumns(['candidate_actions', 'action']) // Ensure the columns are not escaped
                ->make(true);
        }

        // role users
        $roles = Auth::user()->getRoleNames();
        $department = Auth::user()->department_name;
        $docNumber = Category::where('department_name', $department)->pluck('doc_number');
        if ($roles->contains('Users')) {
            $count_categories = count(Category::where('department_name', 'like', '%' . $department . '%')->get());
        } else {
            $count_categories = count(Category::select('id')->get());
        }

        $count_qtyreq = count(Category::select('id')->get());
        $count_trashed_categories = count(Category::select('id')->where('deleted_at', '!=', null)->get());

        return view('article::categories.index', compact('count_categories', 'count_trashed_categories', 'count_qtyreq'));
    }

    public function create(Request $request)
    {
        $categories = Category::printCategory(null, $layer = 2);

        // Get the site value from the request (if available)
        $site = $request->input('site', '01'); // Default to '01' if not provided
        $docNumber = Category::generateDocNumber($site);

        $departments = Department::all();
        $positions = Position::all();
        $user = Auth::user();
        $firstName = $user->first_name;
        $lastName = $user->last_name;

        return view('article::categories.create', compact('categories', 'docNumber', 'departments', 'positions', 'firstName', 'lastName'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('category.create')) {
            return abort(403, 'You are not allowed to access this page !');
        }

        $request->validate([
            'request_date'  => 'nullable|string',
            'request_by'    => 'required|string',
            'doc_number'       => 'required|string',
            'department_name'       => 'required|string',
            'applied_to_position'   => 'nullable|string',
            'level'         => 'required|string',
            'status'        => 'required|string',
            'priority'      => 'required|string',
            'experience'    => 'nullable|string',
            'gender'        => 'required|string',
            'age'           => 'nullable|string',
            'skills'        => 'array',
            'skills.*'      => 'string',
            'education_min' => 'required|string',
            'education_max' => 'required|string',
            'qty_request'   => 'required|integer',
            'reason'        => 'required|string',
            'desc_reason'   => 'nullable|string',
            'file_upload_req' => 'file|mimes:pdf,doc,docx|max:2048',
            'site'          => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $category = new Category();
            $category->doc_number = $request->doc_number;
            $category->request_date = $request->request_date;
            $category->request_by = $request->request_by;
            $category->department_name = $request->department_name;
            $category->applied_to_position = $request->applied_to_position;
            $category->level = $request->level;
            $category->status = $request->status;
            $category->priority = $request->priority;
            $category->experience = $request->experience;
            $category->gender = $request->gender;
            $category->age = $request->age;
            $category->education_min = $request->education_min;
            $category->education_max = $request->education_max;
            $category->qty_request = $request->qty_request;
            $category->reason = $request->reason;
            $category->desc_reason = $request->desc_reason;
            $category->site = $request->site;
            $category->approve = null;

            $category->skills = $request->has('skills') ? $request->skills : [];
            if (!is_null($request->file_upload_req)) {
                $category->file_upload_req = UploadHelper::upload('file_upload_req', $request->file_upload_req,  $request->request_by . '-' . date('Y-m-d', time()) .
                    '-filereq', 'public/assets/files/req');
            }

            $category->created_at = Carbon::now();
            $category->created_by = Auth::id();
            $category->updated_at = Carbon::now();

            $category->save();

            Track::newTrack($category->request_by, 'New form status has been created');
            DB::commit();
            session()->flash('success', 'New form has been created successfully !!');
            return redirect()->route('admin.categories.show', $category->id);
        } catch (\Exception $e) {
            session()->flash('sticky_error', $e->getMessage());
            DB::rollBack();
            return back();
        }
    }

    public function show($id)
    {
        if (is_null($this->user) || !$this->user->can('category.view')) {
            $message = 'You are not allowed to access this category!';
            return view('errors.403', compact('message'));
        }

        // signature
        $category = Category::findOrFail($id);
        $signature = Signature::where('name', $category->request_by)->first();
        $category->skills = implode(', ', $category->skills);
        $signatureApp = Signature::where('nik', $category->nikapp)->first();
        $signatureApp2 = Signature::where('nik', $category->nikapp2)->first();
        $nameApp = Admin::where('nik', $category->nikapp)->first();
        $nameApp2 = Admin::where('nik', $category->nikapp2)->first();
        $approveValue = $category->approve;
        $roles = Auth::user()->getRoleNames();

        return view('article::categories.show', compact('category', 'signature', 'signatureApp', 'nameApp', 'roles', 'approveValue', 'signatureApp2', 'nameApp2'));
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('category.edit')) {
            $message = 'You are not allowed to access this page !';
            return view('errors.403', compact('message'));
        }

        $category   = Category::find($id);
        $categories = Category::printCategory($category->parent_category_id, $layer = 2);
        $departments = Department::all();
        $positions = Position::all();

        return view('article::categories.edit', compact('categories', 'category', 'departments', 'positions'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('category.edit')) {
            $message = 'You are not allowed to access this page !';
            return view('errors.403', compact('message'));
        }

        $category = Category::find($id);
        $departmentName = $category->department_name;
        if (is_null($category)) {
            session()->flash('error', "The page is not found !");
            return redirect()->route('admin.categories.index');
        }

        $request->validate([
            'request_date'  => 'nullable|string',
            'request_by'    => 'required|string',
            'department_name'       => 'required|string',
            'applied_to_position'   => 'nullable|string',
            'level'         => 'required|string',
            'status'        => 'required|string',
            'priority'      => 'required|string',
            'experience'    => 'nullable|string',
            'gender'        => 'required|string',
            'age'           => 'nullable|string',
            'skills'        => 'nullable|array',
            'skills.*'        => 'string',
            'education_min' => 'required|string',
            'education_max' => 'required|string',
            'qty_request'   => 'required|integer',
            'reason'        => 'required|string',
            'desc_reason'   => 'nullable|string',
            'file_upload_req' => 'file|mimes:pdf,doc,docx|max:2048'
        ]);

        try {
            DB::beginTransaction();
            $category->doc_number = $request->doc_number;
            $category->site = $request->site;
            $category->request_date = $request->request_date;
            $category->request_by = $request->request_by;
            $category->department_name = $request->department_name;
            $category->applied_to_position = $request->applied_to_position;
            $category->level = $request->level;
            $category->status = $request->status;
            $category->priority = $request->priority;
            $category->experience = $request->experience;
            $category->gender = $request->gender;
            $category->age = $request->age;
            $category->education_min = $request->education_min;
            $category->education_max = $request->education_max;
            $category->qty_request = $request->qty_request;
            $category->reason = $request->reason;
            $category->desc_reason = $request->desc_reason;
            $category->approve = null;

            $category->skills = $request->has('skills') ? $request->skills : [];
            if (!is_null($request->file_upload_req)) {
                $category->file_upload_req = UploadHelper::upload('file_upload_req', $request->file_upload_req, $request->request_by . '-' . date('Y-m-d', time()) .
                    '-filereq', 'public/assets/files/req');
            }

            $category->updated_by = Auth::id();
            $category->updated_at = Carbon::now();
            $category->save();

            Track::newTrack($category->department_name, 'Form has been updated successfully !!');
            DB::commit();
            session()->flash('success', 'Form has been updated successfully !!');
            return redirect()->route('admin.categories.show', $category->id);
        } catch (\Exception $e) {
            session()->flash('sticky_error', $e->getMessage());
            DB::rollBack();
            return back();
        }
    }

    public function destroy($id)
    {
        if (!$this->user || !$this->user->can('category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::withTrashed()->findOrFail($id);

        if ($category->trashed()) {
            $category->forceDelete();
            session()->flash('success', 'Form permanently deleted.');
        } else {
            $category->delete();
            session()->flash('success', 'Form removed to view.');
        }

        return redirect()->route('admin.categories.index');
    }

    public function revertFromTrash($id)
    {
        if (is_null($this->user) || !$this->user->can('category.delete')) {
            $message = 'You are not allowed to access this page !';
            return view('errors.403', compact('message'));
        }

        $category = Category::find($id);
        if (is_null($category)) {
            session()->flash('error', "The page is not found !");
            return redirect()->route('admin.categories.trashed');
        }
        $category->deleted_at = null;
        $category->deleted_by = null;
        $category->save();

        session()->flash('success', 'Category has been revert back successfully !!');
        return redirect()->route('admin.categories.trashed');
    }

    public function destroyTrash($id)
    {
        if (is_null($this->user) || !$this->user->can('category.delete')) {
            $message = 'You are not allowed to access this page !';
            return view('errors.403', compact('message'));
        }
        $category = Category::find($id);
        if (is_null($category)) {
            session()->flash('error', "The page is not found !");
            return redirect()->route('admin.categories.trashed');
        }

        // Remove Images
        UploadHelper::deleteFile('public/assets/images/categorys/' . $category->banner_image);
        UploadHelper::deleteFile('public/assets/images/categorys/' . $category->image);

        // Delete Category permanently
        $category->delete();

        session()->flash('success', 'Category has been deleted permanently !!');
        return redirect()->route('admin.categories.trashed');
    }

    public function trashed()
    {
        if (is_null($this->user) || !$this->user->can('category.view')) {
            $message = 'You are not allowed to access this page !';
            return view('errors.403', compact('message'));
        }
        return $this->index(request(), true);
    }

    public function getDocNumber($site)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        $romanMonth = Category::toRoman($month);

        // Find the last record from the previous month
        $lastRecord = Category::where('doc_number', 'like', "%/{$site}/%")
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('doc_number', 'desc')
            ->first();

        // If there's no record for the current month, check the previous month
        if (!$lastRecord) {
            $previousMonth = Carbon::now()->subMonth();
            $previousYear = $previousMonth->year;
            $previousMonthFormat = $previousMonth->format('m');

            // Query the last record from the previous month
            $lastRecord = Category::where('doc_number', 'like', "%/{$site}/%")
                ->whereYear('created_at', $previousYear)
                ->whereMonth('created_at', $previousMonthFormat)
                ->orderBy('doc_number', 'desc')
                ->first();
        }

        // Determine the new number to use
        if ($lastRecord) {
            $lastNumber = (int)substr($lastRecord->doc_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        $docNumber = "{$year}/{$romanMonth}/{$site}/{$formattedNumber}";

        return response()->json(['docNumber' => $docNumber]);
    }

    public function approve($id)
    {
        $nikApp = Auth::user()->nik;
        $category = Category::findOrFail($id);
        $category->nikapp = $nikApp;
        $category->approve = 1;
        $category->save();

        return response()->json([
            'success' => "Employee request form approved successfully",
        ]);
    }

    public function approve2($id)
    {
        $nikApp = Auth::user()->nik;
        $category = Category::findOrFail($id);
        $category->nikapp2 = $nikApp;
        $category->save();

        return response()->json([
            'success' => "Employee request form approved successfully",
        ]);
    }

    public function reject($id)
    {
        $nikApp = Auth::user()->nik;
        $category = Category::findOrFail($id);
        if ($category->nikapp == null) {
            $category->nikapp = $nikApp;
        } else if ($category->nikapp != $nikApp) {
            $category->nikapp2 = $nikApp;
        }
        $category->approve = 0;
        $category->save();

        return response()->json([
            'success' => "Employee request form rejected successfully",
        ]);
    }
}
