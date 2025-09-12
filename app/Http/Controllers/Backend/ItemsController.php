<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarcodeModel;
use App\Models\ItemsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\SapService;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemsController extends Controller
{
    protected $sap;
    protected $default_warehouse;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
        $this->middleware(function ($request, $next) {
            $this->default_warehouse = Auth::user()->warehouse_access;
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $param = [
            'ItemCode' => $request->get('code'),
            "ItemName" => $request->get('name'),
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
        ];
        $user           = Auth::user()->username;
        $getItems      = $this->sap->getItems($param);
        $addedBarcodes  = BarcodeModel::where('username', $user)->latest()->take(5)->get();

        $currentCount = $getItems['total'] ?? count($getItems['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view("api.items.barcode", [
            'items'      => $getItems['data'] ?? [],
            'page'        => $getItems['page'],
            'limit'       => $getItems['limit'],
            'total'       => $getItems['total'],
            'totalPages'  => $totalPages,
            'addedBarcodes'  => $addedBarcodes,
        ]);
    }

    public function index_old(Request $request)
    {
        $user           = Auth::user()->username;
        $getRecord      = ItemsModel::getRecord($request);
        $addedBarcodes  = BarcodeModel::where('username', $user)->latest()->take(5)->get();

        return view("backend.items.barcode", compact('getRecord', 'addedBarcodes'));
    }

    public function print(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = BarcodeModel::where("username", $user)->get();

        if (empty($addedBarcodes)) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        return view('backend.items.print', compact('addedBarcodes'));
    }

    public function print_ppic(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = BarcodeModel::where("username", $user)->get();

        if (empty($addedBarcodes)) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        return view('backend.items.printppic', compact('addedBarcodes'));
    }

    public function post(Request $request)
    {
        $user   = Auth::user()->username;
        $barcode = request()->validate([
            'code'  => 'required|string',
            'name'  => 'required|string',
            'qty'   => 'required|numeric|min:1',
        ]);

        $barcode        = new BarcodeModel();
        $barcode->code  = trim($request->code);
        $barcode->name  = trim($request->name);
        $barcode->username = $user;
        $barcode->qty   = trim($request->qty);
        $barcode->save();

        return redirect('admin/items/barcode')->with('success', "Succesfully add barcode {$barcode->name}");
    }

    public function delete($id)
    {
        $recordDelete = BarcodeModel::find($id);
        $recordDelete->delete();
        // dd($recordDelete);

        return redirect()->back()->with('error', 'Barcodes successfully delete');
    }

    public function deleteall()
    {
        $user = Auth::user()->username;
        $recordDelete = BarcodeModel::where("username", $user);
        $recordDelete->delete();

        return redirect()->back()->with('error', 'All Barcodes succesfully delete');
    }

    public function add()
    {
        return view("backend.items.add");
    }

    public function post_item(Request $request)
    {
        // dd($request->all());
        $items = request()->validate([
            'posting_date'  => 'required',
            'code'          => 'required|string',
            'name'          => 'required|string'
        ]);

        $items      = new ItemsModel();
        $items->posting_date    = trim($request->posting_date);
        $items->code            = trim($request->code);
        $items->name            = trim($request->name);
        $items->group           = trim($request->group);
        $items->uom             = trim($request->uom);
        $items->stock_min       = trim($request->stock_min);
        $items->stock_max       = trim($request->stock_max);
        $items->save();

        return redirect("admin/items/additem")->with('success', "Successfully add items {$items->name}");
    }

    public function list(Request $request)
    {
        $param = [
            'ItemCode' => $request->get('item_code'),
            "ItemName" => $request->get('item_desc'),
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            'Status' => (int) $request->get('stockNotes'),
            'WhsCode' => $request->get('warehouse', $this->default_warehouse)
        ];
        $getRecord      = $this->sap->getStockItems($param);

        if (empty($getRecord) || $getRecord['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $currentCount = $getRecord['total'] ?? count($getRecord['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        return view("api.items.list", [
            'getRecord'      => $getRecord['data'] ?? [],
            'page'        => $getRecord['page'],
            'limit'       => $getRecord['limit'],
            'total'       => $getRecord['total'],
            'totalPages'  => $totalPages,
            'stockNotes' => $request->get('stockNotes', ''),
            'defaultWh' => $param['WhsCode'],
            'stockStatus' => [
                '' => 'Semua',
                0 => 'Stock tidak harus dibeli',
                1 => 'Stock harus dibeli',
            ]
        ]);
    }

    public function list_old(Request $request)
    {
        $getRecord = ItemsModel::getRecord($request);
        return view("backend.items.list", compact('getRecord'));
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

                    ItemsModel::create([
                        'posting_date'  => Carbon::createFromFormat('d.m.Y', $row[1])->format('Y-m-d'),
                        'code'          => $row[1],
                        'name'          => $row[2],
                        'group'         => $row[3],
                        'uom'           => $row[4],
                        'stock_min'     => $row[5],
                        'stock_max'     => $row[6],
                        'is_active'     => $row[7],
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

        return back()->with('success', "Items Imported Succesfully");
    }

    public function onhand_search(Request $request)
    {
        $q = $request->get('q');
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);

        $results = collect();

        // 1. Cari berdasarkan ItemCode
        $paramCode = [
            "page" => $page,
            "limit" => $limit,
            'WhsCode' => $this->default_warehouse,
            "ItemCode" => $q,
        ];
        $getCode = $this->sap->getStockItems($paramCode);

        if (!empty($getCode) && $getCode['success'] === true) {
            $results = $results->merge($getCode['data']);
        }

        // 2. Cari berdasarkan ItemName
        $paramName = [
            "page" => $page,
            "limit" => $limit,
            'WhsCode' => $this->default_warehouse,
            "ItemName" => $q,
        ];
        $getName = $this->sap->getStockItems($paramName);

        if (!empty($getName) && $getName['success'] === true) {
            $results = $results->merge($getName['data']);
        }

        $results = $results->unique('ItemCode')->values();

        $wh = $results->map(function ($val) {
            return [
                'id'        => $val['ItemCode'],
                'text'      => $val['ItemCode'] . " - " . $val['ItemName'],
                'uom'       => $val['InvntryUom'],
                'item_desc' => $val['ItemName'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => [
                'code' => $getCode,
                'name' => $getName,
            ]
        ]);
    }


    public function warehouse_search(Request $request)
    {
        $q = $request->get('q');
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        $results = collect();
        // 1. Cari berdasarkan WhsCode
        $paramCode = [
            "page" => $page,
            "limit" => $limit,
            "WhsCode" => $q,
        ];
        $getCode = $this->sap->getWarehouses($paramCode);

        if (!empty($getCode) && $getCode['success'] === true) {
            $results = $results->merge($getCode['data']);
        }

        // 2. Cari berdasarkan WhsName
        $paramName = [
            "page" => $page,
            "limit" => $limit,
            "WhsName" => $q,
        ];
        $getName = $this->sap->getWarehouses($paramName);

        if (!empty($getName) && $getName['success'] === true) {
            $results = $results->merge($getName['data']);
        }

        $results = $results->unique('WhsCode')->values();

        $wh = $results->map(function ($val) {
            return [
                'id'   => $val['WhsCode'],
                'text' => $val['WhsCode'] . ' ' . $val['WhsName'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => $results
        ]);
    }

    public function cost_center_search(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            "OcrCode" => $request->get('q'),
            'page'       => 1,
        ];

        $get = $this->sap->getCostCenters($param);

        if (empty($get) || $get['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }

        $wh = collect($get['data'] ?? [])->map(function ($val) {
            return [
                'id'   => $val['OcrCode'],
                'text' => $val['OcrCode'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => $get
        ]);
    }

    public function project_search(Request $request)
    {
        $q = $request->get('q');
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        $results = collect();

        // 1. Cari berdasarkan PrjCode
        $paramCode = [
            "page" => $page,
            "limit" => $limit,
            "PrjCode" => $q,
            'Locked' => "N",
        ];
        $getCode = $this->sap->getProjects($paramCode);

        if (!empty($getCode) && $getCode['success'] === true) {
            $results = $results->merge($getCode['data']);
        }

        // 2. Cari berdasarkan PrjName
        $paramName = [
            "page" => $page,
            "limit" => $limit,
            "PrjName" => $q,
            'Locked' => "N",
        ];
        $getName = $this->sap->getProjects($paramName);

        if (!empty($getName) && $getName['success'] === true) {
            $results = $results->merge($getName['data']);
        }

        // Gabungkan hasil dan hapus duplikat berdasarkan 'PrjCode'
        $results = $results->unique('PrjCode')->values();

        $wh = $results->map(function ($val) {
            return [
                'id' => $val['PrjCode'],
                'text' => $val['PrjCode'] . ' - ' . $val['PrjName'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => $results
        ]);
    }
    public function printBarcodeWithPdf(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = BarcodeModel::where("username", $user)->get();

        if ($addedBarcodes->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        $pdf = Pdf::loadView('backend.items.pdf', compact('addedBarcodes'))
            ->setPaper([0, 0, 283.465, 107.48]); // 100mm x 40mm

        return $pdf->stream('barcodes.pdf');
    }
}
