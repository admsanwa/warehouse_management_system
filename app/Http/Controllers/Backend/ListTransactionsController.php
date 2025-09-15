<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ItemsMaklonModel;
use App\Models\RFPModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use App\Models\grpoModel;
use App\Models\SapReasonModel as SapReason;

class ListTransactionsController extends Controller
{


    public function stock_in(Request $request)
    {
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
        ];

        $getRecord = GrpoModel::query()
            ->select('grpo.*', 'users.fullname')
            ->join('users', 'grpo.user_id', '=', 'users.id')
            ->when($param['item_code'], function ($query, $item_code) {
                return $query->where('grpo.item_code', $item_code);
            })
            ->when($param['item_desc'], function ($query, $item_desc) {
                return $query->where('grpo.item_desc', 'like', '%' . $item_desc . '%');
            })
            ->paginate(10);

        return view('backend.listtransactions.stockin', compact('getRecord'));
    }



    public function stock_out(Request $request)
    {
        $getRecord = StockModel::getData($request)->whereNotNull('isp')->paginate(5);

        return view("backend.listtransactions.stockout", compact('getRecord'));
    }

    public function rfp(Request $request)
    {
        $getRecord = RFPModel::getRecord($request);

        return view("backend.listtransactions.rfp", compact('getRecord'));
    }

    public function goodissue(Request $request)
    {
        $getRecord = ItemsMaklonModel::getRecord($request)->where("gi", '>', 0)->paginate(5);

        return view("backend.listtransactions.goodissue", compact('getRecord'));
    }

    public function goodreceipt(Request $request)
    {
        $getRecord = ItemsMaklonModel::getRecord($request)->where("gr", '>', 0)->paginate(5);

        return view("backend.listtransactions.goodreceipt", compact('getRecord'));
    }
}
