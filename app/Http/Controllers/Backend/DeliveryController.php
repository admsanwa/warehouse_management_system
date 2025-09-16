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
        $getRecord = QualityModel::getRecord($request)
            ->with('delivery')
            ->where('result', 1)
            ->where('prod_no', 'LIKE', 'SI%')
            ->paginate(10);

        return view('backend.delivery.list', compact('getRecord'));
    }

    public function estimate(Request $request, $docEntry)
    {
        // dd($docEntry);
        $request->validate([
            'status'    => "required",
            "date"      => "required|date",
            "remark"    => "required|string|min:3"
        ]);
        $quality = QualityModel::where('doc_entry', $docEntry)->first();
        $user   = Auth::user()->username;

        // save db
        $delivery = new DeliveryModel();
        $delivery->doc_entry    = $quality->doc_entry;
        $delivery->io           = $quality->io;
        $delivery->prod_order   = $quality->prod_order;
        $delivery->prod_no      = $quality->prod_no;
        $delivery->prod_desc    = $quality->prod_desc;
        $delivery->series       = $quality->series;
        $delivery->status       = $request->status;
        $delivery->date         = $request->date;
        $delivery->remark       = $request->remark;
        $delivery->tracker_by   = $user;
        $delivery->save();
        // dd($request->all());
        return redirect()->back()->with("success", "Telah berhasil tracking delivery product: {$quality->prod_no} menjadi {$request->status}");
    }

    public function history(Request $request)
    {
        $getRecord = DeliveryModel::getRecord($request)->paginate(10);

        return view('backend.delivery.history', compact('getRecord'));
    }
}
