<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\goodissueModel;
use App\Models\goodreceiptModel;
use App\Models\grpoModel;
use App\Models\IFPModel;
use App\Models\ItemsMaklonModel;
use App\Models\ItemsModel;
use App\Models\ProductionModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use illuminate\support\facades\Auth;
use phpDocumentor\Reflection\Types\Null_;
use App\Services\SapService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\SapReasonModel as SapReason;

class TransactionController extends Controller
{
    // stock in
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }
    // stock in
    public function stock_in(Request $request)
    {
        $po       = $request->get('po');
        $docEntry = $request->get('docEntry');
        $poData   = [];

        return view('api.transaction.stockin', compact('po', 'docEntry'));
    }


    public function stock_in_old(Request $request)
    {
        $temp           = StockModel::where('is_temp', true)->orderByDesc('id')->first();
        $latestStock    = StockModel::whereNotNull('grpo')->orderByDesc('id')->first();
        if ($temp) {
            $grpo       = $temp->grpo;
        } else {
            $grpo       = $latestStock && $latestStock->grpo ? ((int)$latestStock->grpo + 1) : 1;
        }
        $getPos         = null;

        return view('backend.transaction.stockin', compact('grpo', 'getPos'));
    }

    public function stockin_po(Request $request, $po)
    {

        $temp           = StockModel::where('is_temp', true)->orderByDesc('id')->first();
        $latestStock    = StockModel::whereNotNull('grpo')->orderByDesc('id')->first();
        if ($temp) {
            $grpo       = $temp->grpo;
        } else {
            $grpo       = $latestStock && $latestStock->grpo ? ((int)$latestStock->grpo + 1) : 1;
        }
        $getPos         = null;
        $getPos = PurchasingModel::where("no_po", $po)->first();
        return view('backend.transaction.stockin', compact('grpo', 'getPos'));
    }

    public function scan_and_store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string',
            'po'        => 'nullable|string',
            'docEntry'  => 'nullable|string',
        ]);

        $warehouse = "BK001";
        $barcode   = $validated['item_code'];

        $items = $this->sap->getStockItems([
            'ItemCode' => $barcode,
            'WhsCode'  => $warehouse,
            'limit'    => 1,
            'page'     => 1
        ]);

        if (!Arr::get($items, 'success')) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan SAP, coba lagi beberapa saat'
            ]);
        }

        if (empty(Arr::get($items, 'total'))) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: {$barcode}. Scan ulang!"
            ]);
        }

        // Build PO params
        $poParam = [
            "page"      => 1,
            "limit"     => 100,
            "ItemCode"  => $barcode,
            "DocStatus" => "Open"
        ];
        if (!empty($validated['docEntry'])) {
            $poParam['DocEntry'] = $validated['docEntry'];
        }
        if (!empty($validated['po'])) {
            $poParam['DocNum'] = $validated['po'];
        }

        $get_po = $this->sap->getPurchaseOrders($poParam);

        if (!Arr::get($get_po, 'success') || empty(Arr::get($get_po, 'data'))) {
            return response()->json([
                'success' => false,
                'message' => "Nomor PO tidak ditemukan untuk barcode: {$barcode}"
            ]);
        }

        if ($validated['docEntry'] && $validated['po']) {
            $poData = Arr::get($get_po, 'data.0', []);
        } else {
            $poData = Arr::get($get_po, 'data', []);
        }
        $item   = Arr::get($items, 'data.0', []);

        $warehouseStock = collect(Arr::get($item, 'warehouses', []))
            ->firstWhere('WhsCode', $warehouse);

        return response()->json([
            'success'        => true,
            'itemCode'       => Arr::get($item, 'ItemCode'),
            'ItemName'       => Arr::get($item, 'ItemName'),
            'warehouseStock' => $warehouseStock,
            // 'items'          => $items,
            'poData'         => $poData,
            'message'        => 'Item berhasil di scan!'
        ]);
    }

    public function grpo_histories(Request $request)
    {
        $validated = $request->validate([
            'DocNum'   => 'required|string',
            'DocEntry' => 'required|string',
        ]);

        $grpo_histories = GrpoModel::where('no_po', $validated['DocNum'])
            ->where('base_entry', $validated['DocEntry'])
            ->orderBy('created_at', 'desc') // ganti field order sesuai kebutuhan
            ->get();

        if ($grpo_histories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data GRPO history tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $grpo_histories
        ]);
    }


    public function scan_and_store_old(Request $request)
    {
        $barcode    = $request->input("item_code");
        $items      = ItemsModel::where('code', $barcode)->first();
        if (!$items) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan Scan kembali!"
            ]);
        }
        $poDetails      = PurchaseOrderDetailsModel::where("item_code", $barcode)->pluck('nopo')->unique()->toArray();
        $purchaseOrders = PurchasingModel::select("no_po")->where("status", "Open")->whereIn('no_po', $poDetails)->distinct()->get();
        $latestStockOut = StockModel::where("item_code", $items->code)->whereNotNull("stock_out")->orderByDesc("id")->value("stock_out") ?? 0;
        $latestStockIn  = StockModel::where("item_code", $items->code)->whereNotNull("stock_in")->orderByDesc("id")->value("stock_in") ?? 0;
        $latestOnhand   = ($items->in_stock + $latestStockIn) - $latestStockOut;
        $user           = Auth::user()->id;

        // save db
        $stock              = new StockModel();
        $stock->grpo        = trim($request->input("grpo"));
        $stock->item_code   = $items->code;
        $stock->stock       = $items->in_stock ?? 0;
        $stock->stock_out   = $latestStockOut;
        $stock->scanned_by  = $user;
        $stock->is_temp     = true;
        $stock->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'id'        => $stock->id,
                'code'      => $items->code,
                'name'      => $items->name, // tambahkan ini
                'no_po'     => $purchaseOrders,
                'on_hand'   => $latestOnhand,
                // 'Po_details'    => $poDetails,
                'message'   => 'Item berhasil di scan!'
            ]);
        }
    }

    public function getScannedBarcodes($grpo)
    {
        $user            = Auth::user()->id;
        $scannedBarcodes = StockModel::where('grpo', $grpo)->where('is_temp', true)->where('scanned_by', $user)->get();

        return view("backend.transaction.partials.scanned", compact('scannedBarcodes'));
    }

    // POST GRPO
    public function stock_up(Request $request)
    {
        $post_grpo = null;
        $postData  = [];

        try {
            $validated = $request->validate([
                'no_po'        => 'required',
                'cardName'     => 'required',
                'cardCode'     => 'required',
                'docDate'      => 'required|date',
                'remarks'      => 'required|string',
                'numAtCard'    => 'nullable|string',
                'U_MEB_NO_IO'  => 'nullable|string',
                'U_MEB_No_SO'  => 'nullable|string',
                'U_MEB_Ket_Pur' => 'nullable|string',
                'stocks'                       => 'required|array|min:1',
                'stocks.*.BaseEntry'           => 'nullable',
                'stocks.*.LineNum'             => 'required',
                'stocks.*.ItemCode'            => 'required|string',
                'stocks.*.Dscription'          => 'nullable|string',
                'stocks.*.qty'                 => 'required|numeric|min:1',
                'stocks.*.PriceBefDi'          => 'nullable|numeric',
                'stocks.*.DiscPrcnt'           => 'nullable|numeric',
                'stocks.*.VatGroup'            => 'nullable|string',
                'stocks.*.AcctCode'            => 'nullable|string',
                'stocks.*.OcrCode'             => 'nullable|string',
                'stocks.*.FreeText'            => 'nullable|string',
                'stocks.*.UnitMsr'             => 'nullable|string',
            ]);

            // Header untuk API
            $postData = [
                'CardCode'    => $validated['cardCode'],
                'CardName'    => $validated['cardName'],
                'DocDate'     => date("Y/m/d"),
                'NumAtCard'   => $validated['numAtCard'] ?? null,
                'Comments'    => $validated['remarks'],
                'U_MEB_NO_IO' => $validated['U_MEB_NO_IO'] ?? null,
                'U_MEB_No_SO' => $validated['U_MEB_No_SO'] ?? null,
                'Lines'       => []
            ];

            $lines        = [];
            $insertedData = [];
            $user         = Auth::id();

            foreach ($validated['stocks'] as $row) {
                // untuk API SAP
                $lines[] = [
                    'BaseEntry'   => $row['BaseEntry'] ?? null,
                    'BaseLineNum' => $row['LineNum'] ?? null,
                    'ItemCode'    => $row['ItemCode'],
                    'Dscription'  => $row['Dscription'] ?? null,
                    'Quantity'    => $row['qty'],
                    'PriceBefDi'  => $row['PriceBefDi'] ?? null,
                    'DiscPrcnt'   => $row['DiscPrcnt'] ?? null,
                    'VatGroup'    => $row['VatGroup'] ?? null,
                    'AcctCode'    => $row['AcctCode'] ?? null,
                    'OcrCode'     => $row['OcrCode'] ?? null,
                    'FreeText'    => $row['FreeText'] ?? null,
                    'UnitMsr'     => $row['UnitMsr'] ?? null,
                ];

                // untuk DB
                $insertedData[] = [
                    'no_po'        => $validated['no_po'],
                    'vendor_code'  => $validated['cardCode'],
                    'vendor'  => $validated['cardName'],
                    'vendor_ref_no' => $validated['numAtCard'],
                    'io'           => $validated['U_MEB_NO_IO'],
                    'so'           => $validated['U_MEB_No_SO'],
                    'internal_no'  => $row['U_MEB_Ket_Pur'] ?? null,
                    'base_entry'   => $row['BaseEntry'] ?? null,
                    'line_num'     => $row['LineNum'] ?? null,
                    'item_code'    => $row['ItemCode'] ?? null,
                    'item_desc'    => $row['Dscription'] ?? null,
                    'qty'    => $row['qty'] ?? null,
                    'uom'    => $row['UnitMsr'] ?? null,
                    'whse'    => 'BK001',
                    'note'    => '-',
                    'user_id'      => $user,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ];
            }
            $postData['Lines'] = $lines;

            DB::beginTransaction();

            // Call API SAP
            $post_grpo = $this->sap->postGrpo($postData);
            if (empty($post_grpo['success'])) {
                throw new \Exception($post_grpo['message'] ?? 'SAP GRPO failed without message');
            }

            // Insert ke DB
            if (!empty($insertedData)) {
                GrpoModel::insert($insertedData);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Telah berhasil menambahkan item yang sudah di scan',
                'request'  => $postData,
                'response' => $post_grpo ?? [],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
                'request' => $postData,
                'response' => $post_grpo ?? [],
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request' => $postData,
                'response' => $post_grpo ?? [],
            ], 500);
        }
    }


    public function stock_up_old(Request $request)
    {
        $validated = $request->validate([
            'stocks'        => 'required|array',
            'stocks.*.id'   => 'required|integer|exists:stocks,id',
            'stocks.*.item_code'    => 'required|string',
            'stocks.*.qty'  => 'required|numeric|min:1',
        ]);

        foreach ($validated['stocks'] as $stockData) {
            $item = ItemsModel::where('code', $stockData['item_code'])->first();
            if ($item) {
                $latestStockin  = StockModel::where("item_code", $item->code)->whereNotNull("stock_in")->orderByDesc("id")->value("stock_in");
            }
            $stock  = StockModel::find($stockData["id"]);
            if ($stock) {
                $stock->no_po       = trim($request->nopo);
                $stock->qty         = $stockData["qty"];
                $stock->stock_in    = ($latestStockin ?? 0) + $stockData["qty"];
                $stock->is_temp     = false;
                $stock->save();
            }
        }

        // save grpo table
        $po = PurchasingModel::where('no_po', trim($request->nopo))->first();
        if (!$po) {
            throw new \Exception('PO not found');
        }

        try {
            $grpo = grpoModel::updateOrCreate(
                ['no_po' => trim($request->nopo)],
                [
                    'vendor_code'   => $po->vendor_code,
                    'vendor'        => $po->vendor,
                    'vendor_ref_no' => $po->vendor_ref_no,
                    'io'            => $po->io,
                    'so'            => $po->so,
                    'internal_no'   => $po->internal_no,
                    'whse'          => "BK001",
                    'note'          => $po->note,
                    'remarks'       => trim($request->remarks)
                ]
            );
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        }

        return redirect('admin/transaction/stockdet/' . $request->input("grpo"))->with('success', 'Telah berhasil menambahkan item yang sudah di scan');
    }

    public function stock_del($grpo)
    {
        // dd($grpo);
        $records        = StockModel::where("grpo", $grpo)->get();
        $getRecord      = StockModel::where("grpo", $grpo)->first();
        // dd("getRecord", $getRecord);

        // set value PO
        $items          = ItemsModel::where("code", $getRecord->item_code)->first();
        $getPO          = PurchasingModel::select("id")->where("no_po", $getRecord->no_po)->first();
        $getQtyPo       = PurchaseOrderDetailsModel::where("nopo", $getRecord->no_po)->whereNotNull("qty")->sum("qty") ?? 0;
        $getQtyStocks   = StockModel::where("id", !$getRecord->id)->where("no_po", $getRecord->no_po)->whereNotNull("qty")->sum("qty") ?? 0;
        $getItemStocks  = StockModel::where("no_po", $getRecord->no_po)->where("item_code", $items->code)->whereNotNull("qty")->first();
        $getIdStocks    = $getItemStocks->id;
        $resultQty      = $getQtyPo - $getQtyStocks;
        if ($resultQty < 0) {
            PurchasingModel::where("id", $getPO->id)->update(["status" => "Closed"]);
            StockModel::where("id", $getIdStocks)->update(["note" => "Stock In over qty from PO"]);
        } else if ($resultQty == 0) {
            PurchasingModel::where("id", $getPO->id)->update(["status" => "Closed"]);
        } else if ($resultQty > 0) {
            PurchasingModel::where("id", $getPO->id)->update(["status" => "Open"]);
            $resultQty;
        }
        // dd("qty po", $getQtyPo, "qty stock", $getQtyStocks, "Result Qty", $resultQty, "get PO", $getPO->id);

        // delete data
        grpoModel::where("no_po", $getRecord->no_po)->delete();
        if ($records->isEmpty()) {
            return redirect("admin/transaction/stockin")->with("error", "Tidak ada data yang dihapus");
        }
        $itemNames = $records->map(function ($records) {
            return $records->item->name ?? "Tidak dikenal";
        })->implode(", ");
        StockModel::where("grpo", $grpo)->delete();

        return redirect('admin/transaction/stockin')->with('warning', "Membatalkan item(s) {$itemNames}!");
    }

    public function stockin_delone($id)
    {
        $records = StockModel::where("id", $id)->delete();

        if ($records) {
            return response()->json(["success", true]);
        } else {
            return response()->json(["Error", "Item not found"], 404);
        }
    }

    public function stock_det(Request $request, $grpo)
    {
        $getRecord  = StockModel::where("grpo", $grpo)->first();
        $getData    = StockModel::where("grpo", $grpo)->orderBy('id', 'desc')->paginate(5);

        // set value PO
        $items          = ItemsModel::where("code", $getRecord->item_code)->first();
        $getPO          = PurchasingModel::with("po_details")->where("no_po", $getRecord->no_po)->first();
        $getQtyPo       = PurchaseOrderDetailsModel::where("nopo", $getRecord->no_po)->whereNotNull("qty")->sum("qty") ?? 0;
        $getQtyStocks   = StockModel::where("no_po", $getRecord->no_po)->whereNotNull("qty")->sum("qty") ?? 0;
        $getItemStocks  = StockModel::where("grpo", $grpo)->where("no_po", $getRecord->no_po)->where("item_code", $items->code)->whereNotNull("qty")->first();
        $resultQty      = $getQtyPo - $getQtyStocks;
        if ($resultQty < 0) {
            PurchasingModel::where("no_po", $getPO->no_po)->update(["status" => "Closed"]);
            StockModel::where("id", $getItemStocks->id)->update(["note" => "Stock In over qty from PO"]);
        } else if ($resultQty == 0) {
            PurchasingModel::where("no_po", $getPO->no_po)->update(["status" => "Closed"]);
        } else if ($resultQty > 0) {
            $resultQty;
        }

        // dd($getQtyPo, $getQtyStocks, $resultQty);
        return view("backend.transaction.stockdet", compact('getRecord', 'getData'));
    }

    // stockout - Issue For Production
    public function stock_out(Request $request)
    {
        $po       = $request->get('docNum');
        $docEntry = $request->get('docEntry');
        $gi_reasons = SapReason::where('type', 'issue')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();

        return view('api.transaction.stockout', compact(
            'po',
            'docEntry',
            'gi_reasons',
        ));
    }

    public function stock_out_old()
    {
        $temp           = StockModel::where('is_temp', true)->orderByDesc('id')->first();
        $latestStockOut = StockModel::whereNotNull('isp')->orderByDesc('id')->first();
        if ($temp) {
            $isp        = $latestStockOut && $latestStockOut->isp ? $latestStockOut->isp : 1;
        } else {
            $isp        = $latestStockOut && $latestStockOut->isp ? ((int)$latestStockOut->isp + 1) : 1;
        }
        $getProdorder   = null;
        $getPos         = null;

        return view('backend.transaction.stockout', compact("getProdorder", "getPos", "isp"));
    }

    public function stockout_po($prod_order)
    {
        $temp           = StockModel::where('is_temp', true)->orderByDesc('id')->first();
        $latestStockOut = StockModel::whereNotNull('isp')->orderByDesc('id')->first();
        if ($temp) {
            $isp        = $latestStockOut && $latestStockOut->isp ? $latestStockOut->isp : 1;
        } else {
            $isp        = $latestStockOut && $latestStockOut->isp ? ((int)$latestStockOut->isp + 1) : 1;
        }
        // dd([
        //     'temp' => $temp,
        //     'latestStockOut' => $latestStockOut,
        //     'isp' => $isp
        // ]);

        $getPos = ProductionModel::where("doc_num", $prod_order)->first();
        return view("backend.transaction.stockout", compact("getPos", "isp"));
    }

    public function scan_and_issued(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string',
        ]);

        $warehouse = "BK001";
        $barcode   = $validated['item_code'];
        $items = $this->sap->getStockItems([
            'ItemCode' => $barcode,
            'WhsCode'  => $warehouse,
            'limit'    => 1,
            'page'     => 1
        ]);

        if (!Arr::get($items, 'success')) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan SAP, coba lagi beberapa saat'
            ]);
        }
        $item   = Arr::get($items, 'data.0', []);
        $warehouseStock = collect(Arr::get($item, 'warehouses', []))
            ->firstWhere('WhsCode', $warehouse);
        if (empty(Arr::get($items, 'total'))) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: {$barcode}. Scan ulang!"
            ]);
        }

        return response()->json([
            'success'        => true,
            'itemCode'       => Arr::get($item, 'ItemCode'),
            'ItemName'       => Arr::get($item, 'ItemName'),
            'warehouseStock' => $warehouseStock,
            // 'items'          => $items,
            // 'prodData'         => $prodData,
            'message'        => 'Item berhasil di scan!'
        ]);
    }

    public function scan_and_issued_old(Request $request)
    {
        $barcode    = $request->input("item_code");
        $Prodorder  = $request->input("prod_order") ? $request->input("prod_order") : 1;
        $items      = ItemsModel::where('code', $barcode)->first();
        if (!$items) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan scan kembali!"
            ]);
        }
        $docNums            = ProductionOrderDetailsModel::where("item_code", $barcode)->pluck("doc_num")->toArray();
        $productionOrders   = ProductionModel::where("status", "Released")->whereIn("doc_num", $docNums)->distinct()->get();
        $latestStockIn      = StockModel::where("item_code", $items->code)->whereNotNull("stock_in")->orderByDesc("id")->value("stock_in") ?? 0;
        $latestStockOut     = StockModel::where("item_code", $items->code)->whereNotNull("stock_out")->orderByDesc("id")->value("stock_out") ?? 0;
        $latestOnhand       = ($items->in_stock + $latestStockIn) - $latestStockOut;
        $user               = Auth::user()->id;

        // save db
        if ($productionOrders->isNotEmpty()) {
            $stock              = new StockModel();
            $stock->item_code   = $items->code;
            $stock->prod_order  = $Prodorder;
            $stock->isp         = trim($request->input("isp"));
            $stock->stock       = $items->in_stock;
            $stock->stock_in    = $latestStockIn;
            $stock->scanned_by  = $user;
            $stock->is_temp     = true;
            $stock->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success'   => true,
                    'code'      => $items->code,
                    'name'      => $items->name, // tambahkan ini
                    'doc_num'   => $productionOrders,
                    'on_hand'   => $latestOnhand,
                ]);
            }
        } else {
            return response()->json([
                "message"   => "Data production order tidak ditemukan!"
            ]);
        }
    }

    public function getScanOut($isp)
    {
        $user            = Auth::user()->id;
        $scannedBarcodes = StockModel::where('isp', $isp)->where('is_temp', true)->where('scanned_by', $user)->get();

        return view("backend.transaction.partials.scanned-out", compact('scannedBarcodes'));
    }
