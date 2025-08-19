<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockModel;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = StockModel::getRecord($request);
        return view('backend.stock.list', ['stocks' => $getRecord]);
    }
}
