<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ItemsModel;
use App\Models\ProductionModel;
use App\Models\PurchasingModel;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use illuminate\support\facades\Auth;
use phpDocumentor\Reflection\Types\Null_;

class TransactionController extends Controller
{
    // stock in
    public function stock_in(Request $request)
    {
        $latestStock  = StockModel::whereNotNull('grpo')->orderByDesc('id')->first();
        $grpo = $latestStock && $latestStock->grpo ? ((int)$latestStock->grpo + 1) : 1;
        $getPos = null;
        return view('backend.transaction.stockin', compact('grpo', 'getPos'));
    }

    public function stockin_po(Request $request, $po)
    {
        $latestStock  = StockModel::whereNotNull("grpo")->orderBy('id', 'desc')->first();
        $grpo = $latestStock && $latestStock->grpo ? ($latestStock->grpo + 1) : 1;
        $getPos = PurchasingModel::where("no_po", $po)->first();
        return view('backend.transaction.stockin', compact('grpo', 'getPos'));
    }

    public function scan_and_store(Request $request)
    {
        $barcode    = $request->input("item_code");
        $items      = ItemsModel::where('code', $barcode)->first();
        $item_id    = $items->id;
        if (!$items) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan Scan kembali!"
            ]);
        }
        $purchaseOrders = PurchasingModel::select("no_po")->where("status", "Open")->where('item_code', $barcode)->distinct()->get();
        $latestOnhand   = StockModel::where('item_id', $item_id)->whereNotNull("on_hand")->orderByDesc('id')->value("on_hand") ?? 0;
        $latestStock    = StockModel::where("item_id", $item_id)->whereNotNull("stock")->orderByDesc("id")->value("stock") ?? 0;
        $latestStockOut = StockModel::where("item_id", $item_id)->whereNotNull("stock_out")->orderByDesc("id")->value("stock_out") ?? 0;
        $user           = Auth::user()->id;

        // save db
        $stock              = new StockModel();
        $stock->grpo        = trim($request->input("grpo"));
        $stock->item_id     = $items->id;
        $stock->stock       = $latestStock;
        $stock->stock_out   = $latestStockOut;
        $stock->scanned_by  = $user;
        $stock->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'id'        => $stock->id,
                'code'      => $items->code,
                'name'      => $items->name, // tambahkan ini
                'no_po'     => $purchaseOrders,
                'on_hand'   => $latestOnhand,
                'message'   => 'Item berhasil di scan!'
            ]);
        }
    }

    public function getScannedBarcodes($grpo)
    {
        $scannedBarcodes = StockModel::where('grpo', $grpo)->latest()->get();
        return view("backend.transaction.partials.scanned", compact('scannedBarcodes'));
    }

    public function stock_up(Request $request)
    {
        $validated = $request->validate([
            'stocks'    => 'required|array',
            'stocks.*.id'   => 'required|integer|exists:stocks,id',
            'stocks.*.item_code'    => 'required|string',
            'stocks.*.qty'  => 'required|numeric|min:1',
        ]);

        foreach ($validated['stocks'] as $stockData) {
            $item = ItemsModel::where('code', $stockData['item_code'])->first();
            if ($item) {
                $itemId = $item->id;
                $latestStock = StockModel::where("item_id", $itemId)->whereNotNull("stock")->orderByDesc("id")->value("stock");
                $latestStockin = StockModel::where("item_id", $itemId)->whereNotNull("stock_in")->orderByDesc("id")->value("stock_in");
                $latestOnhand = StockModel::where("item_id", $itemId)->whereNotNull("on_hand")->orderByDesc("id")->value("on_hand");
            }
            $stock = StockModel::find($stockData["id"]);
            if ($stock) {
                $stock->no_po   = trim($request->nopo);
                $stock->qty     = $stockData["qty"];
                $stock->stock   = ($latestStock ?? 0) + $stockData["qty"];
                $stock->stock_in    = ($latestStockin ?? 0) + $stockData["qty"];
                $stock->on_hand = ($latestOnhand ?? 0) + $stockData["qty"];
                $stock->save();
            }
        }

        return redirect('admin/transaction/stockdet/' . $request->input("grpo"))->with('success', 'Telah berhasil menambahkan item yang sudah di scan');
    }

    public function stock_del($grpo)
    {
        // dd($grpo);
        $records = StockModel::where("grpo", $grpo)->get();
        $getRecord = StockModel::where("grpo", $grpo)->first();

        // set value PO
        $stockItemId = $getRecord->item_id;
        $items = ItemsModel::where("id", $stockItemId)->first();
        $getItemPO = PurchasingModel::where("no_po", $getRecord->no_po)->where("item_code", $items->code)->whereNotNull("qty")->first();
        $getIdPo = $getItemPO->id;
        PurchasingModel::where("id", $getIdPo)->update(["status" => "Open"]);
        // dd("id Po", $getIdPo);

        // delete data
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
        $getRecord = StockModel::where("grpo", $grpo)->first();
        $getData = StockModel::where('grpo', 'like', "%{$grpo}%")->orderBy('id', 'desc')->paginate(5);

        // set value PO
        $stockItemId = $getRecord->item_id;
        $items = ItemsModel::where("id", $stockItemId)->first();
        $getItemPO = PurchasingModel::where("no_po", $getRecord->no_po)->where("item_code", $items->code)->whereNotNull("qty")->first();
        $getPO = PurchasingModel::where("no_po", $getRecord->no_po)->where("item_code", $items->code)->whereNotNull("qty")->get()->value('no_po');
        $getQtyPo = PurchasingModel::where("no_po", $getRecord->no_po)->where("item_code", $items->code)->whereNotNull("qty")->sum("qty") ?? 0;
        $getQtyStocks = StockModel::where("no_po", $getRecord->no_po)->where("item_id", $items->id)->whereNotNull("qty")->sum("qty") ?? 0;
        $getItemStocks = StockModel::where("no_po", $getRecord->no_po)->where("item_id", $items->id)->whereNotNull("qty")->first();
        $getIdStocks = $getItemStocks->id;
        $resultQty = $getQtyPo - $getQtyStocks;
        if ($resultQty < 0) {
            PurchasingModel::where("no_po", $getPO)->update(["status" => "Closed"]);
            StockModel::where("id", $getIdStocks)->update(["note" => "Stock In over qty from PO"]);
        } else if ($resultQty == 0) {
            PurchasingModel::where("no_po", $getPO)->update(["status" => "Closed"]);
        } else if ($resultQty > 0) {
            $resultQty;
        }

        // dd($getQtyPo, $getQtyStocks, $resultQty);
        return view("backend.transaction.stockdet", compact('getRecord', 'getData'));
    }

    // stockout
    public function stock_out()
    {
        $latestStockOut = StockModel::whereNotNull("isp")->orderByDesc("id")->first();
        $isp = $latestStockOut && $latestStockOut->isp ? ((int)$latestStockOut->isp + 1) : 1;
        $getProdorder = null;
        $getPos = null;
        return view('backend.transaction.stockout', compact("getProdorder", "getPos", "isp"));
    }

    public function stockout_po($prod_order)
    {
        $latestStockOut = StockModel::whereNotNull("isp")->orderByDesc("id")->first();
        $isp = $latestStockOut && $latestStockOut->isp ? ((int)$latestStockOut->isp + 1) : 1;
        $getPos = ProductionModel::where("doc_num", $prod_order)->first();
        return view("backend.transaction.stockout", compact("getPos", "isp"));
    }

    public function scan_and_issued(Request $request)
    {
        $barcode    = $request->input("item_code");
        $Prodorder  = $request->input("prod_order") ? $request->input("prod_order") : 1;
        $items      = ItemsModel::where('code', $barcode)->first();
        $item_id    = $items->id;
        if (!$items) {
            return response()->json([
                'success' => false,
                'message' => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan scan kembali!"
            ]);
        }
        $productionOrders = ProductionModel::select("doc_num")->where("status", 0)->where("item_code", $barcode)->distinct()->get();
        $ios            = ProductionModel::select("io_no")->where("status", 0)->where("item_code", $barcode)->distinct()->get();
        $latestOnhand   = StockModel::where('item_id', $item_id)->whereNotNull("on_hand")->orderByDesc('id')->value("on_hand") ?? 0;
        $latestStock    = StockModel::where("item_id", $item_id)->whereNotNull("stock")->orderByDesc("id")->value("stock") ?? 0;
        $latestStockIn  = StockModel::where("item_id", $item_id)->whereNotNull("stock_in")->orderByDesc("id")->value("stock_in") ?? 0;
        $user           = Auth::user()->id;

        // save db
        if ($productionOrders->isNotEmpty()) {
            $stock              = new StockModel();
            $stock->item_id     = $items->id;
            $stock->prod_order  = $Prodorder;
            $stock->isp         = trim($request->input("isp"));
            $stock->stock       = $latestStock;
            $stock->stock_in    = $latestStockIn;
            $stock->scanned_by  = $user;
            $stock->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success'   => true,
                    'code'      => $items->code,
                    'name'      => $items->name, // tambahkan ini
                    'io_no'     => $ios,
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
        $scannedBarcodes = StockModel::where('isp', $isp)->latest()->get();
        return view("backend.transaction.partials.scanned-out", compact('scannedBarcodes'));
    }

    public function stockout_up(Request $request)
    {
        $validated = $request->validate([
            "stocks"    => 'required|array',
            "stocks.*.id"   => 'required|integer|exists:stocks,id',
            "stocks.*.item_code" => 'required|string',
            "stocks.*.qty"  => 'required|numeric|min:1'
        ]);

        foreach ($validated["stocks"] as $stockData) {
            $item = ItemsModel::where("code", $stockData["item_code"])->first();
            if ($item) {
                $itemId = $item->id;
                $latestStock = StockModel::where("item_id", $itemId)->whereNotNull("stock")->orderByDesc("id")->value("stock");
                $latestStockOut = StockModel::where("item_id", $itemId)->whereNotNull("stock_out")->orderByDesc("id")->value("stock_out");
                $latestOnhand = StockModel::where("item_id", $itemId)->whereNotNull("on_hand")->orderByDesc("id")->value("on_hand");
            }
            $stock = StockModel::find($stockData["id"]);
            if ($stock) {
                $stock->qty     = $stockData["qty"];
                $stock->prod_order = $request->prod_order;
                $stock->stock   = ($latestStock ?? 0) - $stockData["qty"];
                $stock->stock_out = ($latestStockOut ?? 0) + $stockData["qty"];
                $stock->on_hand = ($latestOnhand ?? 0) - $stockData["qty"];
                $stock->save();
            }
        }

        return redirect("admin/transaction/stockoutdet/" . $stock->isp)->with("success", "Telah berhasil mengeluarkan item yang sudah di scan");
    }

    public function stockout_det(Request $request, $isp)
    {
        $getRecord = StockModel::where("isp", $isp)->first();
        $getData = StockModel::where("isp", $isp)->orderByDesc("id")->paginate(5);
        $prodOrder = $getRecord->prod_order;
        $getProd = ProductionModel::where("doc_num", $prodOrder)->first();
        $getIo  = $getProd->io_no;

        // set value Prod order
        $stockItemId = $getRecord->item_id;
        $items = ItemsModel::where("id", $stockItemId)->first();
        $getItemPO = ProductionModel::where("doc_num", $getRecord->prod_order)->where("item_code", $items->code)->whereNotNull("qty")->first();
        $getPO = $getItemPO->doc_num;
        $getQtyPo = ProductionModel::where("doc_num", $getRecord->prod_order)->where("item_code", $items->code)->whereNotNull("qty")->sum("qty") ?? 0;
        $getItemStocks = StockModel::where("prod_order", $getRecord->prod_order)->where("item_id", $stockItemId)->whereNotNull("qty")->first();
        $getIdStocks = $getItemStocks->id;
        $getQtyStocks = StockModel::where("prod_order", $getRecord->prod_order)->where("item_id", $stockItemId)->whereNotNull("qty")->sum("qty") ?? 0;
        $resultQty = $getQtyPo - $getQtyStocks;
        if ($resultQty < 0) {
            StockModel::where("id", $getIdStocks)->update(["note" => "Stock Out over qty from Prod Order"]);
            ProductionModel::where("doc_num", $getPO)->update(["status" => 1]);
        } else if ($resultQty == 0) {
            ProductionModel::where("doc_num", $getPO)->update(["status" => 1]);
        } else if ($resultQty > 0) {
            StockModel::where("id", $getIdStocks)->update(["note" => Null]);
            ProductionModel::where("doc_num", $getPO)->update(["status" => 0]);
        }

        // dd($resultQty);
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
        $records = StockModel::where("isp", $isp)->get();
        $getRecord = StockModel::where("isp", $isp)->first();

        // set value Prod order
        $stockItemId = $getRecord->item_id;
        $items = ItemsModel::where("id", $stockItemId)->first();
        $getPo = ProductionModel::where("doc_num", $getRecord->prod_order)->where("item_code", $items->code)->whereNotNull("qty")->get()->value('doc_num');
        ProductionModel::where("doc_num", $getPo)->update(["status" => 0]);

        // delete data
        if ($records->isEmpty()) {
            return redirect("admin/transaction/stockout")->with("error", "Tidak ada data yang dihapus");
        }
        $itemNames = $records->map(function ($records) {
            return $records->item->name ?? "Tidak dikenal";
        })->implode(", ");
        StockModel::where("isp", $isp)->delete();

        return redirect('admin/transaction/stockout')->with('warning', "Membatalkan item(s) {$itemNames}!");
    }

    // Receipt From Prod
    public function receipt_from_prod()
    {
        $latestRFP = RFPModel::whereNotnull("number")->orderByDesc("id")->first();
        $number = $latestRFP && $latestRFP->number ? ((int)$latestRFP->number + 1) : 1;
        $getIos = null;
        $getPos = null;
        return view("backend.transaction.receiptfromprod", compact("getIos", "getPos", "number"));
    }

    public function scan_and_receipt(Request $request)
    {
        $barcode = request()->input("prod_no");
        $prodOrder = ProductionModel::where("prod_no", $barcode)->first();
        if (!$prodOrder) {
            return response()->json([
                "success" => false,
                "message" => "Produk tidak ditemukan untuk barcode: " . $barcode . ". Pastikan produk sesuai dan Scan kembali!"
            ]);
        }
        $iopo = ProductionModel::select("io_no")->where("status", 1)->where("prod_no", $barcode)->distinct()->get();
        $po = ProductionModel::select("doc_num")->where("status", 1)->where("prod_no", $barcode)->distinct()->get();
        $user = Auth::user()->username;

        // save db
        $rfp        = new RFPModel();
        $rfp->number        = trim($request->input("number"));
        $rfp->io    = "-";
        $rfp->prod_order    = 0;
        $rfp->prod_no       = $prodOrder->prod_no;
        $rfp->prod_desc     = $prodOrder->prod_desc;
        $rfp->qty           = 0;
        $rfp->scanned_by    = $user;
        $rfp->save();

        if ($request->expectsJson()) {
            return response()->json([
                "success"   => true,
                "io"        => $iopo,
                "po"        => $po,
                "prod_no"   => $prodOrder->prod_no,
                "message"   => "Produk berhasil di scan!"
            ]);
        }
    }

    public function getScannedRfp($number)
    {
        $scannedBarcodes = RFPModel::where("number", $number)->latest()->get();
        return view("backend.transaction.partials.scanned-rfp", compact("scannedBarcodes"));
    }

    public function rfp_update(Request $request)
    {
        $validated = $request->validate([
            "rfp"   => "required|array",
            "rfp.*.id"  => "required|integer|exists:receipt_from_production,id",
            "rfp.*.qty" => "required|numeric|min:1"
        ]);

        foreach ($validated["rfp"] as $rfpData) {
            $rfp = RFPModel::find($rfpData["id"]);
            if ($rfp) {
                $rfp->io = trim($request->input("io"));
                $rfp->prod_order = trim($request->input("prod_order"));
                $rfp->qty = $rfpData["qty"];
                $rfp->save();
            }
        }

        return redirect("admin/transaction/rfpdetail/" . $request->input("number"))->with("success", "Telah berhasil receipt from production");
    }

    public function rfp_delone($id)
    {
        $records = RFPModel::where("id", $id)->delete();

        if ($records) {
            return response()->json(["success", true]);
        } else {
            return response()->json(["Error", "Item not found"], 404);
        }
    }

    public function rfp_detail($number)
    {
        $getRecord = RFPModel::where("number", $number)->first();
        $RFPQty = RFPModel::where("number", $number)->whereNotNull('qty')->value('qty');
        $getPO = RFPModel::where("number", $number)->whereNotNull('qty')->value('prod_order');
        $getData = RFPModel::where("number", $number)->orderByDesc("id")->paginate(5);
        if ($RFPQty > 0) {
            ProductionModel::where('doc_num', $getPO)->update(['status' => 2]);
        }

        return view("backend.transaction.rfpdetail", compact("getRecord", "getData"));
    }

    public function rfp_delete($number)
    {
        $records = RFPModel::where("number", $number)->get();
        $getPo = RFPModel::where("number", $number)->get()->value('prod_order');
        ProductionModel::where('doc_num', $getPo)->update(['status' => 1]);

        $prods = $records->map(function ($records) {
            return $records->prod_desc ?? "Tidak dikenal";
        })->implode(", ");
        RFPModel::where("number", $number)->delete();

        return redirect("admin/transaction/rfp")->with("warning", "Membatalkan product {$prods}!");
    }
}
