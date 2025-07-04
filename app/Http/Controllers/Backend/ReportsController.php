<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\RFPModel;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function finish_goods(Request $request)
    {
        $getRecord = RFPModel::getRecord($request);
        return view('backend.reports.finishgoods', compact('getRecord'));
    }
}
