<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryModel;
use App\Models\ProductionModel;
use App\Models\QualityModel;
use App\Services\SapService;
use Auth;
use DB;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = DeliveryModel::getRecord($request);

        return view('backend.delivery.list', compact('getRecord'));
    }

    public function estimate(Request $request, $docEntry)
    {
        // dd($docEntry);
        $request->validate([
            'status'    => "required",
            "date"      => "required|date",
            "remark"    => "required|string|min:2"
        ]);
        $user   = Auth::user()->username;
        $deliveryData = DeliveryModel::where('doc_entry', $docEntry)->first();

        // save db
        $delivery = new DeliveryModel();
        $delivery->doc_entry    = $docEntry;
        $delivery->io           = $deliveryData->io;
        $delivery->prod_order   = $deliveryData->prod_order;
        $delivery->prod_no      = $deliveryData->prod_no;
        $delivery->prod_desc    = $deliveryData->prod_desc;
        $delivery->series       = $deliveryData->series;
        $delivery->status       = $request->status;
        $delivery->date         = $request->date;
        $delivery->remark       = $request->remark;
        $delivery->tracker_by   = $user;
        $delivery->save();


        // dd($request->all());
        return redirect()->back()->with("success", "Telah berhasil tracking delivery product: {$delivery->prod_no} menjadi {$request->status}");
    }

    public function history(Request $request)
    {
        $getRecord = DeliveryModel::get();

        return view('backend.delivery.history', compact('getRecord'));
    }
}
