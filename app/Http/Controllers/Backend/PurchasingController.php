<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ItemsMaklonModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\StockModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\SapService;
use Illuminate\Support\Arr;
use Auth;

class PurchasingController extends Controller
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
            "DocStatus" => $request->get('docStatus', 'Open'),
            "DocNum" => $request->get('docNum'),
            "DocDueDate" => formatDateSlash($request->get('DocDueDate')),
            "CardName" =>  $request->get('cardName'),
            "DocDate" => formatDateSlash($request->get('docDate')),
            "Series" =>  $request->get('series')
        ];

        $orders = $this->sap->getPurchaseOrders($param);
        if (empty($orders) || $orders['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $currentCount = $orders['total'] ?? count($orders['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;


        return view('api.purchasing.list', [
            'orders'      => $orders['data'] ?? [],
            'page'        => $orders['page'],
            'limit'       => $orders['limit'],
            'total'       => $orders['total'],
            'totalPages'  => $totalPages,
            'statuses' => [
                'Open',
                'Close',
            ]
        ]);
    }

    public function view(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => 1,
            "DocNum" =>  $request->query('docNum'),
            "DocEntry" => $request->query('docEntry'),
        ];
        $orders = $this->sap->getPurchaseOrders($param);

        if (empty($orders) || !Arr::get($orders, 'success')) {
            return back()->with(
                'error',
                Arr::get($orders, 'message', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.')
            );
        }

        $po = Arr::get($orders, 'data.0', []);
        $lines = Arr::get($po, 'Lines', []);

        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $po['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);
        // dd($get_series);
        return view('api.purchasing.view', [
            'po'    => $po,
            'lines' => $lines,
            'series' => $series,
            'user' => Auth::user()
        ]);
    }

    public function po_search(Request $request)
    {
        $param = [
            "limit" => (int) $request->get('limit', 5),
            "DocStatus" => $request->get('status', 'Open'),
            "ItemCode" => $request->get('code'),
            "DocNum" => $request->get('q'),
            "DocEntry" => $request->get('docentry'),
            "Series" => $request->get('series'),
            'page'       => 1,
        ];

        $orders = $this->sap->getPurchaseOrders($param);

        if (empty($orders) || $orders['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }

        $poData = collect($orders['data'] ?? [])->map(function ($item) {
            return [
                'id'   => $item['DocEntry'],
                'docnum'   => $item['DocNum'],
                'text' => $item['DocNum'] . " - " . $item['CardName'],
            ];
        });

        return response()->json([
            'results' => $poData,
            'po' => $orders['data']
        ]);
    }

    public function series_search(Request $request)
    {
        $searchQuery = $request->get('q');

        $param = [
            'page'       => 1,
            'limit'      => 100,
            'Locked'     => 'N', // Terbuka
            'ObjectCode' => $request->get('ObjectCode'),
            'SeriesName' => $searchQuery,
            'Series' => $request->get('Series'),
        ];

        $getSeries = $this->sap->getSeries($param);

        if (empty($getSeries) || $getSeries['success'] !== true) {
            return response()->json([
                'results' => []
            ]);
        }

        $series = collect($getSeries['data'] ?? [])->map(function ($item) {
            return [
                'id'   => $item['Series'],
                'text' => $item['SeriesName'],
            ];
        });

        return response()->json([
            'results' => $series
        ]);
    }


    public function old_index(Request $request)
    {
        $getRecord      = PurchasingModel::with("po_details")->get()->values();
        $getRecordTwo   = PurchasingModel::with("maklon_details")->get()->values();
        // $getRecord      = PurchaseOrderDetailsModel::with("stocks")->get()->unique("nopo")->values();
        $getPagination  = PurchasingModel::getRecord($request);

        $purchasingSummary = [];
        $purchasingSummaryTwo = [];
        foreach ($getRecord as $record) {
            $po = $record->no_po;
            $purchaseQty = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $stockInQty = StockModel::where("no_po", $po)->sum("qty");

            $purchasingSummary[$po] = [
                'remain' => $purchaseQty - $stockInQty
            ];
        }
        foreach ($getRecordTwo as $record) {
            $po             = $record->no_po;
            $purchaseQty    = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $goodReceipt    = ItemsMaklonModel::where("po", $po)
                ->where(function ($query) {
                    $query->whereNotNull("gr")->where("gr", "<>", 0);
                })->sum("qty");

            $purchasingSummaryTwo[$po] = [
                'remain' => $purchaseQty - $goodReceipt
            ];
        }

        return view("backend.purchasing.list", compact('getRecord', 'getPagination', 'purchasingSummary', 'purchasingSummaryTwo'));
    }

    public function old_view(Request $request, $id)
    {
        $getRecord  = PurchasingModel::find($id);
        $getPO      = PurchasingModel::where("id", $id)->value("no_po");
        $getData    = PurchaseOrderDetailsModel::where("nopo", $getPO)->get();
        return view("backend.purchasing.view", compact('getRecord', 'getData', 'getPO'));
    }

    public function upload_form()
    {
        return view("backend.purchasing.upload");
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

                    PurchasingModel::create([
                        'no_po'           => $row[0],
                        'vendor'          => $row[1],
                        'contact_person'  => $row[2],
                        'buyer'           => $row[3],
                        'posting_date' => Carbon::createFromFormat('d.m.Y', $row[4])->format('Y-m-d'),
                        'status'          => $row[5],
                        'item_code'       => $row[6],
                        'item_type'       => $row[7],
                        'item_desc'       => $row[8],
                        'qty' => str_replace(',', '', $row[9]),
                        'uom'             => $row[10],
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

        return back()->with('success', "POs Imported Succesfully");
    }
}
