<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Services\SapService;
use Auth;
use DB;
use Illuminate\Http\Request;

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
}
