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
use App\Services\SapService;

use App\Models\ProgressTrackingModel;
use App\Helpers\ProgressHelper;

class DashboardController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }
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

        $param = [
            "page"  => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 50),
            'DocStatus' => 'O',
            "DocDate" => date("Y")
        ];

        $getInvtf = $this->sap->getInventoryTransfers($param);

        if (empty($getInvtf) || $getInvtf['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $invtf = collect($getInvtf['data'])
            ->map(function ($row) {
                $prjCode = $row['U_MEB_Project_Code'] ?? null;

                if ($prjCode) {
                    $getProject = $this->sap->getProjects([
                        'limit'   => 1,
                        'PrjCode' => $prjCode
                    ]);

                    if (!empty($getProject) && $getProject['success'] === true && !empty($getProject['data'])) {
                        // $row['project'] = $getProject['data'][0];
                        $row['PrjName'] = $getProject['data'][0]['PrjName'] ?? null;
                    } else {
                        $row['PrjName'] = null;
                    }
                } else {
                    $row['PrjName'] = null;
                }

                return $row;
            });


        $currentCount = $getInvtf['total'] ?? count($getInvtf['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total = $getInvtf['total'];
        $page = $getInvtf['page'];
        $limit = $param['limit'];
        return view('backend.dashboard.list', compact('needBuy', 'afterCheck', 'deliveryStatus', 'prodRelease', 'purchaseOrder', 'goodIssued', 'goodReceipt', 'rfp', 'user', 'invtf', 'total', 'limit', 'page', 'totalPages'));
    }

    public function dashboard_invtf(Request $request)
    {
        $getRap = $this->sap->getSeries([
            'limit'      => 1,
            'ObjectCode' => '67',
            'SeriesName' => "BKS-" . date('y')
        ]);
        $seriesDefault = $getRap['data'][0]['Series'];
        $param = [
            'U_MEB_NO_IO' => $request->get('U_MEB_NO_IO'),
            'page'        => (int) $request->get('page', 1),
            'limit'       => 10,
            'Series'      => $seriesDefault
        ];

        $getInvtf = $this->sap->getInventoryTransfers($param);

        if (empty($getInvtf) || ($getInvtf['success'] ?? false) !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $data = collect($getInvtf['data']);

        $noIOs        = $data->pluck('U_MEB_NO_IO')->filter()->unique()->toArray();
        $progressData = ProgressTrackingModel::whereIn('no_io', $noIOs)->get()->keyBy('no_io');

        // Loop data + ambil project & series per item
        $invtf = $data->map(function ($item) use ($progressData) {
            $noIO    = $item['U_MEB_NO_IO'] ?? null;
            // pastikan key sesuai data SAP
            $project = $item['U_MEB_Project_Code']
                ?? $item['ProjectCode']
                ?? $item['PrjCode']
                ?? null;
            $series  = $item['Series'] ?? null;

            // Progress
            $progress = $progressData[$noIO] ?? null;
            $item['progress'] = [
                'current_stage'    => $progress->current_stage ?? null,
                'progress_percent' => $progress->progress_percent ?? 0,
            ];

            // Ambil nama project (langsung ke API)
            if ($project) {
                $respProject = $this->sap->getProjects([
                    'limit'   => 1,
                    'PrjCode' => $project
                ]);

                $item['PrjName'] = (!empty($respProject)
                    && ($respProject['success'] ?? false) === true
                    && !empty($respProject['data']))
                    ? ($respProject['data'][0]['PrjName'] ?? '-')
                    : '-';
            } else {
                $item['PrjName'] = '-';
            }

            // Ambil nama series (langsung ke API)
            if ($series) {
                $respSeries = $this->sap->getSeries([
                    'limit'  => 1,
                    'Series' => $series
                ]);

                $item['SeriesName'] = (!empty($respSeries)
                    && ($respSeries['success'] ?? false) === true
                    && !empty($respSeries['data']))
                    ? ($respSeries['data'][0]['SeriesName'] ?? '-')
                    : '-';
            } else {
                $item['SeriesName'] = '-';
            }

            return $item;
        });

        $currentCount = $getInvtf['total'] ?? count($getInvtf['data'] ?? []);
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = $getInvtf['total'] ?? count($invtf);
        $page         = $getInvtf['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view('backend.dashboard.inventory-list', compact('invtf', 'total', 'limit', 'page', 'totalPages'));
    }

    public function syncInventoryProgress(Request $request)
    {
        $params = [
            'page'  => (int) $request->get('page', 1),
            'limit' => 10,
        ];
        if ($request->filled('U_MEB_NO_IO')) {
            $params['U_MEB_NO_IO'] = $request->get('U_MEB_NO_IO');
        }
        if ($request->filled('Series')) {
            $params['Series'] = $request->get('series');
        }

        $getInvtf = $this->sap->getInventoryTransfers($params);
        $data = $getInvtf['data'] ?? [];

        $grouped = collect($data)->groupBy('U_MEB_NO_IO');

        foreach ($grouped as $noIO => $records) {
            $latest = $records->sortByDesc('DocDate')->first();

            $stage    = ProgressHelper::detectStage($latest);
            $progress = ProgressHelper::progressPercent($stage);

            // Project name
            $projectName = null;
            if (!empty($latest['U_MEB_Project_Code'])) {
                $respProject = $this->sap->getProjects(['PrjCode' => $latest['U_MEB_Project_Code']]);
                $projectName = $respProject['data'][0]['PrjName'] ?? null;
            }

            // Series name
            $seriesName = null;
            if (!empty($latest['Series'])) {
                $respSeries = $this->sap->getSeries(['Series' => $latest['Series']]);
                $seriesName = $respSeries['data'][0]['SeriesName'] ?? null;
            }

            ProgressTrackingModel::updateOrCreate(
                ['no_io' => $noIO],
                [
                    'project_code'      => $latest['U_MEB_Project_Code'] ?? null,
                    'project_name'      => $projectName,
                    'prod_order_no'     => $latest['U_MEB_No_Prod_Order'] ?? null,
                    'series'            => $latest['Series'] ?? null,
                    'series_name'       => $seriesName,
                    'current_stage'     => $stage,
                    'progress_percent'  => $progress
                ]
            );
        }

        return response()->json(['message' => 'Progress synced', 'debug' => [
            'stage' => $stage,
            'progress' => $progress,
            'getInvtf' => $getInvtf
        ]]);
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
