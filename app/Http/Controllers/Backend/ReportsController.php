<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BonDetailsModel;
use App\Models\DeliveryModel;
use App\Models\grpoModel;
use App\Models\MemoDetailModel;
use App\Models\QualityModel;
use App\Services\SapService;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReportsController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function semifg(Request $request)
    {
        // get series based on user
        $getYear = now()->year;
        $year = substr($getYear, -2);

        if (Auth::user()->default_series_prefix === 'SBY') {
            $series = 'SBY-' . $year;

            $getSeries = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'ObjectCode' => 67, 'SeriesName' => $series]);
            if (!empty($getSeries['data'][0]['Series'])) {
                $series = $getSeries['data'][0]['Series'];
            }
        }

        // filter SAP
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 2),
            "U_MEB_NO_IO" => $request->get('io'),
            "DocNum" => $request->get('inv_transfer'),
            "DocStatus" =>  $request->get('status', 'O'),
            "Series" =>  $series ?? $request->get('series'),
        ];

        // get data SAP
        $getInvtf = $this->sap->getInventoryTransfers($param);
        if (empty($getInvtf) || $getInvtf['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // filter 
        $getInvtf['data'] = collect($getInvtf['data'])
            ->map(function ($doc) use ($request) {

                $lines = collect($doc['Lines'] ?? []);

                // 🔍 Filter ItemCode (LIKE)
                if ($request->filled('ItemCode')) {
                    $keyword = strtolower($request->get('ItemCode'));
                    $lines = $lines->filter(
                        fn($line) =>
                        isset($line['ItemCode']) &&
                            str_contains(strtolower($line['ItemCode']), $keyword)
                    );
                }

                // 🔍 Filter ItemName (LIKE)
                if ($request->filled('ItemName')) {
                    $keyword = strtolower($request->get('ItemName'));
                    $lines = $lines->filter(
                        fn($line) =>
                        isset($line['ItemName']) &&
                            str_contains(strtolower($line['ItemName']), $keyword)
                    );
                }

                // 🔥 Filter SEMIFG (SF)
                $lines = $lines->filter(
                    fn($line) =>
                    isset($line['ItemCode']) &&
                        str_starts_with(strtoupper($line['ItemCode']), 'SF')
                );

                $doc['Lines'] = $lines->values()->all();

                return $doc;
            })
            ->filter(fn($doc) => count($doc['Lines']) > 0)
            ->values()
            ->all();

        $currentCount = $getInvtf['total'] ?? count($getInvtf['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view('backend.reports.semifg', [
            'getInvtf'      => $getInvtf['data'],
            'page'        => $getInvtf['page'],
            'limit'       => $getInvtf['limit'],
            'total'       => $getInvtf['total'],
            'totalPages'  => $totalPages,
        ]);
    }

    public function finish_goods(Request $request)
    {
        // get series based on user
        $getYear = now()->year;
        $year = substr($getYear, -2);

        if (Auth::user()->default_series_prefix === 'SBY') {
            $series = 'SBY-' . $year;

            $getSeries = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'ObjectCode' => 202, 'SeriesName' => $series]);
            if (!empty($getSeries['data'][0]['Series'])) {
                $series = $getSeries['data'][0]['Series'];
            }
        }
        // dd(['series' => $series, "getSeries" => $getSeries]);

        // filter SAP
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 20),
            "DocNum" => $request->get('prod_order'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->filled('ItemCode') ? $request->get('ItemCode') : 'SI%',
            "ItemName" =>  $request->get('ItemName'),
            "Series" =>  $series ?? $request->get('series'),
            "Status" =>  $request->get('status', 'Closed'),
        ];

        // get data API
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

        // set pagination
        $currentCount = $getProds['total'] ?? count($getProds['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $user = Auth::user();

        return view('backend.reports.finishgoods', [
            'getProds'      => $getProds['data'] ?? [],
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $getProds['total'],
            'totalPages'  => $totalPages,
            'user'        => $user
        ]);
    }

    public function bon()
    {
        return view("backend.reports.bon");
    }

    public function data()
    {
        $query = BonDetailsModel::with('bon')
            ->withSum(['grpo as receipt_qty' => function ($q) {
                $q->whereColumn('grpo.no_po', 'bon_details.no_po')
                    ->whereColumn('grpo.item_code', 'bon_details.item_code');
            }], 'qty')
            ->withMax(['grpo as latest_grpo_id' => function ($q) {
                $q->whereColumn('grpo.no_po', 'bon_details.no_po')
                    ->whereColumn('grpo.item_code', 'bon_details.item_code');
            }], 'id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no', fn($row) => $row->bon->no ?? '-')
            ->addColumn('date', fn($row) => $row->bon->date ? date('d-m-Y', strtotime($row->bon->date)) : '-')
            ->addColumn('item_name', fn($row) => $row->item_name)
            ->addColumn('uom', fn($row) => $row->uom)
            ->addColumn('qty', fn($row) => $row->qty)
            ->addColumn('remark', fn($row) => $row->remark)
            ->addColumn('receipt_date', function ($row) {
                $latest = $row->grpo->sortByDesc('created_at')->first();
                return $latest ? date('d-m-Y', strtotime($latest->created_at)) : '-';
            })
            ->addColumn('receipt_qty', fn($row) => $row->receipt_qty ?? 0)
            ->addColumn('remain_qty', fn($row) => $row->qty - $row->receipt_qty ?? 0)
            ->addColumn('reason_qty', function ($row) {
                return GrpoModel::find($row->latest_grpo_id)?->reason_qty ?? '-';
            })
            ->make(true);
    }

    public function memo()
    {
        return view("backend.reports.memo");
    }

    public function memo_data()
    {
        $query = MemoDetailModel::with(['memo'])->filter();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no', fn($row) => $row->memo->no ?? '-')
            ->addColumn('date', fn($row) => $row->memo->date ? date('d-m-Y', strtotime($row->memo->date)) : '-')
            ->addColumn('unit', fn($row) => $row->unit)
            ->addColumn('uom', fn($row) => $row->uom)
            ->addColumn('qty', fn($row) => $row->qty)
            // ->addColumn('remark', fn($row) => $row->remark)
            // ->addColumn('receipt_date', function ($row) {
            //     $latest = $row->gr->sortByDesc('created_at')->first();
            //     return $latest ? date('d-m-Y', strtotime($latest->created_at)) : '-';
            // });
            // ->addColumn('receipt_qty', fn($row) => $row->total_grpo_qty)
            // ->addColumn('remain_qty', fn($row) => $row->qty - $row->total_grpo_qty)
            // ->addColumn('reason_qty', function ($row) {
            //     $latest = $row->grpo->sortByDesc('id')->first();
            //     return $latest?->reason_qty ?? '-';)}
            ->make(true);
    }
}
