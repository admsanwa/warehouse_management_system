<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BonDetailsModel;
use App\Models\DeliveryModel;
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
        $query = BonDetailsModel::with(['bon', 'grpo'])
            ->filter();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no', fn($row) => $row->bon->no ?? '-')
            ->addColumn('date', fn($row) => $row->bon?->date ? date('d-m-Y', strtotime($row->bon->date)) : '-')
            ->addColumn('item_name', fn($row) => $row->item_name)
            ->addColumn('uom', fn($row) => $row->uom)
            ->addColumn('qty', fn($row) => $row->qty)
            ->addColumn('remark', fn($row) => $row->remark)
            ->addColumn('receipt_date', fn($row) => $row->grpo?->created_at ? date('d-m-Y', strtotime($row->grpo->created_at)) : '-')
            ->addColumn('receipt_qty', fn($row) => $row->grpo?->qty ? $row->grpo->qty : 0)
            ->addColumn('remain_qty', fn($row) => $row->grpo?->qty ? $row->qty - $row->grpo->qty : 0)
            ->make(true);
    }
}
