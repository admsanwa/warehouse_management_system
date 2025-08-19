<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BonModel;
use App\Models\DeliveryModel;
use App\Models\ItemsModel;
use App\Models\ProductionModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Models\StockModel;
use Auth;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // get value
        $getItems       = ItemsModel::whereColumn('stock_min', '>=', 'in_stock')->get()->unique('code');
        $getQuality     = QualityModel::whereNotNull("result")->get()->unique('io');
        $getDelivery    = DeliveryModel::whereNotNull("status")->get()->unique('io');
        $getProd        = ProductionModel::where("status", 1)->get();

        // count value
        $needBuy        = $getItems->count();
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
        $user       = Auth::user();
        $hasPending = BonModel::whereDoesntHave("signBon", function ($q) {
            $q->where('sign', 1);
        })->exists();

        if ($user->nik === "06067" && $hasPending || $user->nik === "08517" && $hasPending || $user->nik === "250071" && $hasPending) {
            session()->flash('bonPending', true);
        }

        return view('backend.dashboard.list', compact('needBuy', 'afterCheck', 'deliveryStatus', 'prodRelease', 'purchaseOrder', 'goodIssued', 'goodReceipt', 'rfp'));
    }

    public function clearBonNotif()
    {
        session()->forget('bonPending');
        return redirect('admin/production/listbon');
    }

    public function min_stock(Request $request)
    {
        $getRecord = ItemsModel::getRecordThree($request);

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

        return view("backend.dashboard.aftercheck", compact('getRecord'));
    }

    public function deliv_status(Request $request)
    {
        $getRecord = ProductionModel::with(['delivery', 'quality'])
            ->where('status', 2)
            ->whereHas('quality', function ($q) {
                $q->where('result', 1);
            })
            ->whereHas("delivery", function ($q) {
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
        $getData    = ProductionModel::withCount("stocks")->where('status', 1)->orderBy("id", "desc")->paginate(10);
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
        return view('backend.production.list', compact('getData', 'productionSummary'));
    }

    public function grpo(Request $request)
    {
        $getRecord      = PurchasingModel::with("po_details")->get()->values();
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

        return view("backend.dashboard.goodreceiptpo", compact('getRecord', 'getPagination', 'purchasingSummary'));
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

        return view("backend.dashboard.goodreceiptpo", compact('getRecord', 'getPagination', 'purchasingSummary'));
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
