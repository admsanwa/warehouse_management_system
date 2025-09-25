<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\goodissueModel;
use App\Models\goodreceiptModel;
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
            'no_po' => $request->get('no_po'),
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
            ->when($param['no_po'], function ($query, $no_po) {
                return $query->where('grpo.no_po', 'like', '%' . $no_po . '%');
            })
            ->paginate(10);

        return view('backend.listtransactions.stockin', compact('getRecord'));
    }

    public function stock_out(Request $request)
    {
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
            'no_po' => $request->get('no_po'), // Ini nomor produksi
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
            ->when($param['no_po'], function ($query, $no_po) {
                return $query->where('ifp.no_po', 'like', '%' . $no_po . '%');
            })
            ->paginate(10);

        return view("backend.listtransactions.stockout", compact('getRecord'));
    }

    public function rfp(Request $request)
    {
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
            'prod_order' => $request->get('prod_order'),
            'io' => $request->get('io'),
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
            ->when($param['io'], function ($query, $io) {
                return $query->where('receipt_from_production.io', 'like', '%' . $io . '%');
            })
            ->when($param['prod_order'], function ($query, $prod_order) {
                return $query->where('receipt_from_production.prod_order', 'like', '%' . $prod_order . '%');
            })
            ->paginate(10);

        return view("backend.listtransactions.rfp", compact('getRecord'));
    }

    public function goodissue(Request $request)
    {
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
            'io' => $request->get('io'),
            'po' => $request->get('po'),
            'internal_no' => $request->get('internal_no'),
        ];
        $getRecord = goodissueModel::query()
            ->select('goods_issue.*', 'users.fullname')
            ->join('users', 'goods_issue.user_id', '=', 'users.id')
            ->when($param['item_code'], function ($query, $item_code) {
                return $query->where('goods_issue.item_code', $item_code);
            })
            ->when($param['item_desc'], function ($query, $item_desc) {
                return $query->where('goods_issue.item_desc', 'like', '%' . $item_desc . '%');
            })
            ->when($param['io'], function ($query, $io) {
                return $query->where('goods_issue.io', 'like', '%' . $io . '%');
            })
            ->when($param['po'], function ($query, $po) {
                return $query->where('goods_issue.po', 'like', '%' . $po . '%');
            })
            ->when($param['internal_no'], function ($query, $internal_no) {
                return $query->where('goods_issue.internal_no', 'like', '%' . $internal_no . '%');
            })
            ->paginate(10);
        return view("backend.listtransactions.goodissue", compact('getRecord'));
    }

    public function goodreceipt(Request $request)
    {
        $param = [
            'item_code' => $request->get('item_code'),
            'item_desc' => $request->get('item_desc'),
            'io' => $request->get('io'),
            'po' => $request->get('po'),
            'internal_no' => $request->get('internal_no'),
        ];
        $getRecord = goodreceiptModel::query()
            ->select('goods_receipt.*', 'users.fullname')
            ->join('users', 'goods_receipt.user_id', '=', 'users.id')
            ->when($param['item_code'], function ($query, $item_code) {
                return $query->where('goods_receipt.item_code', $item_code);
            })
            ->when($param['item_desc'], function ($query, $item_desc) {
                return $query->where('goods_receipt.item_desc', 'like', '%' . $item_desc . '%');
            })
            ->when($param['io'], function ($query, $io) {
                return $query->where('goods_receipt.io', 'like', '%' . $io . '%');
            })
            ->when($param['po'], function ($query, $po) {
                return $query->where('goods_receipt.po', 'like', '%' . $po . '%');
            })
            ->when($param['internal_no'], function ($query, $internal_no) {
                return $query->where('goods_receipt.internal_no', 'like', '%' . $internal_no . '%');
            })
            ->paginate(10);
        return view("backend.listtransactions.goodreceipt", compact('getRecord'));
    }
}
