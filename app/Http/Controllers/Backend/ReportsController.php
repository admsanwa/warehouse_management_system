<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BonDetailsModel;
use App\Models\QualityModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportsController extends Controller
{
    public function semifg(Request $request)
    {
        $getRecord = QualityModel::getRecord($request)
            ->where('result', 1)
            ->where('prod_no', 'LIKE', 'SF%')
            ->paginate(10);

        return view('backend.reports.semifg', compact('getRecord'));
    }

    public function finish_goods(Request $request)
    {
        $getRecord = QualityModel::getRecord($request)
            ->where('result', 1)
            ->where('prod_no', 'LIKE', 'SI%')
            ->paginate(10);

        return view('backend.reports.finishgoods', compact('getRecord'));
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
            ->addColumn('date', fn($row) => $row->bon->date ? date('d-m-Y', strtotime($row->bon->date)) : '-')
            ->addColumn('item_name', fn($row) => $row->item_name)
            ->addColumn('uom', fn($row) => $row->uom)
            ->addColumn('qty', fn($row) => $row->qty)
            ->addColumn('remark', fn($row) => $row->remark)
            ->addColumn('receipt_date', fn($row) => $row->grpo?->created_at ? date('d-m-Y', strtotime($row->grpo->created_at)) : '-')
            ->make(true);
    }
}
