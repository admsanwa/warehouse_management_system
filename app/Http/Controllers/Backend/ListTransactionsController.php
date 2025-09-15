<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ItemsMaklonModel;
use App\Models\RFPModel;
use App\Models\IFPModel;
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

        $getRecord = grpoModel::query()
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
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
        ];
        $getRecord = IFPModel::query()
            ->select('ifp.*', 'users.fullname')
            ->join('users', 'ifp.user_id', '=', 'users.id')
            ->when($param['item_code'], function ($query, $item_code) {
                return $query->where('ifp.item_code', $item_code);
            })
            ->when($param['item_desc'], function ($query, $item_desc) {
                return $query->where('ifp.item_desc', 'like', '%' . $item_desc . '%');
            })
            ->paginate(10);

        return view("backend.listtransactions.stockout", compact('getRecord'));
    }

    public function rfp(Request $request)
    {
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
        ];
        $getRecord = RFPModel::query()
            ->select('receipt_from_production.*', 'users.fullname')
            ->join('users', 'receipt_from_production.user_id', '=', 'users.id')
            ->when($param['item_code'], function ($query, $item_code) {
                return $query->where('receipt_from_production.item_code', $item_code);
            })
            ->when($param['item_desc'], function ($query, $item_desc) {
                return $query->where('receipt_from_production.item_desc', 'like', '%' . $item_desc . '%');
            })
            ->paginate(10);

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
