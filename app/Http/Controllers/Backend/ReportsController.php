<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\RFPModel;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function finish_goods(Request $request)
    {
        $getRecord = RFPModel::getRecord($request)->where("prod_no", "LIKE", "SI%")->paginate(10);
        return view('backend.reports.finishgoods', compact('getRecord'));
    }

    public function semifg(Request $request)
    {
        $getRecord = RFPModel::getRecord($request)->where("prod_no", "NOT LIKE", "SI%")->paginate(10);
        return view("backend.reports.semifg", compact("getRecord"));
    }
}