// POST Issue For Production
    public function save_production_issue(Request $request)
    {
        $post_gi = null;
        $postData  = [];

        try {
            $validated = $request->validate([
                'prod_order'        => 'nullable',
                'remarks'      => 'required|string',
                'reason'      => 'required|string',
                'docEntry'      => 'required|string',
                'no_io'      => 'nullable|string',
                'no_so'      => 'nullable|string',
                'project'      => 'nullable|string',
                'warehouse'      => 'nullable|string',
                'reason'      => 'required|string',
                'cost_center'      => 'nullable|string',
                'prod_type'      => 'nullable|string',
                'stocks'                       => 'required|array|min:1',
                'stocks.*.BaseEntry'            => 'required|string',
                'stocks.*.BaseLine'          => 'nullable|string',
                'stocks.*.qty'                 => 'required|numeric|min:1',
                'stocks.*.UnitMsr'             => 'nullable|string',
            ]);

            $warehouse = $validated['warehouse'] ?? '';
            $project = $validated['project'] ?? '';

            // Header untuk API
            $postData = [
                // "Series" => 694,
                'DocDate'     => date("Y/m/d"),
                'Comment'    => $validated['remarks'] ?? '',
                'ProductionType'    => $validated['prod_type'] ?? '',
                "Ext" => [
                    "U_MEB_Alasan_GIssues" => $validated['reason'],
                    "U_MEB_Default_Whse" =>   $warehouse,
                    "U_MEB_No_IO" =>   $validated['no_io'] ?? '',
                    "U_MEB_No_SO" =>   $validated['no_so'] ?? '',
                    "U_MEB_Project_Code" =>   $project ?? '',
                    "U_MEB_DIST_RULE" =>  $validated['cost_center'] ?? ''
                ],
                'Lines'       => []
            ];

            $lines        = [];

            foreach ($validated['stocks'] as $row) {
                // untuk API SAP
                $lines[] = [
                    'BaseEntry'    => $row['BaseEntry'] ?? '',
                    'BaseLine'  => $row['BaseLine'] ?? null,
                    'Quantity'    => $row['qty'] ?? '',
                    'WhsCode'    =>  $warehouse ?? '',
                ];

                // untuk DB
                // $insertedData[] = [
                // ];
            }
            $postData['Lines'] = $lines;
            // Call API SAP
            $post_gi = $this->sap->postProdIssue($postData);
            if (empty($post_gi['success'])) {
                throw new \Exception($post_gi['message'] ?? 'SAP Issue For Production failed without message');
            }


            return response()->json([
                'success'  => true,
                'message'  => 'Issue For Production Telah Berhasil Disimpan',
                'request'  => $postData,
                'response' => $post_gi ?? [],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 500);
        }
    }

    public function stockout_up(Request $request)
    {
        $validated = $request->validate([
            "stocks"    => 'required|array',
            "stocks.*.id"   => 'required|integer|exists:stocks,id',
            "stocks.*.item_code" => 'required|string',
            "stocks.*.qty"  => 'required|numeric|min:0.0001'
        ]);

        foreach ($validated["stocks"] as $stockData) {
            $items = ItemsModel::where("code", $stockData["item_code"])->first();
            if ($items) {
                $latestStockOut = StockModel::where("item_code", $items->code)->whereNotNull("stock_out")->orderByDesc("id")->value("stock_out");
            }
            $stock = StockModel::find($stockData["id"]);
            if ($stock) {
                $stock->qty         = $stockData["qty"];
                $stock->prod_order  = trim($request->prod_order);
                $stock->stock       = ($items->in_stock ?? 0);
                $stock->stock_out   = ($latestStockOut ?? 0) + $stockData["qty"];
                $stock->is_temp     = false;
                $stock->save();
            }
        }

        // save ifp table
        $po = ProductionModel::where('doc_num', trim($request->prod_order))->first();
        if (!$po) {
            throw new \Exception('Prod Order not found');
        }

        try {
            $ifp = IFPModel::updateOrCreate(
                ['no_po' => trim($request->prod_order)],
                [
                    'project_code'  => $po->project_code,
                    'whse'          => "BK001",
                    'reason'        => trim($request->reason),
                    'io'            => $po->io_no,
                    'so'            => $po->sales_order,
                    'remarks'       => trim($request->remarks)
                ]
            );
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        }

        return redirect("admin/transaction/stockoutdet/" . $stock->isp)->with("success", "Telah berhasil mengeluarkan item yang sudah di scan");
    }

    public function stockout_det(Request $request, $isp)
    {
        $getRecord  = StockModel::where("isp", $isp)->first();
        $getData    = StockModel::where("isp", $isp)->orderByDesc("id")->paginate(5);
        $prodOrder  = $getRecord->prod_order;
        $getProd    = ProductionModel::where("doc_num", $prodOrder)->first();

        // dd("qty po", $getQtyPo, "qty stock", $getQtyStocks, "Result Qty", $resultQty, "get PO", $getPO->id);
        return view("backend.transaction.stockoutdet", compact("getRecord", "getData", "getProd"));
    }

    public function stockout_delone($id)
    {
        // dd($id);
        $deleted = StockModel::where("id", $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Item not found'], 404);
        }
    }

    public function stockout_del($isp)
    {
        // dd($grpo);
        $records    = StockModel::where("isp", $isp)->get();
        $getRecord  = StockModel::where("isp", $isp)->first();

        // set value Prod order
        $getPO          = ProductionModel::where("doc_num", $getRecord->prod_order)->first();

        // delete data
        if ($records->isEmpty()) {
            return redirect("admin/transaction/stockout")->with("error", "Tidak ada data yang dihapus");
        }
        $itemNames = $records->map(function ($records) {
            return $records->item->name ?? "Tidak dikenal";
        })->implode(", ");
        IFPModel::where("no_po", $getPO->doc_num)->delete();
        StockModel::where("isp", $isp)->delete();

        return redirect('admin/transaction/stockout')->with('warning', "Membatalkan item(s) {$itemNames}!");
    }

    // Receipt From Prod
    public function receipt_from_prod(Request $request)
    {
        $po       = $request->get('docNum');
        $docEntry = $request->get('docEntry');
        $gr_reason = SapReason::where('type', 'receipt')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();

        return view('api.transaction.receiptfromprod', compact(
            'po',
            'docEntry',
            'gr_reason',
        ));
    }

    public function scan_and_receipt(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string',
        ]);

        $warehouse = "BK001";
        $barcode   = $validated['item_code'];
        $items = $this->sap->getStockItems([
            'ItemCode' => $barcode,
            'WhsCode'  => $warehouse,
            'limit'    => 1,
            'page'     => 1
        ]);

        if (!Arr::get($items, 'success')) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan SAP, coba lagi beberapa saat'
            ]);
        }
        $item   = Arr::get($items, 'data.0', []);
        $warehouseStock = collect(Arr::get($item, 'warehouses', []))
            ->firstWhere('WhsCode', $warehouse);
        if (empty(Arr::get($items, 'total'))) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: {$barcode}. Scan ulang!"
            ]);
        }

        return response()->json([
            'success'        => true,
            'itemCode'       => Arr::get($item, 'ItemCode'),
            'ItemName'       => Arr::get($item, 'ItemName'),
            'warehouseStock' => $warehouseStock,
            // 'items'          => $items,
            // 'prodData'         => $prodData,
            'message'        => 'Item berhasil di scan!'
        ]);
    }

    public function save_prod_receipt(Request $request)
    {
        $post_gi = null;
        $postData  = [];

        try {
            $validated = $request->validate([
                'prod_order'        => 'nullable',
                'remarks'      => 'required|string',
                'reason'      => 'required|string',
                'docEntry'      => 'required|string',
                'no_io'      => 'nullable|string',
                'no_so'      => 'nullable|string',
                'project'      => 'nullable|string',
                'warehouse'      => 'nullable|string',
                'reason'      => 'required|string',
                // 'cost_center'      => 'nullable|string',
                'prod_type'      => 'nullable|string',
                'stocks'                       => 'required|array|min:1',
                'stocks.*.BaseEntry'            => 'required|string',
                'stocks.*.BaseLine'          => 'nullable|string',
                'stocks.*.qty'                 => 'required|numeric|min:1',
                'stocks.*.UnitMsr'             => 'nullable|string',
            ]);

            $warehouse = $validated['warehouse'] ?? '';
            $project = $validated['project'] ?? '';

            // Header untuk API
            $postData = [
                // "Series" => ,
                'DocDate'     => date("Y/m/d"),
                'Comment'    => $validated['remarks'] ?? '',
                'ProductionType'    => $validated['prod_type'] ?? '',
                "Ext" => [
                    "U_MEB_Alasan_GRceipt" => $validated['reason'],
                    "U_MEB_Default_Whse" =>   $warehouse,
                    "U_MEB_No_IO" =>   $validated['no_io'] ?? '',
                    "U_MEB_No_SO" =>   $validated['no_so'] ?? '',
                    "U_MEB_Project_Code" =>   $project ?? '',
                    // "U_MEB_DIST_RULE" =>  $validated['cost_center'] ?? ''
                ],
                'Lines'       => []
            ];

            $lines        = [];
            foreach ($validated['stocks'] as $row) {
                // untuk API SAP
                $lines[] = [
                    'BaseEntry'    => $row['BaseEntry'] ?? '',
                    'BaseLine'  => $row['BaseLine'] ?? null,
                    'Quantity'    => $row['qty'] ?? '',
                    'WhsCode'    =>  $warehouse ?? '',
                ];

                // untuk DB
                // $insertedData[] = [
                // ];
            }
            $postData['Lines'] = $lines;
            // Call API SAP
            $post_gi = $this->sap->postProdReceipt($postData);
            if (empty($post_gi['success'])) {
                throw new \Exception($post_gi['message'] ?? 'SAP Receipt For Production failed without message');
            }


            return response()->json([
                'success'  => true,
                'message'  => 'Receipt For Production Telah Berhasil Disimpan',
                'request'  => $postData,
                'response' => $post_gi ?? [],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 500);
        }
    }

    public function receipt_from_prod_old()
    {
        session()->forget('first_scan');
        $temp   = RFPModel::orderByDesc('id')->where('is_temp', true)->first();
        $rfp    = RFPModel::whereNotNull('number')->orderByDesc('id')->first();
        if (!$temp) {
            $number = $rfp && $rfp->number ? ((int)$rfp->number + 1) : 1;
        } else {
            $number = $rfp->number;
        }

        $getIos     = null;
        $getPos     = null;
        return view("backend.transaction.receiptfromprod", compact("getIos", "getPos", "number"));
    }

    public function getScannedRfp($number)
    {
        $scannedBarcodes = RFPModel::where("number", $number)->latest()->get();
        return view("backend.transaction.partials.scanned-rfp", compact("scannedBarcodes"));
    }

    public function rfp_update(Request $request)
    {
        session()->forget('first_scan');
        $validated = $request->validate([
            "rfp"       => "required|array",
            "rfp.*.id"  => "required|integer|exists:receipt_from_production,id",
            "rfp.*.qty" => "required|numeric|min:1"
        ]);

        foreach ($validated["rfp"] as $rfpData) {
            $po     = ProductionModel::where('doc_num', trim($request->prod_order))->first();
            $rfp    = RFPModel::find($rfpData["id"]);
            if ($rfp) {
                $rfp->io            = $po->io_no ?? "";
                $rfp->prod_order    = trim($request->input("prod_order"));
                $rfp->qty           = $rfpData["qty"];
                $rfp->so            = $po->sales_order ?? "";
                $rfp->project_code  = $po->project_code ?? "";
                $rfp->whse          = $po->whse ?? "";
                $rfp->reason        = trim($request->input("reason"));
                $rfp->remarks       = trim($request->input("remarks"));
                $rfp->is_temp       = false;
                $rfp->save();
            }
        }

        return redirect("admin/transaction/rfpdetail/" . $request->input("number"))->with("success", "Telah berhasil receipt from production");
    }

    public function rfp_delone($id)
    {
        session()->forget('first_scan');
        $records = RFPModel::where("id", $id)->delete();

        if ($records) {
            return response()->json(["success", true]);
        } else {
            return response()->json(["Error", "Item not found"], 404);
        }
    }

    public function rfp_detail(Request $request, $number)
    {
        $getRecord  = RFPModel::where("number", $number)->first();
        $RFPQty     = RFPModel::where("prod_order", $getRecord->prod_order)->whereNotNull('qty')->value('qty');
        $getPO      = RFPModel::where("number", $number)->whereNotNull('qty')->value('prod_order');
        $getData    = RFPModel::where("number", $number)->orderByDesc("id")->paginate(5);
        if ($RFPQty > 0) {
            ProductionModel::where('doc_num', $getPO)->update(['status' => 'Closed']);
        }

        // save table items
        if ($getRecord->prod_no) {
            $items = new ItemsModel();
            $items->code        = $getRecord->prod_no;
            $items->name        = $getRecord->prod_desc;
            $items->group       = "-";
            $items->uom         = "-";
            $items->in_stock    = $getRecord->qty;
            $items->stock_min   = 0;
            $items->save();
        } else {
            ItemsModel::where('code', $getRecord->prod_no)->update(['in_stock' => $getRecord->qty]);
        }

        return view("backend.transaction.rfpdetail", compact("getRecord", "getData"));
    }

    public function rfp_delete($number)
    {
        session()->forget('first_scan');
        $records    = RFPModel::where("number", $number)->get();
        $getPo      = RFPModel::where("number", $number)->get()->value('prod_order');
        ProductionModel::where('doc_num', $getPo)->update(['status' => "Released"]);

        $prods = $records->map(function ($records) {
            return $records->prod_desc ?? "Tidak dikenal";
        })->implode(", ");
        RFPModel::where("number", $number)->delete();

        // delete items
        foreach ($records as $record) {
            $item = ItemsModel::where('code', $record->prod_no)->first();

            if ($item && $item->in_stock >= $record->qty) {
                $item->decrement('in_stock', $record->qty);
            }
        }

        return redirect("admin/transaction/rfp")->with("warning", "Membatalkan product {$prods}!");
    }

    // good issue
    public function good_issued(Request $request)
    {
        $po       = $request->get('po');
        $docEntry = $request->get('docEntry');

        $gi_reasons = SapReason::where('type', 'issue')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();

        $inv_trans_reasons = SapReason::where('type', 'inv-trans')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();

        return view('api.transaction.goodissue', compact(
            'po',
            'docEntry',
            'gi_reasons',
            'inv_trans_reasons'
        ));
    }

    public function good_issued_old(Request $request)
    {
        $temp               = ItemsMaklonModel::orderByDesc('id')->where('is_temp', true)->first();
        $latestItemsMaklon  = ItemsMaklonModel::where('gi', '!=', 0)->whereNotNull('gi')->orderByDesc('id')->first();
        if (!$temp) {
            $gi = $latestItemsMaklon && $latestItemsMaklon->gi ? ((int)$latestItemsMaklon->gi + 1) : 1;
        } else {
            $gi = $latestItemsMaklon->gi;
        }
        // dd($temp, $latestItemsMaklon);

        return view('backend.transaction.goodissue', compact('gi'));
    }

    public function scan_and_out(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string',
            'po'        => 'nullable|string',
            'docEntry'  => 'nullable|string',
        ]);

        $warehouse = "BK001";
        $barcode   = $validated['item_code'];

        $items = $this->sap->getStockItems([
            'ItemCode' => $barcode,
            'WhsCode'  => $warehouse,
            'limit'    => 1,
            'page'     => 1
        ]);

        if (!Arr::get($items, 'success')) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan SAP, coba lagi beberapa saat'
            ]);
        }

        if (empty(Arr::get($items, 'total'))) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: {$barcode}. Scan ulang!"
            ]);
        }

        $item   = Arr::get($items, 'data.0', []);
        $warehouseStock = collect(Arr::get($item, 'warehouses', []))
            ->firstWhere('WhsCode', $warehouse);

        return response()->json([
            'success'        => true,
            'itemCode'       => Arr::get($item, 'ItemCode'),
            'ItemName'       => Arr::get($item, 'ItemName'),
            'warehouseStock' => $warehouseStock,
            'items'          => $items['data'],
            'message'        => 'Item berhasil di scan!'
        ]);
    }

    public function save_good_issue(Request $request)
    {
        $post_gi = null;
        $postData  = [];

        try {
            $validated = $request->validate([
                'no_po'        => 'nullable',
                'remarks'      => 'required|string',
                'no_surat_jalan'      => 'nullable|string',
                'no_inventory_tf'      => 'nullable|string',
                'type_inv_transaction'      => 'nullable|string',
                'internal_no'      => 'nullable|string',
                'no_io'      => 'nullable|string',
                'no_so'      => 'nullable|string',
                'project'      => 'nullable|string',
                'warehouse'      => 'nullable|string',
                'cost_center'      => 'nullable|string',
                'reason'      => 'required|string',
                'stocks'                       => 'required|array|min:1',
                'stocks.*.ItemCode'            => 'required|string',
                'stocks.*.Dscription'          => 'nullable|string',
                'stocks.*.qty'                 => 'required|numeric|min:1',
                'stocks.*.UnitMsr'             => 'nullable|string',
            ]);

            $ocr = $validated['cost_center'] ?? '';
            $warehouse = $validated['warehouse'] ?? '';
            $project = $validated['project'] ?? '';

            // Header untuk API
            $postData = [
                // "Series" => 694,
                'DocDate'     => date("Y/m/d"),
                'Comment'    => $validated['remarks'] ?? '',
                "Ext" => [
                    "U_MEB_Alasan_GIssues" => $validated['reason'],
                    "U_MEB_Default_Whse" =>   $warehouse,
                    "U_MEB_Internal_No" =>   $validated['internal_no'] ?? '',
                    "U_MEB_No_IO" =>   $validated['no_io'] ?? '',
                    "U_MEB_No_SO" =>   $validated['no_so'] ?? '',
                    "U_MEB_Project_Code" =>   $project ?? '',
                    "U_SI_No_Surat_Jalan" => $validated['no_surat_jalan'] ?? '',
                    "U_SI_IT" => $validated['no_inventory_tf'] ?? '',
                    "U_MEB_Type_Inv_Trans" => $validated['type_inv_transaction'] ?? '',
                    "U_MEB_PONo_Maklon" => $validated['no_po'] ?? null,
                    "U_MEB_DIST_RULE" =>  $ocr ?? ''
                ],
                'Lines'       => []
            ];

            $lines        = [];

            foreach ($validated['stocks'] as $row) {
                // untuk API SAP
                $lines[] = [
                    'ItemCode'    => $row['ItemCode'] ?? '',
                    'Dscription'  => $row['Dscription'] ?? null,
                    'Quantity'    => $row['qty'] ?? '',
                    'WhsCode'    =>  $warehouse ?? '',
                    'Ext' => [
                        // 'AcctCode'    => "212400",
                        'OcrCode' => $ocr ?? '',
                        'Project' => $project ?? ''
                    ]
                ];

                // untuk DB
                // $insertedData[] = [
                // ];
            }
            $postData['Lines'] = $lines;
            // Call API SAP
            $post_gi = $this->sap->postGoodIssue($postData);
            if (empty($post_gi['success'])) {
                throw new \Exception($post_gi['message'] ?? 'SAP Good Issue failed without message');
            }


            return response()->json([
                'success'  => true,
                'message'  => 'Good Issue Telah Berhasil Disimpan',
                'request'  => $postData,
                'response' => $post_gi ?? [],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 500);
        }
    }

    public function scan_and_out_old(Request $request)
    {
        $barcode    = $request->input("item_code");
        $gi         = $request->input("gi");
        $items      = ItemsModel::where("code", $barcode)->first();
        $user       = Auth::user()->username;
        if (!$items) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan scan kembali"
            ]);
        }
        $on_hand        = ItemsMaklonModel::where("code", $barcode)->orderByDesc("id")->value("in_stock") ?? 0;
        $purchaseOrders = PurchasingModel::with("po_details")->whereHas("po_details", function ($q) {
            $q->where('item_code', 'LIKE', '%Maklon%');
        })->where("status", "Open")->where("status", "!=", "GR")->pluck("no_po");

        // save db
        $goodissue = new ItemsMaklonModel();
        $goodissue->gi          = $gi;
        $goodissue->gr          = 0;
        $goodissue->po          = 0;
        $goodissue->code        = $items->code;
        $goodissue->name        = $items->name;
        $goodissue->uom         = $items->uom;
        $goodissue->in_stock    = 0;
        $goodissue->qty         = 0;
        $goodissue->stock_min   = 0;
        $goodissue->scanned_by  = $user;
        $goodissue->is_temp     = true;
        $goodissue->save();

        return response()->json([
            "success"   => true,
            "name"      => $items->name,
            "message"   => "Item berhasil di scan!",
            "on_hand"   => $on_hand,
            "pos"       => $purchaseOrders ?? 0,
        ]);
    }

    public function get_scanned_gi($gi)
    {
        $user            = Auth::user()->username;
        $scannedBarcodes = ItemsMaklonModel::where('gi', $gi)->where('is_temp', true)->where('scanned_by', $user)->get();

        return view("backend.transaction.partials.scanned-gi", compact("scannedBarcodes"));
    }

    public function gi_update(Request $request)
    {
        $validated   = $request->validate([
            "goodissue.*"       => 'required|array',
            "goodissue.*.id"    => "required|integer|exists:items_maklon,id",
            "goodissue.*.qty"   => "required|numeric|min:1"
        ]);

        // save items maklon
        foreach ($validated["goodissue"] as $giData) {
            $gi             = ItemsMaklonModel::find($giData["id"]);
            $maklonItems    = ItemsMaklonModel::select("in_stock")->where("po", $request->po)->orderByDesc("id")->first();
            if ($gi) {
                $gi->po         = trim($request->po);
                $gi->in_stock   = $maklonItems->in_stock ?? 0;
                $gi->qty        = $giData["qty"];
                $gi->is_temp    = false;
                $gi->save();
            }
        }

        // save gi table
        $po = PurchasingModel::where('no_po', trim($request->po))->first();
        if (!$po) {
            throw new \Exception('PO not found');
        }

        try {
            $gi = goodissueModel::updateOrCreate(
                ['po' => trim($request->nopo)],
                [
                    'po'            => $po->no_po,
                    'io'            => $po->io,
                    'internal_no'   => $po->internal_no,
                    'so'            => $po->so,
                    'no_surat_jalan'        => trim($request->no_surat_jalan),
                    'no_inventory_tf'       => trim($request->no_inventory_tf),
                    'type_inv_transaction'  => trim($request->type_inv_transaction),
                    'reason'        => trim($request->reason),
                    'whse'          => "BK903",
                    'project_code'  => trim($request->project_code) ?? "-",
                    'distr_rule'    => $po->distr_rule,
                    'vendor_code'   => $po->vendor_code,
                    'remarks'       => trim($request->remarks)
                ]
            );
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        }
        // dd($request->all());

        return redirect("admin/transaction/gidetail/" . $request->input("gi"))->with("success", "Telah berhasil good issue");
    }

    public function gi_delone($id)
    {
        $records = ItemsMaklonModel::where("id", $id)->delete();

        if ($records) {
            return response()->json(["success", true]);
        } else {
            return response()->json(["Error", "Item not found"], 404);
        }
    }

    public function gi_detail($gi)
    {
        $getRecord  = ItemsMaklonModel::where("gi", $gi)->first();
        $getData    = ItemsMaklonModel::where('gi', $gi)->orderBy('id', 'desc')->paginate(5);

        // set value po
        $getQtyPo       = PurchaseOrderDetailsModel::where("nopo", $getRecord->po)->whereNotNull("qty")->sum("qty") ?? 0;
        $giQty          = ItemsMaklonModel::where("po", $getRecord->po)->where("gi", $gi)->whereNotNull('qty')->sum('qty') ?? 0;
        $getPO          = PurchasingModel::with("po_details")->where("no_po", $getRecord->po)->first();
        $resultQty      = $getQtyPo - $giQty;
        if ($resultQty < 0) {
            PurchasingModel::where("no_po", $getPO->no_po)->update(["status" => "GR"]);
        } else if ($resultQty == 0) {
            PurchasingModel::where("no_po", $getPO->no_po)->update(["status" => "GR"]);
        } else if ($resultQty > 0) {
            $resultQty;
        }
        // dd("qty po", $getQtyPo, "qty stock", $giQty, "Result Qty", $resultQty, "get PO", $getPO->no_po);

        return view("backend.transaction.gidetail", compact("getData", "getRecord"));
    }

    public function gi_delete($gi)
    {
        $records = ItemsMaklonModel::where("gi", $gi)->get();
        $getPo = ItemsMaklonModel::where("gi", $gi)->get()->value('po');
        PurchasingModel::where('no_po', $getPo)->update(['status' => "Open"]);

        // name delete
        $prods = $records->map(function ($records) {
            return $records->name ?? "Tidak dikenal";
        })->implode(", ");

        // delete
        goodissueModel::where("po", $getPo)->delete();
        ItemsMaklonModel::where("gi", $gi)->delete();
        // dd("prods", $prods, "getPo", $getPo, "gi", $gi);

        return redirect("admin/transaction/goodissued")->with("warning", "Membatalkan product {$prods}!");
    }

    // good receipt
    public function good_receipt(Request $request)
    {
        $po       = $request->get('po');
        $docEntry = $request->get('docEntry');

        $gr_reasons = SapReason::where('type', 'receipt')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();

        $inv_trans_reasons = SapReason::where('type', 'inv-trans')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();

        return view("api.transaction.goodreceipt", compact(
            'po',
            'docEntry',
            'gr_reasons',
            'inv_trans_reasons'
        ));
    }

    public function good_receipt_old(Request $request)
    {
        $temp               = ItemsMaklonModel::orderByDesc('id')->where('is_temp', true)->first();
        $latestItemsMaklon  = ItemsMaklonModel::where('gr', '!=', 0)->whereNotNull('gr')->orderByDesc('id')->first();
        if (!$temp) {
            $gr       = $latestItemsMaklon && $latestItemsMaklon->gr ? ((int)$latestItemsMaklon->gr + 1) : 1;
        } else {
            $gr       = $latestItemsMaklon->gr;
        }
        // dd($temp);

        return view("backend.transaction.goodreceipt", compact("gr"));
    }

    public function scan_and_greceipt(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string',
            'po'        => 'nullable|string',
            'docEntry'  => 'nullable|string',
        ]);

        $warehouse = "BK001";
        $barcode   = $validated['item_code'];

        $items = $this->sap->getStockItems([
            'ItemCode' => $barcode,
            'WhsCode'  => $warehouse,
            'limit'    => 1,
            'page'     => 1
        ]);

        if (!Arr::get($items, 'success')) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan SAP, coba lagi beberapa saat'
            ]);
        }

        if (empty(Arr::get($items, 'total'))) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: {$barcode}. Scan ulang!"
            ]);
        }

        // Build PO params
        $poData = [];
        $get_po = [];
        if (!empty($validated['docEntry']) && !empty($validated['po'])) {
            $poParam = [
                "page"      => 1,
                "limit"     => 1,
                "DocStatus" => "Open"
            ];
            $poParam['DocEntry'] = $validated['docEntry'];
            $poParam['DocNum'] = $validated['po'];
            $get_po = $this->sap->getPurchaseOrders($poParam);

            if (!Arr::get($get_po, 'success') || empty(Arr::get($get_po, 'data'))) {
                return response()->json([
                    'success' => false,
                    'message' => "Nomor PO tidak ditemukan untuk barcode: {$barcode}"
                ]);
            }
            $poData = Arr::get($get_po, 'data.0', []);
        }

        $item   = Arr::get($items, 'data.0', []);
        $warehouseStock = collect(Arr::get($item, 'warehouses', []))
            ->firstWhere('WhsCode', $warehouse);

        return response()->json([
            'success'        => true,
            'itemCode'       => Arr::get($item, 'ItemCode'),
            'ItemName'       => Arr::get($item, 'ItemName'),
            'warehouseStock' => $warehouseStock,
            'items'          => $items['data'],
            'poData'         => $poData,
            'message'        => 'Item berhasil di scan!'
        ]);
    }


    public function save_good_receipt(Request $request)
    {
        $post_gi = null;
        $postData  = [];

        try {
            $validated = $request->validate([
                'no_po'        => 'nullable',
                'remarks'      => 'required|string',
                'no_surat_jalan'      => 'nullable|string',
                'no_inventory_tf'      => 'nullable|string',
                'type_inv_transaction'      => 'nullable|string',
                'internal_no'      => 'nullable|string',
                'ref_surat_jalan'      => 'nullable|string',
                'no_gi'      => 'nullable|string',
                'no_io'      => 'nullable|string',
                'no_so'      => 'nullable|string',
                'project'      => 'nullable|string',
                'warehouse'      => 'nullable|string',
                'cost_center'      => 'nullable|string',
                'reason'      => 'required|string',
                'stocks'                       => 'required|array|min:1',
                'stocks.*.ItemCode'            => 'required|string',
                'stocks.*.Dscription'          => 'nullable|string',
                'stocks.*.qty'                 => 'required|numeric|min:1',
                'stocks.*.UnitMsr'             => 'nullable|string',
            ]);

            $ocr = $validated['cost_center'] ?? '';
            $warehouse = $validated['warehouse'] ?? '';
            $project = $validated['project'] ?? '';

            // Header untuk API
            $postData = [
                'DocDate'     => date("Y/m/d"),
                'Comment'    => $validated['remarks'] ?? '',
                "Ext" => [
                    "U_MEB_Alasan_GRceipt" => $validated['reason'] ?? '',
                    "U_MEB_Default_Whse" =>   $warehouse ?? '',
                    "U_MEB_Internal_No" =>   $validated['internal_no'] ?? '',
                    "U_MEB_No_GI" =>   $validated['no_gi'] ?? '',
                    "U_MEB_No_IO" =>   $validated['no_io'] ?? '',
                    "U_MEB_No_SO" =>   $validated['no_so'] ?? '',
                    "U_MEB_Project_Code" =>   $project ?? '',
                    "U_SI_No_Surat_Jalan" => $validated['no_surat_jalan'] ?? '',
                    "Ref2" => $validated['ref_surat_jalan'] ?? '',
                    "U_SI_IT" => $validated['no_inventory_tf'] ?? '',
                    "U_MEB_Type_Inv_Trans" => $validated['type_inv_transaction'] ?? '',
                    "U_MEB_PONo_Maklon" => $validated['no_po'] ?? null,
                    "U_MEB_DIST_RULE" =>  $ocr
                ],
                'Lines'       => []
            ];

            $lines        = [];

            foreach ($validated['stocks'] as $row) {
                // untuk API SAP
                $lines[] = [
                    'ItemCode'    => $row['ItemCode'] ?? '',
                    'Dscription'  => $row['Dscription'] ?? null,
                    'Quantity'    => $row['qty'] ?? '',
                    'WhsCode'    =>  $warehouse,
                    'Ext' => [
                        'OcrCode' => $ocr,
                        'Project' => $project
                    ]
                ];

                // untuk DB
                // $insertedData[] = [
                // ];
            }
            $postData['Lines'] = $lines;
            // Call API SAP
            $post_gi = $this->sap->postGoodReceipt($postData);
            if (empty($post_gi['success'])) {
                throw new \Exception($post_gi['message'] ?? 'SAP Good Receipt failed without message');
            }


            return response()->json([
                'success'  => true,
                'message'  => 'Telah berhasil Good Receipt item yang sudah di scan',
                'request'  => $postData,
                'response' => $post_gi ?? [],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request' => $postData,
                'response' => $post_gi ?? [],
            ], 500);
        }
    }

    public function scan_and_greceipt_old(Request $request)
    {
        $barcode    = $request->input("item_code");
        $gr         = $request->input("gr");
        $items      = ItemsModel::where("code", $barcode)->first();
        $user       = Auth::user()->username;
        if (!$items) {
            return response()->json([
                "success" => false,
                "message" => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan scan kembali"
            ]);
        }
        $on_hand        = ItemsModel::where("code", $barcode)->orderByDesc("id")->value("in_stock") ?? 0;
        $purchaseOrders = PurchasingModel::where("status", "GR")->pluck("no_po");

        // save db
        $goodreceipt = new ItemsMaklonModel();
        $goodreceipt->gi          = 0;
        $goodreceipt->gr          = $gr;
        $goodreceipt->po          = 0;
        $goodreceipt->code        = $items->code;
        $goodreceipt->name        = $items->name;
        $goodreceipt->uom         = $items->uom;
        $goodreceipt->in_stock    = 0;
        $goodreceipt->qty         = 0;
        $goodreceipt->stock_min   = 0;
        $goodreceipt->scanned_by  = $user;
        $goodreceipt->is_temp     = true;
        $goodreceipt->save();

        return response()->json([
            "success"   => true,
            "name"      => $items->name,
            "message"   => "Item berhasil di scan!",
            "on_hand"   => $on_hand,
            "pos"       => $purchaseOrders ?? 0,
        ]);
    }

    public function get_scanned_gr($gr)
    {
        $user            = Auth::user()->username;
        $scannedBarcodes = ItemsMaklonModel::where('gr', $gr)->where('is_temp', true)->where('scanned_by', $user)->get();

        return view("backend.transaction.partials.scanned-gr", compact("scannedBarcodes"));
    }

    public function gr_delone($id)
    {
        $records = ItemsMaklonModel::where("id", $id)->delete();

        if ($records) {
            return response()->json(["success", true]);
        } else {
            return response()->json(["Error", "Item not found"], 404);
        }
    }

    public function gr_update(Request $request)
    {
        $validated   = $request->validate([
            "goodreceipt.*"       => 'required|array',
            "goodreceipt.*.id"    => "required|integer|exists:items_maklon,id",
            "goodreceipt.*.qty"   => "required|numeric|min:1"
        ]);

        foreach ($validated["goodreceipt"] as $grData) {
            $gr             = ItemsMaklonModel::find($grData["id"]);
            $items          = ItemsModel::where("code", $gr->code)->first();
            $maklonItems    = ItemsMaklonModel::select("in_stock")->where("po", $request->po)->where('code', $gr->code)->orderByDesc("id")->first();
            if ($gr) {
                $gr->po             = trim($request->po);
                $gr->io             = trim($request->io ?? 0);
                $gr->internal_no    = trim($request->internal_no);
                $gr->in_stock       = $maklonItems ? $maklonItems->in_stock + $grData["qty"] : $grData["qty"];
                $gr->qty            = $grData["qty"];
                $gr->is_temp        = false;
                $gr->save();
            }
            if ($items) {
                $items->in_stock = $items ? $items->in_stock + $grData["qty"] : $grData["qty"];
                $items->save();
            }
        }

        // save gi table
        $po = PurchasingModel::where('no_po', trim($request->po))->first();
        $maklonItems = ItemsMaklonModel::where("po", trim($request->po))->first();
        if (!$po) {
            throw new \Exception('PO not found');
        }

        try {
            $gr = goodreceiptModel::updateOrCreate(
                ['po' => trim($request->nopo)],
                [
                    'po'            => $po->no_po,
                    'io'            => $po->io,
                    'internal_no'   => $po->internal_no,
                    'so'            => $po->so,
                    'no_gi'         => $maklonItems->gi,
                    'no_surat_jalan'    => trim($request->no_surat_jalan),
                    'ref_surat_jalan'   => trim($request->ref_surat_jalan),
                    'no_inventory_tf'   => trim($request->no_inventory_tf),
                    'type_inv_transaction'  => trim($request->type_inv_transaction),
                    'reason'        => trim($request->reason),
                    'whse'          => trim($request->whse),
                    'project_code'  => trim($request->project_code),
                    'distr_rule'    => $po->distr_rule,
                    'vendor_code'   => $po->vendor_code,
                    'acct_code'     => $po->acct_code,
                    'remarks'       => trim($request->remarks)
                ]
            );
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        }
        // dd($request->all());

        return redirect("admin/transaction/grdetail/" . $request->input("gr"))->with("success", "Telah berhasil good receipt");
    }

    public function gr_detail($gr)
    {
        $getRecord  = ItemsMaklonModel::where("gr", $gr)->first();
        $getData    = ItemsMaklonModel::where('gr', $gr)->orderBy('id', 'desc')->paginate(5);

        // set value po
        $getQtyPo       = PurchaseOrderDetailsModel::where("nopo", $getRecord->po)->whereNotNull("qty")->sum("qty") ?? 0;
        $grQty          = ItemsMaklonModel::where("po", $getRecord->po)->where("gr", $gr)->whereNotNull('qty')->sum('qty') ?? 0;
        $getPO          = PurchasingModel::with("po_details")->where("no_po", $getRecord->po)->first();
        $resultQty = $getQtyPo - $grQty;
        if ($resultQty < 0) {
            PurchasingModel::where("no_po", $getPO->no_po)->update(["status" => "Closed"]);
        } else if ($resultQty == 0) {
            PurchasingModel::where("no_po", $getPO->no_po)->update(["status" => "Closed"]);
        } else if ($resultQty > 0) {
            $resultQty;
        }
        // dd("qty po", $getQtyPo, "qty stock", $grQty, "Result Qty", $resultQty, "get PO", $getPO->no_po);

        return view("backend.transaction.grdetail", compact("getData", "getRecord"));
    }

    public function gr_delete($gr)
    {
        $records    = ItemsMaklonModel::where("gr", $gr)->get();
        $getPo      = ItemsMaklonModel::where("gr", $gr)->get()->value('po');
        PurchasingModel::where('no_po', $getPo)->update(['status' => "GR"]);

        // cancel add items
        foreach ($records as $record) {
            $item = ItemsModel::where("code", $record->code)->first();

            if ($item) {
                $item->in_stock -= $record->qty;
                $item->save();
            }
        }

        $prods = $records->map(function ($records) {
            return $records->name ?? "Tidak dikenal";
        })->implode(", ");
        goodreceiptModel::where("po", $getPo)->delete();
        ItemsMaklonModel::where("gr", $gr)->delete();
        // dd("prods", $prods, "getPo", $getPo, "gr", $gr);

        return redirect("admin/transaction/goodreceipt")->with("warning", "Membatalkan product {$prods}!");
    }
}
