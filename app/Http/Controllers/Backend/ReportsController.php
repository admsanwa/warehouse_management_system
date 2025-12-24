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
        $getRecord = DeliveryModel::getRecord($request)
            ->where('prod_no', 'LIKE', 'SF%')
            ->paginate(10);

        $firstRecord = $getRecord->first();

        $series = [];
        if ($firstRecord) {
            $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $firstRecord->series]) ?? '0';
            $series =   Arr::get($get_series, 'data.0', []);
        }

        return view('backend.reports.semifg', compact('getRecord', 'series'));
    }

    public function finish_goods(Request $request)
    {
        $getRecord = DeliveryModel::getRecord($request)
            ->where('prod_no', 'LIKE', 'SI%')
            ->paginate(10);

        $firstRecord = $getRecord->first();

        $series = [];
        if ($firstRecord) {
            $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $firstRecord->series]) ?? '0';
            $series =   Arr::get($get_series, 'data.0', []);
        }

        return view('backend.reports.finishgoods', compact('getRecord', 'series'));
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
            }], 'qty');

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
                $latest = $row->grpo->sortByDesc('id')->first();
                return $latest?->reason_qty ?? '-';
            })->make(true);
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
