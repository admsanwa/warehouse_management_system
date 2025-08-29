<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BonModel;
use App\Models\DeliveryModel;
use App\Models\ItemsModel;
use App\Models\MemoModel;
use App\Models\ProductionModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Models\StockModel;
use Auth;
use DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // get value
        $getQuality     = QualityModel::whereNot("result", 3)->get()->unique('io');
        $getDelivery    = DeliveryModel::whereNotNull("status")->get()->unique('io');
        $getProd        = ProductionModel::where("status", "Released")->get();

        // count value
        $latestStock = DB::table('stocks as s1')
            ->select('s1.item_code', 's1.stock_in', 's1.stock_out', 's1.id as stock_id')
            ->whereRaw('s1.id = (SELECT MAX(s2.id) FROM stocks s2 WHERE s2.item_code = s1.item_code)');

        $needBuy = ItemsModel::leftJoinSub($latestStock, 'latest', function ($join) {
            $join->on('latest.item_code', '=', 'items.code');
        })
            ->select('items.id', 'items.code', 'items.name', 'items.uom', 'items.stock_min', 'items.in_stock', 'items.updated_at')
            ->selectRaw('COALESCE(latest.stock_in,0) as last_in')
            ->selectRaw('COALESCE(latest.stock_out,0) as last_out')
            ->selectRaw('latest.stock_id')
            ->whereRaw('items.stock_min >= (items.in_stock + (COALESCE(latest.stock_in,0) - COALESCE(latest.stock_out,0)))')
            ->orderByDesc('latest.stock_id')
            ->count();
        $afterCheck     = $getQuality->count();
        $deliveryStatus = $getDelivery->count();
        $prodRelease    = $getProd->count();
        $purchaseOrder  = PurchasingModel::where("status", "Open")->whereHas('po_details', function ($query) {
            $query->where('item_code', 'NOT LIKE', '%Maklon%');
        })->count();
        $goodIssued     = PurchasingModel::where("status", "GI")->whereHas('po_details', function ($query) {
            $query->where('item_code', 'LIKE', '%Maklon%');
        })->count();
        $goodReceipt    = PurchasingModel::where("status", "GR")->whereHas('po_details', function ($query) {
            $query->where('item_code', 'LIKE', '%Maklon%');
        })->count();
        $rfp            = ProductionModel::with('qualityTwo')
            ->whereHas('qualityTwo', function ($q) {
                $q->where('result', 1);
            })
            ->addSelect([
                'latest_quality_id' => QualityModel::select('id')
                    ->whereColumn('quality.io', 'production_order.io_no')
                    ->orderByDesc('id')
                    ->limit(1)
            ])
            ->where('prod_no', '$rfpProdNo')
            ->orderByDesc('latest_quality_id')
            ->filter($request) // <-- using the scope
            ->count();

        // notif modal
        $user           = Auth::user();
        $hasPending     = BonModel::whereDoesntHave("signBon", function ($q) use ($user) {
            $q->where('sign', 1)->where('nik', $user->nik);
        })->exists();
        $hasPendingMemo = MemoModel::whereDoesntHave("sign", function ($q) {
            $q->where('sign', 1);
        })->exists();
        $hasQcPending   = QualityModel::where('result', 3)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('quality')
                    ->groupBy('io');
            })
            ->exists();

        $hasQcPendingProd   = QualityModel::where('result', 4)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('quality')
                    ->groupBy('io');
            })
            ->exists();

        if (in_array($user->nik, ["06067", "08517", "250071"]) && $hasPending) {
            session()->flash('bonPending', true);
        }
        if ($user->nik === "06067" && $hasPendingMemo) {
            session()->flash('memoPending', true);
        }
        if (in_array($user->nik, ["06067"]) && $hasQcPending) {
            session()->flash('qcPending', true);
        }
        if ($user->nik === "12345" && $hasQcPendingProd) {
            session()->flash('qcPendingProd', true);
        }

        return view('backend.dashboard.list', compact('needBuy', 'afterCheck', 'deliveryStatus', 'prodRelease', 'purchaseOrder', 'goodIssued', 'goodReceipt', 'rfp', 'user'));
    }

    public function clearBonNotif()
    {
        session()->forget('bonPending');
        return redirect('admin/production/listbon');
    }

    public function clearMemoNotif()
    {
        session()->forget('memoPending');
        return redirect('admin/production/listmemo');
    }

    public function clearQcNotif()
    {
        session()->forget('qcPending');
        session()->forget('qcPendingProd');
        return redirect('admin/quality/list');
    }

    public function min_stock(Request $request)
    {
        $latestStock = DB::table('stocks as s1')
            ->select('s1.item_code', 's1.stock_in', 's1.stock_out', 's1.id as stock_id')
            ->whereRaw('s1.id = (SELECT MAX(s2.id) FROM stocks s2 WHERE s2.item_code = s1.item_code)');

        $getRecord = ItemsModel::leftJoinSub($latestStock, 'latest', function ($join) {
            $join->on('latest.item_code', '=', 'items.code');
        })
            ->select('items.id', 'items.code', 'items.name', 'items.uom', 'items.stock_min', 'items.in_stock', 'items.updated_at')
            ->selectRaw('COALESCE(latest.stock_in,0) as last_in')
            ->selectRaw('COALESCE(latest.stock_out,0) as last_out')
            ->selectRaw('latest.stock_id')
            ->whereRaw('items.stock_min >= (items.in_stock + (COALESCE(latest.stock_in,0) - COALESCE(latest.stock_out,0)))')
            ->orderByDesc('latest.stock_id')
            ->paginate(10);

        return view("backend.dashboard.minstock", compact('getRecord'));
    }

    public function after_check(Request $request)
    {

        $getRecord = ProductionModel::with('qualityTwo')
            ->whereHas('qualityTwo', function ($q) {
                $q->whereNotNull('result');
            })
            ->filter($request) // <-- using the scope
            ->orderBy('id', 'desc')
            ->get()
            ->unique('io_no');
        $user = Auth::user();

        return view("backend.dashboard.aftercheck", compact('getRecord', 'user'));
    }

    public function deliv_status(Request $request)
    {
        $getRecord = ProductionModel::with(['delivery', 'quality'])
            ->where('status', 'Closed')
            ->whereHas('quality', function ($q) {
                $q->where('result', 1);
            })
            ->whereHas('delivery', function ($q) {
                $q->whereNotNull('status');
            })
            ->filter($request)
            ->orderBy('id', 'desc')
            ->get();
        // dd($request);

        return view("backend.dashboard.delivstatus", compact("getRecord"));
    }

    public function prod_release(Request $request)
    {
        $getData    = ProductionModel::getRecord($request)->withCount("stocks")->where('status', 'Released')->orderBy("id", "desc")->paginate(10);
        $getRecord  = ProductionOrderDetailsModel::with("stocks")->get()->unique("doc_num")->values();
        $getSeries  = ProductionModel::getRecord($request)->get();
        $user       = Auth::user();

        $productionSummary = [];
        foreach ($getRecord as $record) {
            $po = $record->doc_num;

            $productionQty  = ProductionOrderDetailsModel::where("doc_num", $po)->sum("qty");
            $stockOutQty    = StockModel::where("prod_order", $po)->sum("qty");

            $productionSummary[$po] = [
                "remain" => $productionQty - $stockOutQty
            ];
        }
        return view('backend.dashboard.prodrelease', compact('getData', 'productionSummary', 'getSeries', 'user'));
    }

    public function grpo(Request $request)
    {
        $getRecord      = PurchasingModel::with("po_details")->get()->values();
        $user           = Auth::user();
        // $getRecord      = PurchaseOrderDetailsModel::with("stocks")->get()->unique("nopo")->values();
        $getPagination = PurchasingModel::where('status', 'Open')
            ->whereHas('po_details', function ($query) {
                $query->where('item_code', 'not like', '%Maklon%');
            })
            ->paginate(10); // example pagination


        $purchasingSummary = [];
        foreach ($getRecord as $record) {
            $po = $record->no_po;
            $purchaseQty = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $stockInQty = StockModel::where("no_po", $po)->sum("qty");

            $purchasingSummary[$po] = [
                'remain' => $purchaseQty - $stockInQty
            ];
        }

        return view("backend.dashboard.goodreceiptpo", compact('getRecord', 'getPagination', 'purchasingSummary', 'user'));
    }

    public function good_issued(Request $request)
    {
        $getRecord      = PurchasingModel::with("po_details")->get()->values();
        // $getRecord      = PurchaseOrderDetailsModel::with("stocks")->get()->unique("nopo")->values();
        $getPagination = PurchasingModel::where('status', 'GI')
            ->whereHas('po_details', function ($query) {
                $query->where('item_code', 'like', '%Maklon%');
            })
            ->paginate(10); // example pagination


        $purchasingSummary = [];
        foreach ($getRecord as $record) {
            $po = $record->no_po;
            $purchaseQty = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $stockInQty = StockModel::where("no_po", $po)->sum("qty");

            $purchasingSummary[$po] = [
                'remain' => $purchaseQty - $stockInQty
            ];
        }

        return view("backend.dashboard.goodreceiptpo", compact('getRecord', 'getPagination', 'purchasingSummary'));
    }

    public function good_receipt(Request $request)
    {
        $getRecord      = PurchasingModel::with("po_details")->get()->values();
        $user           = Auth::user();
        // $getRecord      = PurchaseOrderDetailsModel::with("stocks")->get()->unique("nopo")->values();
        $getPagination = PurchasingModel::where('status', 'GR')
            ->whereHas('po_details', function ($query) {
                $query->where('item_code', 'like', '%Maklon%');
            })
            ->paginate(10); // example pagination


        $purchasingSummary = [];
        foreach ($getRecord as $record) {
            $po = $record->no_po;
            $purchaseQty = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $stockInQty = StockModel::where("no_po", $po)->sum("qty");

            $purchasingSummary[$po] = [
                'remain' => $purchaseQty - $stockInQty
            ];
        }

        return view("backend.dashboard.goodreceiptpo", compact('getRecord', 'getPagination', 'purchasingSummary', 'user'));
    }

    public function receipt_from_prod(Request $request)
    {
        $rfpProdNo  = RFPModel::whereNotNull('prod_no')->pluck('prod_no');
        $getRecord  = ProductionModel::with('qualityTwo')
            ->whereHas('qualityTwo', function ($q) {
                $q->where('result', 1);
            })
            ->addSelect([
                'latest_quality_id' => QualityModel::select('id')
                    ->whereColumn('quality.io', 'production_order.io_no')
                    ->orderByDesc('id')
                    ->limit(1)
            ])
            ->where('prod_no', '$rfpProdNo')
            ->orderByDesc('latest_quality_id')
            ->filter($request) // <-- using the scope
            ->get();
        // dd($getRecord->first()->quality);

        return view("backend.dashboard.receiptfromprod", compact("getRecord"));
    }
}
