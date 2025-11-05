<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BonModel;
use App\Models\DeliveryModel;
use App\Models\ItemsModel;
use App\Models\MemoModel;
use App\Models\ProductionModel;
use App\Models\goodissueModel;
use App\Models\goodreceiptModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Models\IFPModel;
use App\Models\StockModel;
use Auth;
use DB;
use App\Services\SapService;

use App\Models\ProgressTrackingModel;
use App\Helpers\ProgressHelper;
use App\Models\grpoModel;

class DashboardController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function dashboard(Request $request)
    {
        // count value
        $getItems = $this->sap->getStockItems([
            'Status' => 1,
        ]);

        $needBuy = $getItems['total'];
        $afterCheck     = QualityModel::count();
        $deliveryStatus = DeliveryModel::count();
        // Rumus hitung Issue For Production & Receipt From Production
        $getProdRelease = $this->sap->getProductionOrders([
            'Status' => 'Released',
            'limit' => 100,
        ]);
        $filterIFP = $this->filterIssueForProduction($getProdRelease['data']);
        $filterRFP = $this->filterReceiptForProduction($getProdRelease['data']);
        $prodRelease    = count($filterIFP);
        // Rumus hitung GI, GR & GRPO
        $getPurchaseOrders =    $this->sap->getPurchaseOrders(['page' => 1, 'limit' => 100, 'DocStatus' => 'Open']);
        $filteredGI = $this->filterGoodIssueData($getPurchaseOrders['data']);
        $filteredGR = $this->filterGoodReceiptData($getPurchaseOrders['data']);
        $filteredGRPO = $this->filterGRPOData($getPurchaseOrders['data']);

        $grpo = count($filteredGRPO);
        $goodIssued     = count($filteredGI);
        $goodReceipt = count($filteredGR);
        $rfp            = count($filterRFP);
        $memos = MemoModel::count();
        $bons = BonModel::count();

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
        $hasGRPOPending = grpoModel::where('is_temp', 0)->exists();
        $hasGRPending = goodreceiptModel::where('is_temp', 0)->exists();
        $hasDeliveryPending = DeliveryModel::where('is_temp', 0)
            ->orWhereNull('status')
            ->orWhere('status', '')
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
        if (in_array($user->department, ["Purchasing", "PPIC"]) && $hasGRPOPending) {
            session()->flash('grpoPending', true);
        }
        if (in_array($user->department, ["Purchasing", "PPIC"]) && $hasGRPending) {
            session()->flash('grPending', true);
        }
        if (in_array($user->department, ["Procurement, Installation and Delivery"]) && $hasDeliveryPending) {
            session()->flash('deliveryPending', true);
        }

        return view('backend.dashboard.list', compact(
            'needBuy',
            'afterCheck',
            'deliveryStatus',
            'prodRelease',
            'grpo',
            'goodIssued',
            'goodReceipt',
            'rfp',
            'memos',
            'bons',
            'user'
        ));
    }

    public function dashboard_plan(Request $request)
    {
        $param = [
            'U_MEB_NO_IO' => $request->get('U_MEB_NO_IO'),
            'U_MEB_Sales_Type' => '01', //Default
            'page'        => (int) $request->get('page', 1),
            'Series'      => $request->get('series'),
            'limit'       => 20,
        ];

        $getSO = $this->sap->getSalesOrders($param);

        if (empty($getSO) || ($getSO['success'] ?? false) !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $data = collect($getSO['data'])
            ->map(function ($row) {
                // Ambil project name
                if (!empty($row['Lines'][0]['Project'])) {
                    $getProject = $this->sap->getProjects([
                        'PrjCode' => $row['Lines'][0]['Project'],
                        'limit'   => 1
                    ]);
                    if (!empty($getProject['data'][0]['PrjName'])) {
                        $row['ProjectName'] = $getProject['data'][0]['PrjName'];
                    }
                }

                // Ambil series name
                if (!empty($row['Series'])) {
                    $getSeries = $this->sap->getSeries([
                        'Series' => $row['Series'],
                        'limit'  => 1
                    ]);
                    if (!empty($getSeries['data'][0]['SeriesName'])) {
                        $row['SeriesName'] = $getSeries['data'][0]['SeriesName'];
                    }
                }

                // Ambil transfer terbaru
                $get_invtf = $this->sap->getInventoryTransfers([
                    'U_MEB_NO_IO' => $row['U_MEB_NO_IO'],
                    'U_MEB_NO_SO' => $row['DocNum'],
                    'limit'       => 50,
                ]);

                $latestTransfer = collect($get_invtf['data'])
                    ->sortByDesc(fn($x) => $x['DocEntry'] ?? '')
                    ->first();

                $progressData = ProgressHelper::detectStage($latestTransfer);

                $row['FromWhsCode']     = $latestTransfer['FromWhsCode'] ?? null;
                $row['ToWhsCode']       = $latestTransfer['ToWhsCode'] ?? null;
                $row['Stage']           = $progressData['stage'] ?? null;
                $row['CurrentStatus']   = $progressData['status'] ?? null;
                $row['ProgressPercent'] = $progressData['progress_percent'] ?? 0;

                return $row;
            })
            ->filter();


        // ✅ Filter by Status dari request
        if ($request->filled('status')) {
            $data = $data->filter(function ($row) use ($request) {
                return ($row['CurrentStatus'] ?? null) === $request->status;
            });
        }
        // hanya SO dengan transfer terbaru

        $currentCount = $getSO['total'] ?? count($data);
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total = $data->count();
        $page         = $getSO['page'] ?? $param['page'];
        $limit        = $param['limit'];
        $statuses = ProgressHelper::getStatusList();
        return view('backend.dashboard.plan-list', [
            'purchase_orders' => $data,
            'total'           => $total,
            'limit'           => $limit,
            'page'            => $page,
            'totalPages'      => $totalPages,
            'seriesName'      => null,
            'statuses'        => $statuses,
        ]);
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

    public function clearGrpoNotif()
    {
        session()->forget('grpoPending');
        return redirect('admin/listtransaction/stockin');
    }

    public function clearGrNotif()
    {
        session()->forget('grPending');
        return redirect('admin/listtransaction/goodreceipt');
    }

    public function clearDeliveryNotif()
    {
        session()->forget('deliveryPending');
        return redirect('admin/delivery/list');
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
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => 100,
            "DueDate" => formatDateSlash($request->get('date')),
            "DocNum" => $request->get('doc_num'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
            "Status" =>  'Released',
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // Filter ulang kalau ada Series
        if (!empty($param['Series']) && !empty($getProds['data'])) {
            $getProds['data'] = collect($getProds['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            // update total sesuai hasil filter
            $getProds['total'] = count($getProds['data']);
        }

        $filtered = $this->filterIssueForProduction($getProds['data']);

        $currentCount = $getProds['total'] ?? count($filtered); // total asli sebelum filter
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = count($filtered);
        $page         = $getProds['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view('backend.dashboard.prodrelease', [
            'getProds'      => $filtered,
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'totalPages'  => $totalPages,
        ]);
    }

    public function grpo(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            'limit' => 100,
            "DocStatus" => $request->get('docStatus', 'Open'),
            "DocNum" => $request->get('docNum'),
            "DocDueDate" => formatDateSlash($request->get('DocDueDate')),
            "CardName" =>  $request->get('cardName'),
            "DocDate" => formatDateSlash($request->get('docDate')),
            "Series" =>  $request->get('series')
        ];

        $orders = $this->sap->getPurchaseOrders($param);
        if (empty($orders) || ($orders['success'] ?? false) !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();
            $orders['total'] = count($orders['data']);
        }

        $filtered = $this->filterGRPOData($orders['data']);

        $currentCount = $orders['total'] ?? count($filtered); // total asli sebelum filter
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = count($filtered);
        $page         = $orders['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view("backend.dashboard.goodreceiptpo", [
            'orders'      => $filtered,
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'statuses'    => ['Open', 'Close'],
        ]);
    }

    public function good_issued(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            'limit' => 100,
            "DocStatus" => $request->get('docStatus', 'Open'),
            "DocNum" => $request->get('docNum'),
            "DocDueDate" => formatDateSlash($request->get('DocDueDate')),
            "CardName" =>  $request->get('cardName'),
            "DocDate" => formatDateSlash($request->get('docDate')),
            "Series" =>  $request->get('series')
        ];

        $orders = $this->sap->getPurchaseOrders($param);
        if (empty($orders) || ($orders['success'] ?? false) !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();
            $orders['total'] = count($orders['data']);
        }

        $filtered = $this->filterGoodIssueData($orders['data']);

        $currentCount = $orders['total'] ?? count($filtered); // total asli sebelum filter
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = count($filtered);
        $page         = $orders['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view('backend.dashboard.goodissue', [
            'orders'      => $filtered,
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'statuses'    => ['Open', 'Close'],
        ]);
    }

    public function good_receipt(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            'limit' => 100,
            "DocStatus" => $request->get('docStatus', 'Open'),
            "DocNum" => $request->get('docNum'),
            "DocDueDate" => formatDateSlash($request->get('DocDueDate')),
            "CardName" =>  $request->get('cardName'),
            "DocDate" => formatDateSlash($request->get('docDate')),
            "Series" =>  $request->get('series')
        ];

        $orders = $this->sap->getPurchaseOrders($param);
        if (empty($orders) || ($orders['success'] ?? false) !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();
            $orders['total'] = count($orders['data']);
        }

        $filtered = $this->filterGoodReceiptData($orders['data']);

        $currentCount = $orders['total'] ?? count($filtered); // total asli sebelum filter
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = count($filtered);
        $page         = $orders['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view('backend.dashboard.goodreceipt', [
            'orders'      => $filtered,
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'totalPages'  => $totalPages,
        ]);
    }

    public function good_receiptpo(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            'limit' => 50,
            "DocStatus" => $request->get('docStatus', 'Open'),
            "DocNum" => $request->get('docNum'),
            "DocDueDate" => formatDateSlash($request->get('DocDueDate')),
            "CardName" =>  $request->get('cardName'),
            "DocDate" => formatDateSlash($request->get('docDate')),
            "Series" =>  $request->get('series')
        ];

        $orders = $this->sap->getPurchaseOrders($param);
        if (empty($orders) || ($orders['success'] ?? false) !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        if (!empty($param['Series']) && !empty($orders['data'])) {
            $orders['data'] = collect($orders['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();
            $orders['total'] = count($orders['data']);
        }

        $filtered = $this->filterGRPOData($orders['data']);

        $currentCount = $orders['total'] ?? count($filtered); // total asli sebelum filter
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = count($filtered);
        $page         = $orders['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view('backend.dashboard.goodreceipt', [
            'orders'      => $filtered,
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'totalPages'  => $totalPages,
        ]);
    }

    public function receipt_from_prod(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => 100,
            "DueDate" => formatDateSlash($request->get('date')),
            "DocNum" => $request->get('doc_num'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
            "Status" =>  'Released',
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // Filter ulang kalau ada Series
        if (!empty($param['Series']) && !empty($getProds['data'])) {
            $getProds['data'] = collect($getProds['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            // update total sesuai hasil filter
            $getProds['total'] = count($getProds['data']);
        }

        $filtered = $this->filterReceiptForProduction($getProds['data']);

        $currentCount = $getProds['total'] ?? count($filtered); // total asli sebelum filter
        $totalPages   = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $total        = count($filtered);
        $page         = $getProds['page'] ?? $param['page'];
        $limit        = $param['limit'];

        return view('backend.dashboard.receiptfromprod', [
            'getProds'      => $filtered,
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'totalPages'  => $totalPages,
        ]);
    }

    private function filterGoodIssueData(array $orders, bool $onlyMaklon = true): array
    {
        return collect($orders)->filter(function ($order) use ($onlyMaklon) {
            $lines = $order['Lines'] ?? [];
            if (empty($lines)) return false;

            // cek maklon
            $isMaklon = false;
            if (!empty($order['U_MEB_PONo_Maklon'])) {
                $isMaklon = true;
            } else {
                foreach ($lines as $l) {
                    if (stripos($l['Dscription'], 'Maklon') !== false) {
                        $isMaklon = true;
                        break;
                    }
                }
            }

            if ($onlyMaklon && !$isMaklon) return false;

            // cek open qty
            $totalOpenQty = 0;
            foreach ($lines as $l) {
                $totalOpenQty += (float) $l['OpenQty'];
            }

            return $totalOpenQty > 0;
        })->values()->all();
    }

    private function filterGoodReceiptData(array $orders): array
    {
        return collect($orders)->filter(function ($order) {
            $lines = $order['Lines'] ?? [];
            if (empty($lines)) return false;

            // cek kalau salah satu ItemCode mengandung 'Maklon'
            $hasMaklon = collect($lines)->contains(function ($l) {
                return str_contains($l['ItemCode'] ?? '', 'Maklon');
            });

            // total open qty harus > 0 juga
            $totalOpenQty = collect($lines)->sum(function ($l) {
                return (float) ($l['OpenQty'] ?? 0);
            });

            return $hasMaklon && $totalOpenQty > 0;
        })->values()->all();
    }

    private function filterGRPOData(array $orders): array
    {
        return collect($orders)->filter(function ($order) {
            $lines = $order['Lines'] ?? [];
            if (empty($lines)) return false;

            // cek apakah ada 'Maklon' di salah satu ItemCode
            $hasMaklon = collect($lines)->contains(function ($l) {
                return str_contains(strtolower($l['ItemCode'] ?? ''), 'maklon');
            });

            // total open qty
            $totalOpenQty = collect($lines)->sum(function ($l) {
                return (float) ($l['OpenQty'] ?? 0);
            });

            // GRPO = tidak ada Maklon, open qty > 0
            return !$hasMaklon && $totalOpenQty > 0;
        })->values()->all();
    }

    private function filterIssueForProduction(array $orders): array
    {
        return collect($orders)->filter(function ($order) {
            $lines = $order['Lines'] ?? [];
            if (empty($lines)) return false;

            // hanya line yang diawali "RM"
            $rmLines = collect($lines)->filter(fn($l) => str_starts_with($l['ItemCode'] ?? '', 'RM'));

            $totalPlannedQty = $rmLines->sum(fn($l) => (float) ($l['PlannedQty'] ?? 0));
            $totalIssuedQty  = $rmLines->sum(fn($l) => (float) ($l['IssuedQty'] ?? 0));

            return $totalIssuedQty < $totalPlannedQty;
        })->values()->all();
    }


    private function filterReceiptForProduction(array $orders): array
    {
        return collect($orders)->filter(function ($order) {
            $lines = $order['Lines'] ?? [];
            if (empty($lines)) return false;

            // hanya line yang diawali "RM"
            $rmLines = collect($lines)->filter(fn($l) => str_starts_with($l['ItemCode'] ?? '', 'RM'));

            $totalPlannedQty = $rmLines->sum(fn($l) => (float) ($l['PlannedQty'] ?? 0));
            $totalIssuedQty  = $rmLines->sum(fn($l) => (float) ($l['IssuedQty'] ?? 0));

            // Hitung total receipt dari header
            $plannedHeader = (float) ($order['PlannedQty'] ?? 0);
            $completeQty   = (float) ($order['CmpltQty'] ?? 0);
            $rejectQty     = (float) ($order['RjctQty'] ?? 0);
            $totalReceipt  = $completeQty + $rejectQty;

            // Return true jika semua line RM sudah issued dan total receipt < planned header
            return ($totalIssuedQty >= $totalPlannedQty) && ($totalReceipt < $plannedHeader);
        })->values()->all();
    }
}
