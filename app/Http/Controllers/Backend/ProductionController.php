<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BarcodeProductionModel;
use App\Models\BonDetailsModel;
use App\Models\BonModel;
use App\Models\DeliveryModel;
use App\Models\ItemsModel;
use App\Models\MemoModel;
use App\Models\PrepareMatModel;
use App\Models\ProductionModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\QualityModel;
use App\Models\SignBonModel;
use App\Models\RFPModel;
use App\Models\SignModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\SapService;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MailBonApproval;
use App\Notifications\MailBonFinal;
use App\Notifications\MailMemoFinal;
use App\Notifications\MailMemoApproval;
use Barryvdh\DomPDF\Facade\Pdf;


class ProductionController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function index(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 50),
            "DueDate" => formatDateSlash($request->get('date')),
            "DocNum" => $request->get('doc_num'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
            "Status" =>  $request->get('status', 'Released'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // Filter ulang kalau ada Series
        if (!empty($param['Series']) && !empty($getProds['data'])) {
            $getProds['data'] = collect($getProds['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            // update total sesuai hasil filter
            $getProds['total'] = count($getProds['data']);
        }

        // $totalPages = ceil($getProds['total'] / $param['limit']);
        $currentCount = $getProds['total'] ?? count($getProds['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view('api.production.list', [
            'getProds'      => $getProds['data'] ?? [],
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $getProds['total'],
            'totalPages'  => $totalPages,
        ]);
    }

    public function prod_search(Request $request)
    {
        $param = [
            "limit" => (int) $request->get('limit', 5),
            "Status" => $request->get('status', 'Released'),
            "DocNum" => $request->get('q'),
            "Series" => $request->get('series'),
            "DocEntry" => $request->get('docEntry'),
            "ItemCode" =>  $request->get('code'),
            "U_MEB_NO_IO" =>  $request->get('no_io'),
            'page'       => 1,
        ];

        $orders = $this->sap->getProductionOrders($param);

        if (empty($orders) || $orders['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }

        // Filter ulang kalau ada Series
        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            // update total sesuai hasil filter
            $orders['total'] = count($orders['data']);
        }

        $poData = collect($orders['data'] ?? [])->map(function ($item) {
            return [
                'id'   => $item['DocEntry'],
                'docnum'   => $item['DocNum'],
                'text' => $item['DocNum'] . " - " . $item['ItemCode'],
            ];
        });

        return response()->json([
            'results' => $poData,
            'prods' => $orders['data'],
        ]);
    }

    public function view(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 1),
            "DocNum" =>  $request->query('docNum'),
            "DocEntry" => $request->query('docEntry'),
        ];
        $prods = $this->sap->getProductionOrders($param);

        if (empty($prods) || !Arr::get($prods, 'success')) {
            return back()->with(
                'error',
                Arr::get($prods, 'message', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.')
            );
        }

        $prod = Arr::get($prods, 'data.0', []);
        $lines = Arr::get($prod, 'Lines', []);

        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $prod['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);

        $qualities = QualityModel::where('doc_entry', $prod['DocEntry'])
            ->orderByDesc('id')
            ->get()
            ->unique('prod_no')   // ambil yang terbaru per item
            ->keyBy('prod_no');
        $user = Auth::user();

        $rfp = RFPModel::where(function ($q) use ($prod) {
            $q->where('base_entry', $prod['DocEntry'])
                ->orWhere('prod_order', $prod['DocNum']);
        })
            ->where('prod_no', $prod['ItemCode'])
            ->orderByDesc('id')
            ->get();

        $totalRejectQty = $rfp->sum('rjct_qty');
        return view('api.production.view', [
            'getRecord'    => $prod,
            'lines' => $lines,
            'series' => $series,
            'qualities' => $qualities,
            'rfp'        => $rfp,
            'totalRejectQty'        => $totalRejectQty,
            'user'  => $user
        ]);
    }

    public function index_old(Request $request)
    {
        $getData    = ProductionModel::withCount("stocks")->orderBy("id", "desc")->paginate(10);
        $getRecord  = ProductionOrderDetailsModel::with("stocks")->get()->unique("doc_num")->values();

        $productionSummary = [];
        foreach ($getRecord as $record) {
            $po = $record->doc_num;

            $productionQty  = ProductionOrderDetailsModel::where("doc_num", $po)->sum("qty");
            $stockOutQty    = StockModel::where("prod_order", $po)->sum("qty");

            $productionSummary[$po] = [
                "remain" => $productionQty - $stockOutQty
            ];
        }
        return view('backend.production.list', compact('getData', 'productionSummary', 'getRecord'));
    }

    public function view_old(Request $request, $id)
    {
        $getRecord   = ProductionModel::find($id);
        $getDocnum   = ProductionModel::where("id", $id)->value("doc_num");
        $getData     = ProductionOrderDetailsModel::where("doc_num", $getDocnum)->get();

        // dd($getData);
        return view('backend.production.view', compact("getRecord", "getData"));
    }

    public function upload_form()
    {
        return view('backend.production.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $path = $request->file('file')->storeAs('uploads', $request->file('file')->getClientOriginalName());
        $fullPath = storage_path('app/' . $path);
        $data = array_map('str_getcsv', file($fullPath));

        // dd($data);
        // dd(DB::connection()->getDatabaseName());
        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                if ($index === 0 && is_string($row[0])) {
                    continue;
                }

                try {
                    if (count($row) < 11) {
                        throw new \Exception("Row $index has insufficient columns.");
                    }

                    ProductionModel::create([
                        'prod_no'  => Carbon::createFromFormat('d.m.Y', $row[1])->format('Y-m-d'),
                        'prod_desc' => $row[1],
                        'remarks'   => $row[2],
                        'doc_num'   => $row[3],
                        'io_no'     => $row[4],
                        'due_date'  => $row[5],
                        'item_code' => $row[6],
                        'item_type' => $row[7],
                        'item_desc' => $row[8],
                        'qty'       => $row[9],
                        'uom'       => $row[10],
                        'status'    => $row[11]
                    ]);
                } catch (\Exception $e) {
                    dd("Error in row $index:", $row, $e->getMessage());
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upload failed at row $index: " . $e->getMessage());
            Log::error($e); // logs stack trace too
            return back()->with('error', "Upload failed at row $index. " . $e->getMessage());
        }

        return back()->with('success', "Productions Imported Succesfully");
    }

    public function view_prod($prod)
    {
        $prod = ProductionModel::where("prod_no", $prod)->value("id");
        return redirect("admin/production/view/" . $prod);
    }

    // memo
    public function memo(Request $request)
    {
        $number = MemoModel::generateNumber();
        return view('backend.production.memo', compact('number'));
    }

    public function create_memo(Request $request)
    {
        $validated = $request->validate([
            'no'        => 'required|string',
            'date'      => 'required|date',
            'io'        => 'required|string',
            'duedate'   => 'required|date',
            'needs'     => 'required|array',
            'needs.*'   => 'required|string',
            'unit'      => 'nullable|array',
            'unit.*'    => 'nullable|string',
            'width'     => 'nullable|array',
            'width.*'   => 'nullable|numeric',
            'height'    => 'nullable|array',
            'height.*'  => 'nullable|numeric',
            'qty'       => 'nullable|array',
            'qty.*'     => 'nullable|numeric',
            'uom'       => 'nullable|array',
            'uom.*'     => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $user = Auth::user();
            $memo = \App\Models\MemoModel::create([
                'created_by'    => $user->fullname,
                'no'            => $validated['no'],
                'date'          => $validated['date'],
                'description'   => $request->description,
                'project'       => $request->project,
                'io'            => $validated['io'],
                'due_date'      => $validated['duedate'],
            ]);

            foreach ($validated['needs'] as $index => $need) {
                \App\Models\MemoDetailModel::create([
                    'memo_id'   => $memo->id,
                    'needs'     => $need,
                    'unit'      => $validated['unit'][$index] ?? '-',
                    'width'     => $validated['width'][$index] ?? 0,
                    'height'    => $validated['height'][$index] ?? 0,
                    'qty'       => $validated['qty'][$index] ?? 0,
                    'uom'       => $validated['uom'][$index] ?? '-',
                ]);
            }
        });
        // dd($request->all());
        $recipients = User::where('department', 'Procurement, Installation and Delivery')
            ->where('level', 'Manager')
            ->get();
        $dev_users = User::where('department', 'IT')->get();

        $recipients = $recipients->merge($dev_users);
        Notification::send($recipients, new MailMemoApproval(
            $validated['no'],
            url('admin/production/listmemo')
        ));
        return redirect()->back()->with('success', "Successfully create memo {$request->description}");
    }

    public function list_memo(Request $request)
    {
        $getRecord = MemoModel::getRecord($request);
        $user      = Auth::user();
        foreach ($getRecord as $record) {
            $signApprove    = SignModel::where('no_memo', $record->no)->where('sign', 1)->first();
            $signProd       = SignModel::where('no_memo', $record->no)->where('sign', 1)->where('department', 'Production and Warehouse')
                ->whereHas('user', function ($q) {
                    $q->where('department', 'Production and Warehouse');
                })
                ->first();;

            $record->status = ($signApprove)
                ? 'Approve'
                : 'Pending';

            $record->highlight = ($user->nik === "06067" && !$signApprove || $user->nik === "05993" && !$signProd);
        }

        return view('backend.production.listmemo', compact('getRecord'));
    }

    public function detail_memo(Request $request, $id)
    {
        $memo           = MemoModel::with(['details', 'createdBy'])->findOrFail($id);
        $user           = Auth::user();
        $signApprove    = SignModel::with('user')->where('no_memo', $memo->no)->where('sign', 1)->first();
        $signProd       = SignModel::with('user')->where('no_memo', $memo->no)->where('sign', 1)->where('department', 'Production and Warehouse')
            ->whereHas('user', function ($q) {
                $q->where('department', 'Production and Warehouse');
            })
            ->first();;

        // dd($getSign);
        return view('backend.production.showmemo', compact('memo', 'user', 'signApprove', 'signProd'));
    }

    public function approve(Request $request)
    {
        $no     = $request->input("no_memo");
        $user   = Auth::user();
        $recipients = User::where("department", "Production and Warehouse")->get();

        $sign               = new SignModel();
        $sign->no_memo      = $no;
        $sign->nik = $user->nik;
        $sign->department   = $user->department;
        $sign->sign         = 1;
        $sign->save();

        $dev_users = User::where('department', 'IT')->get();
        // merge collections
        $recipients = $recipients->merge($dev_users);

        Notification::send($recipients, new MailMemoFinal(
            $no,
            url('admin/production/listmemo')
        ));
        return response()->json([
            'success' => true,
            'message' => "Successfully approve to production"
        ]);
    }

    public function barcode(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            "U_MEB_NO_IO" => $request->get('io'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Status" =>  $request->get('status', 'Released'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }
        $user          = Auth::user()->username;
        $addedBarcodes = BarcodeProductionModel::where('username', $user)->latest()->take(5)->get();
        // $totalPages = ceil($getProds['total'] / $param['limit']);
        $currentCount = $getProds['total'] ?? count($getProds['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view("api.production.barcode",  [
            'prods'      => $getProds['data'] ?? [],
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $getProds['total'],
            'totalPages'  => $totalPages,
            'addedBarcodes'  => $addedBarcodes,
        ]);
    }

    public function barcode_old(Request $request)
    {
        $getRecord     = ProductionModel::where("status", "Released")->paginate(10);
        $user          = Auth::user()->username;
        $addedBarcodes = BarcodeProductionModel::where('username', $user)->latest()->take(5)->get();

        // dd($getProd);
        return view("backend.production.barcode", compact("getRecord", "addedBarcodes"));
    }

    public function add_print(Request $request)
    {
        // dd($request->all()); // Check what data is being sent
        $user       = Auth::user()->username;
        $barcode    = request()->validate([
            'prod_no'       => 'required|string',
            'prod_desc'     => 'required|string',
            'qty'           => 'required|numeric|min:1',
        ]);

        $barcode            = new BarcodeProductionModel();
        $barcode->prod_no   = trim($request->prod_no);
        $barcode->prod_desc = trim($request->prod_desc);
        $barcode->qty       = trim($request->qty);
        $barcode->duedate   = trim($request->duedate);
        $barcode->username  = $user;
        $barcode->save();
        // dd($barcode);

        return redirect('admin/production/barcode')->with('success', "Succesfully add barcode {$barcode->prod_no}");
    }

    public function print(Request $request)
    {
        $user            = Auth::user()->username;
        $addedBarcodes   = BarcodeProductionModel::where("username", $user)->get();

        if (empty($addedBarcodes)) {
            return redirect()->back()->with("error", "Tidak ada barcode untuk print yang dipilih");
        }

        // dd($addedBarcodes);

        return view("backend.production.print", compact("addedBarcodes"));
    }

    public function printBarcodeWithPdf(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = BarcodeProductionModel::where("username", $user)->get();

        if ($addedBarcodes->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        $pdf = Pdf::loadView('backend.production.pdf', compact('addedBarcodes'))
            ->setPaper([0, 0, 121.88, 70.86]); // 43mm x 25mm

        return $pdf->stream('barcodes.pdf',);
    }

    public function delete($id)
    {
        $recordDelete = BarcodeProductionModel::find($id);
        $recordDelete->delete();

        return redirect()->back()->with("error", "Barcode print berhasil dihapus");
    }

    public function deleteall()
    {
        $user = Auth::user()->username;
        $recordDelete = BarcodeProductionModel::where("username", $user);
        $recordDelete->delete();

        return redirect()->back()->with("error", "Berhasil hapus semua barcode print");
    }

    // bon
    public function bon()
    {
        $user   = Auth::user();
        $number = BonModel::generateNumber();

        return view('backend.production.bon', compact('number', 'user'));
    }

    public function create_bon(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'type'            => 'required|string',
            'no'            => 'required|string',
            'date'          => 'required|date',
            'section'       => 'required',
            'io'            => 'nullable|string',
            'project'       => 'nullable|string',
            'make_to'       => 'nullable|string',
            'item_code'     => 'required|array',
            'item_code.*'   => 'required|string',
            'item_desc'     => 'nullable|array',
            'item_desc.*'   => 'nullable|string',
            'qty'           => 'required|array',
            'qty.*'         => 'required|numeric',
            'uom'           => 'nullable|array',
            'uom.*'         => 'nullable|string',
            'remark'        => 'nullable|array',
            'remark.*'      => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated) {
            $user = Auth::user();
            $bon = BonModel::create([
                'type'            => $validated['type'],
                'no'            => $validated['no'],
                'date'          => $validated['date'],
                'section'       => $validated['section'],
                'io'            => $validated['io'] ?? '-',
                'project'       => $validated['project'] ?? '-',
                'make_to'       => $validated['make_to'],
                'created_by'    => $user->fullname,
            ]);

            foreach ($validated['item_code'] as $index => $item) {
                // $items = ItemsModel::where('code', $item)->first();
                BonDetailsModel::create([
                    'bon_id'    => $bon->id,
                    'item_code' => $validated['item_code'][$index],
                    'item_name' => $validated['item_desc'][$index] ?? '-',
                    'qty'       => $validated['qty'][$index] ?? 0,
                    'uom'       => $validated['uom'][$index] ?? '-',
                    'remark'    => $validated['remark'][$index] ?? '-',
                ]);
            }
        });
        $recipients = User::where('department', 'Procurement, Installation and Delivery')
            ->where('level', 'Manager')
            ->get();
        $dev_users = User::where('department', 'IT')->get();

        // merge collections
        $recipients = $recipients->merge($dev_users);
        Notification::send($recipients, new MailBonApproval(
            $validated['no'],
            url('admin/production/listbon')
        ));

        return redirect()->back()->with('success', "Successfully create bon {$request->no}");
    }

    public function list_bon(Request $request)
    {
        $getRecord   = BonModel::getRecord($request);
        $user       = Auth::user();

        foreach ($getRecord as $record) {
            $signApprove    = SignBonModel::where('no_bon', $record->no)->where('sign', 1)->first();
            $signBuyer      = SignBonModel::where('no_bon', $record->no)->where('sign', 1)->where('department', 'Purchasing')
                ->whereHas('user', function ($q) {
                    $q->where('department', 'Purchasing');
                })
                ->first();;

            $record->status = ($signApprove && $signBuyer)
                ? 'Approve'
                : 'Pending';

            $record->highlight = ($user->nik === "06067" && !$signApprove || $user->nik === "08517" && !$signBuyer || $user->nik === "250071" && !$signBuyer);
        }

        return view('backend.production.listbon', compact('getRecord', 'user'));
    }

    public function bon_details($id)
    {
        $bon            = BonModel::with(['details', 'createdBy'])->findOrFail($id);
        $user           = Auth::user();
        $signApprove    = SignBonModel::with('user')->where('no_bon', $bon->no)->where('sign', 1)->where('department', 'Procurement, Installation and Delivery')->first();
        $signBuyer      = SignBonModel::with('user')->where('no_bon', $bon->no)->where('sign', 1)->where('department', 'Purchasing')
            ->whereHas('user', function ($q) {
                $q->where('department', 'Purchasing');
            })
            ->first();;

        return view("backend.production.showbon", compact('bon', 'user', 'signApprove', 'signBuyer'));
    }

    public function approve_bon(Request $request)
    {
        $no   = $request->input("no_bon");
        $user = Auth::user();

        // Simpan sign
        $sign               = new SignBonModel();
        $sign->no_bon       = $no;
        $sign->nik          = $user->nik;
        $sign->department   = $user->department;
        $sign->sign         = 1;
        $sign->save();

        $bon = BonModel::where("no", $no)->first();

        $recipients = collect();

        if ($bon && $bon->type) {
            if ($bon->type === "Lokal") {
                $recipients = collect([new \App\Models\User([
                    "email" => "purchasing_bks@sanwamas.co.id"
                ])]);
            } elseif ($bon->type === "Import") {
                $recipients = collect([new \App\Models\User([
                    "email" => "purchasing_staff@sanwamas.co.id"
                ])]);
            }
        }

        if ($recipients->isEmpty()) {
            $recipients = User::where("department", "Purchasing")->get();
        }

        $dev_users = User::where('department', 'IT')->get();
        $recipients = $recipients->merge($dev_users);

        Notification::send($recipients, new MailBonFinal(
            $no,
            url('admin/production/listbon')
        ));

        return response()->json([
            'success' => true,
            'message' => 'PO Approve successfully',
        ]);
    }

    public function insert_po($id, Request $request)
    {
        $request->validate([
            'po' => 'required|numeric',
            'no_bon' => 'required',
            'series' => 'required'
        ]);
        $user = Auth::user();

        // Simpan sign
        $sign               = new SignBonModel();
        $sign->no_bon       = $request->no_bon;
        $sign->nik          = $user->nik;
        $sign->department   = $user->department;
        $sign->sign         = 1;
        $sign->save();

        $bon = BonModel::where("no", $request->no_bon)->first();
        BonModel::where('id', $id)->update(['no_po' => $request->po, 'no_series' => $request->series]);

        $recipients = collect();

        if ($bon && $bon->type) {
            if ($bon->type === "Lokal") {
                $recipients = collect([new \App\Models\User([
                    "email" => "purchasing_bks@sanwamas.co.id"
                ])]);
            } elseif ($bon->type === "Import") {
                $recipients = collect([new \App\Models\User([
                    "email" => "purchasing_staff@sanwamas.co.id"
                ])]);
            }
        }

        if ($recipients->isEmpty()) {
            $recipients = User::where("department", "Purchasing")->get();
        }

        $dev_users = User::where('department', 'IT')->get();
        $recipients = $recipients->merge($dev_users);

        Notification::send($recipients, new MailBonFinal(
            $request->no_bon,
            url('admin/production/listbon')
        ));

        return response()->json([
            'success' => true,
            'message' => 'Approve Bon successfully',
        ]);
    }

    public function preparemat_form(Request $request)
    {
        $param = [
            "DocNum" =>  $request->query('docNum'),
            "DocEntry" => $request->query('docEntry'),
        ];

        $prods = $this->sap->getProductionOrders($param);

        if (empty($prods) || !Arr::get($prods, 'success')) {
            return back()->with(
                'error',
                Arr::get($prods, 'message', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.')
            );
        }

        $prod = Arr::get($prods, 'data.0', []);
        $lines = Arr::get($prod, 'Lines', []);

        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $prod['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);

        $qualities = QualityModel::where('doc_entry', $prod['DocEntry'])
            ->orderByDesc('id')
            ->get()
            ->unique('prod_no')   // ambil yang terbaru per item
            ->keyBy('prod_no');
        $user = Auth::user();

        return view('backend.production.prepareform', [
            'getRecord'    => $prod,
            'lines' => $lines,
            'series' => $series,
            'qualities' => $qualities,
            'user'  => $user
        ]);
    }

    public function create_preparemat(Request $request)
    {
        // dd($request->all());
        // dd('controller hit');

        try {
            $validated = $request->validate([
                'DocEntry'  => 'required|integer',
                'U_MEB_NO_IO'   => 'required|string',
                'Series'    => 'required|string',
                'DocNum'    => 'required|integer',
                'ItemCode'  => 'required|string',
                'lines'     => 'required|array',
                'lines.*.ItemCode'  => 'required|string',
                'lines.*.PrepareQty' => 'nullable|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }

        // dd('Validation passed', $validated);

        DB::transaction(function () use ($validated) {
            foreach ($validated['lines'] as $line) {
                if (in_array($line['ItemCode'], ['Z-DL', 'Z-FOH'])) {
                    continue;
                }
                PrepareMatModel::create([
                    'doc_entry' => $validated['DocEntry'],
                    'io'        => $validated['U_MEB_NO_IO'],
                    'series'    => $validated['Series'],
                    'doc_num'   => $validated['DocNum'],
                    'prod_no'   => $validated['ItemCode'],
                    'item_code' => $line['ItemCode'],
                    'prepare_qty'   => $line['PrepareQty'],
                    'status'    => 0
                ]);

                // dd($insert);
            }
        });

        return redirect('/listpreparemat')->with('success', "Succesfully create prepare material data");
    }

    public function list_preparemat(Request $request)
    {
        $getRecord  = PrepareMatModel::getRecord($request);
        $seriesList = PrepareMatModel::select("series")
            ->whereNotNull("series")
            ->distinct()
            ->orderBy("series", "asc")
            ->get();

        return view("backend.production.listpreparemat", compact("getRecord", "seriesList"));
    }

    public function preparemat_details($docEntry)
    {
        $param = [
            "DocEntry" => $docEntry,
        ];

        $prods = $this->sap->getProductionOrders($param);

        if (empty($prods) || !Arr::get($prods, 'success')) {
            return back()->with(
                'error',
                Arr::get($prods, 'message', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.')
            );
        }

        $prod = Arr::get($prods, 'data.0', []);
        $lines = Arr::get($prod, 'Lines', []);
        $preparemat = PrepareMatModel::where('doc_entry', $param)
            ->get()
            ->keyBy('item_code');


        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $prod['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);

        $user = Auth::user();
        return view('backend.production.preparematdetails', [
            'getRecord'    => $prod,
            'lines' => $lines,
            'series' => $series,
            'preparemat' => $preparemat,
            'user'  => $user
        ]);
    }

    public function update_preparemat($docEntry)
    {
        $user = Auth::user();

        if ($user->department == "Production and Warehouse" && $user->level == "Leader") {
            PrepareMatModel::where('doc_entry', $docEntry)->update(['status' => 1]);
        } else if ($user->department == "Production" && $user->level == "Staff") {
            PrepareMatModel::where('doc_entry', $docEntry)->update(['status' => 2]);
        } else if ($user->department == "Quality Control" && $user->level == "Staff") {
            PrepareMatModel::where('doc_entry', $docEntry)->update(['status' => 3]);
        }

        return redirect('/listpreparemat')->with('success', "Succesfully transfer prepare material data");
    }

    public function so_search(Request $request)
    {
        try {

            $param = [
                "limit" => (int) $request->get('limit', 5),
                "Status" => $request->get('DocStatus', 'Open'),
                "Series" => $request->get('series'),
                'page' => 1,
            ];

            $salesOrders = $this->sap->getSalesOrders($param);

            if (empty($salesOrders) || $salesOrders['success'] !== true) {
                return response()->json([
                    'results' => []
                ]);
            }

            // Filter ulang kalau ada Series
            if (!empty($param['Series'])) {
                $salesOrders['data'] = collect($salesOrders['data'])
                    ->where('Series', $param['Series'])
                    ->values()
                    ->all();

                // update total sesuai hasil filter
                $salesOrders['total'] = count($salesOrders['data']);
            }

            $soData = collect($salesOrders['data'] ?? [])->map(function ($item) {
                return [
                    'id'   => $item['DocEntry'],
                    'text'   => $item['U_MEB_NO_IO'],
                    'CardName' => $item['CardName'],
                ];
            });

            return response()->json([
                'results' => $soData,
                'prods' => $salesOrders['data'] ?? [],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error in so_search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
