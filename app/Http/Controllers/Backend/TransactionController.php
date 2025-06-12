<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ItemsModel;
use App\Models\PurchasingModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use illuminate\support\facades\Auth;

class TransactionController extends Controller
{
    public function stock_in(Request $request)
    {
        $latestStock  = StockModel::orderBy('id', 'desc')->first();
        $grpo = $latestStock->grpo ? ($latestStock->grpo + 1) : 1;
        return view('backend.transaction.stockin', compact('grpo'));
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
        $user           = Auth::user()->id;

        // save db
        $stock              = new StockModel();
        $stock->grpo        = trim($request->input("grpo"));
        $stock->item_id     = $items->id;
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
        $pos = PurchasingModel::select("no_po")->where("status", "Open")->distinct()->get();
        return view("backend.transaction.partials.scanned", compact('scannedBarcodes', 'pos'));
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
                $latestStockin = StockModel::where("item_id", $itemId)->whereNotNull("stock_in")->orderByDesc("id")->value("stock_in");
                $latestOnhand = StockModel::where("item_id", $itemId)->whereNotNull("on_hand")->orderByDesc("id")->value("on_hand");
            }
            $stock = StockModel::find($stockData["id"]);
            if ($stock) {
                $stock->no_po   = trim($request->nopo);
                $stock->qty     = $stockData["qty"];
                $stock->stock_in    = ($latestStockin ?? 0) + $stockData["qty"];
                $stock->on_hand     = ($latestOnhand ?? 0) + $stockData["qty"];
                $stock->save();
            }
        }

        return redirect('admin/transaction/stockdet/' . $request->input("grpo"))->with('success', 'Telah berhasil menambahkan item yang sudah di scan');
    }

    public function stock_del($id)
    {
        $recordDelete = StockModel::find($id);
        $recordDelete->delete();
        $itemName = $recordDelete->item->name;

        return redirect('admin/transaction/stockin')->with('error', "Membatalkan item {$itemName}");
    }

    public function stock_det(Request $request, $grpo)
    {
        $getRecord = StockModel::where("grpo", $grpo)->first();
        $getData = StockModel::where('grpo', 'like', "%{$grpo}%")->orderBy('id', 'desc')->paginate(5);
        return view("backend.transaction.stockdet", compact('getRecord', 'getData'));
    }

    public function stock_out()
    {
        return view('backend.transaction.stockout');
    }
}
