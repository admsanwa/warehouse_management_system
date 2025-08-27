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

class ItemsController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
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
        $totalPages = ceil($getItems['total'] / $param['limit']);

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
            "WhsCode" =>  'BK001',
            "ItemName" => $request->get('item_desc'),
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
        ];
        $getRecord      = $this->sap->getStockItems($param);

        if (empty($getRecord) || $getRecord['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $totalPages = ceil($getRecord['total'] / $param['limit']);
        return view("api.items.list", [
            'getRecord'      => $getRecord['data'] ?? [],
            'page'        => $getRecord['page'],
            'limit'       => $getRecord['limit'],
            'total'       => $getRecord['total'],
            'totalPages'  => $totalPages,
            'stockNotes' => $request->get('stockNotes', 2),
            'defaultWh' => $param['WhsCode'],
            'stockStatus' => [
                0 => 'Semua',
                1 => 'Stock harus dibeli',
                2 => 'Stock tidak harus dibeli',
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

        public function warehouse_search(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            "WhsCode" => $request->get('q'),
            'page'       => 1,
        ];

        $get = $this->sap->getWarehouses($param);

        if (empty($get) || $get['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }

        $wh = collect($get['data'] ?? [])->map(function ($val) {
            return [
                'id'   => $val['WhsCode'],
                'text' => $val['WhsCode'] . " - " . $val['WhsName'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => $get
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
                'text' => $val['OcrCode'] . " - " . $val['OcrName'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => $get
        ]);
    }

        public function project_search(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            "PrjCode" => $request->get('q'),
            'Locked'       =>"N",
            'page'       => 1,
        ];

        $get = $this->sap->getProjects($param);

        if (empty($get) || $get['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }

        $wh = collect($get['data'] ?? [])->map(function ($val) {
            return [
                'id'   => $val['PrjCode'],
                'text' => $val['PrjCode'] . " - " . $val['PrjName'],
            ];
        });

        return response()->json([
            'results' => $wh,
            'api_response' => $get
        ]);
    }

}
