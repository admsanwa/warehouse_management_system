<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarcodeModel;
use App\Models\ItemsMaklonModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\StockModel;
use App\Models\UmebApproveModel;
use App\Models\UmebKnowingModel;
use App\Models\BuyerModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\SapService;
use Illuminate\Support\Arr;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;

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

        // Jika user set filter Series, pastikan data difilter ulang
        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            // Rehitung total
            $orders['total'] = count($orders['data']);
        }

        $currentCount = $orders['total'] ?? count($orders['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view('api.purchasing.list', [
            'orders'      => $orders['data'] ?? [],
            'page'        => $orders['page'],
            'limit'       => $orders['limit'],
            'total'       => $orders['total'],
            'totalPages'  => $totalPages,
            'user'        => Auth::user(),
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

        //dapatkan nama series dari api
        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $po['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);
        $buyer = BuyerModel::where('code', $po['SlpCode'])->first();

        if ($buyer) {
            $buyerName = $buyer->code . '-' . $buyer->name;
        } else {
            $buyerName = 'Unknown Buyer Code: ' . $po['SlpCode'];
        }
        $approveName = UmebApproveModel::where('id', $po['U_MEB_Approved_by'])->value('name') ?? 'Unknown Approver';
        $knowingName = UmebKnowingModel::where('id', $po['U_MEB_Knowing_by'])->value('name') ?? 'Unknown Knowing Person';

        return view('api.purchasing.view', [
            'po'    => $po,
            'lines' => $lines,
            'series' => $series,
            'buyer'   => $buyerName,
            'approve_by' => $approveName,
            'knowing_by' => $knowingName,
            'user' => Auth::user()
        ]);
    }

    public function po_search(Request $request)
    {
        $param = [
            "limit" => (int) $request->get('limit', 5),
            "DocStatus" => $request->get('status', 'Open'),
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
        // Jika user set filter Series, pastikan data difilter ulang
        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            // Rehitung total
            $orders['total'] = count($orders['data']);
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

    // currently update
    public function barcode()
    {
        $user         = Auth::user()->username;
        $recordDelete = BarcodeModel::where("username", $user);
        $recordDelete->delete();

        $addedBarcodes  = BarcodeModel::where('username', $user)->latest()->take(10)->get();
        $addedBarcodesLast = [];

        return view("api.purchasing.barcode", [
            'addedBarcodes'  => $addedBarcodes,
            'addedBarcodesLast' => $addedBarcodesLast,
            'docDate' => date('Y-m-d')
        ]);
    }

    public function barcode_po($docEntry, Request $request)
    {
        // delete all data
        $user = Auth::user()->username;
        $recordDelete = BarcodeModel::where("username", $user);
        $recordDelete->delete();

        // get purchase order
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => 1,
            "DocEntry" => $docEntry,
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

        // save db
        foreach ($lines as $line) {
            $barcode        = new BarcodeModel();
            $barcode->code  = $line['ItemCode'];
            $barcode->name  = $line['Dscription'];
            $barcode->username = $user;
            $barcode->qty   = $line['Quantity'];
            $barcode->date_po = date('Y-m-d');
            $barcode->save();
        }

        $addedBarcodes = BarcodeModel::where('username', $user)->latest()->take(10)->get();
        $addedBarcodesLast = BarcodeModel::where('username', $user)->latest()->first();
        return view("api.purchasing.barcode", [
            'items'      => $getItems['data'] ?? [],
            'addedBarcodes'  => $addedBarcodes,
            'addedBarcodesLast' => $addedBarcodesLast,
            'docDate' => $po['DocDate']
        ]);
    }

    public function printBarcodeWithPdf(Request $request)
    {
        $user  = Auth::user()->username;
        $codes = $request->input('codes', []);
        $qtys  = $request->input('qtys', []);

        if (empty($codes) || empty($qtys)) {
            return back()->with('error', 'No barcode data received.');
        }

        foreach ($codes as $index => $code) {
            $newQty = isset($qtys[$index]) ? (int) $qtys[$index] : 0;

            // Get barcode for this user + code
            $barcode = BarcodeModel::where('username', $user)
                ->where('code', $code)
                ->first();

            if ($barcode && $newQty > 0 && $barcode->qty != $newQty) {
                $barcode->qty = $newQty;
                $barcode->save();
            }
        }

        $addedBarcodes = BarcodeModel::where("username", $user)->get();
        if ($addedBarcodes->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        $pdf = Pdf::loadView('backend.purchasing.pdf', compact('addedBarcodes'))
            ->setPaper([0, 0, 121.88, 70.86]); // 43mm x 25mm

        return $pdf->stream('barcodes.pdf');
    }

    public function printBarcodeWithPdfMaklon(Request $request)
    {
        // delete all data
        $user = Auth::user()->username;
        $recordDelete = BarcodeModel::where("username", $user);
        $recordDelete->delete();

        $validated = $request->validate([
            'codes'     => 'required|array',
            'codes.*'   => 'required|string',
            'names'     => 'required|array',
            'names.*'   => 'required|string',
            'qtys'           => 'required|array',
            'qtys.*'         => 'required|numeric',
            'docDate'   => 'required'
        ]);
        // dd($request->all());

        // save db
        $user  = Auth::user()->username;
        foreach ($validated['codes'] as $index => $code) {
            BarcodeModel::create([
                'code'      => $code,
                'name'      => $validated['names'][$index],
                'qty'       => $validated['qtys'][$index],
                'username'  => Auth::user()->username,
                'date_po'   => $validated['docDate'],
            ]);
        }

        // get print
        $addedBarcodes = BarcodeModel::where("username", $user)->get();
        if ($addedBarcodes->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        $pdf = Pdf::loadView('backend.purchasing.pdf', compact('addedBarcodes'))
            ->setPaper([0, 0, 121.88, 70.86]); // 43mm x 25mm

        return $pdf->stream('barcodes.pdf');
    }
}
