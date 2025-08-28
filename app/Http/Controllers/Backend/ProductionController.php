<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BarcodeProductionModel;
use App\Models\BonDetailsModel;
use App\Models\BonModel;
use App\Models\ItemsModel;
use App\Models\MemoModel;
use App\Models\ProductionModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\SignBonModel;
use App\Models\SignModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\SapService;
use Illuminate\Support\Arr;

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
            "limit" => (int) $request->get('limit', 10),
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

        $totalPages = ceil($getProds['total'] / $param['limit']);

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
            "DocEntry" => $request->get('docentry') ?? '',
            'page'       => 1,
        ];

        $orders = $this->sap->getProductionOrders($param);

        if (empty($orders) || $orders['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }
        $poData = collect($orders['data'] ?? [])->map(function ($item) {
            return [
                'id'   => $item['DocNum'],
                'entry'   => $item['DocEntry'],
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

        return view('api.production.view', [
            'getRecord'    => $prod,
            'lines' => $lines,
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
            $memo = \App\Models\MemoModel::create([
                'no'            => $validated['no'],
                'date'          => $validated['date'],
                'description'   => $request->description,
                'project'       => $request->project,
                'io'            => $validated['io'],
                'due_date'      => $validated['duedate'],
            ]);

            foreach ($validated['needs'] as $index => $need) {
                \App\Models\memoDetailModel::create([
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

        $sign               = new SignModel();
        $sign->no_memo      = $no;
        $sign->nik = $user->nik;
        $sign->department   = $user->department;
        $sign->sign         = 1;
        $sign->save();

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
        $totalPages = ceil($getProds['total'] / $param['limit']);
        // dd($getProd);
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
        $items  = ItemsModel::all();
        return view('backend.production.bon', compact('number', 'user', 'items'));
    }

    public function create_bon(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'no'            => 'required|string',
            'date'          => 'required|date',
            'section'       => 'required',
            'io'            => 'nullable|string',
            'project'       => 'nullable|string',
            'make_to'       => 'nullable|string',
            'item_code'     => 'required|array',
            'item_code.*'   => 'required|string',
            'qty'           => 'required|array',
            'qty.*'         => 'required|numeric',
            'uom'           => 'required|array',
            'uom.*'         => 'required|string',
            'remark'        => 'nullable|array',
            'remark.*'      => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated) {
            $user = Auth::user();
            $bon = BonModel::create([
                'no'            => $validated['no'],
                'date'          => $validated['date'],
                'section'       => $validated['section'],
                'io'            => $validated['io'] ?? '-',
                'project'       => $validated['project'] ?? '-',
                'make_to'       => $validated['make_to'],
                'created_by'    => $user->fullname,
            ]);

            foreach ($validated['item_code'] as $index => $item) {
                $items = ItemsModel::where('code', $item)->first();
                BonDetailsModel::create([
                    'bon_id'    => $bon->id,
                    'item_code' => $items->code ?? '-',
                    'item_name' => $items->name ?? $item,
                    'qty'       => $validated['qty'][$index] ?? 0,
                    'uom'       => $validated['uom'][$index] ?? '-',
                    'remark'    => $validated['remark'][$index] ?? '-',
                ]);
            }
        });
        // dd($request->all());

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
        $signApprove    = SignBonModel::with('user')->where('no_bon', $bon->no)->where('sign', 1)->first();
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

        $sign               = new SignBonModel();
        $sign->no_bon       = $no;
        $sign->nik          = $user->nik;
        $sign->department   = $user->department;
        $sign->sign         = 1;
        $sign->save();

        return response()->json([
            'success' => true,
            'message' => "Successfully approve bon"
        ]);
    }
}
