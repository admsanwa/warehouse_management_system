<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryModel;
use App\Models\ProductionModel;
use Auth;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = ProductionModel::with(['delivery', 'quality'])
            ->where('status', 2)
            ->whereHas('quality', function ($q) {
                $q->where('result', 1);
            })
            ->filter($request)
            ->orderBy('id', 'desc')
            ->get();
        // dd($request);

        return view("backend.delivery.list", compact('getRecord'));
    }

    public function estimate(Request $request, $prod_no)
    {
        $request->validate([
            'status'    => "required",
            "date"      => "required|date",
            "remark"    => "required|string|min:3"
        ]);

        $io     = ProductionModel::where("prod_no", $prod_no)->value("io_no");
        $user   = Auth::user()->username;

        // save db
        $delivery = new DeliveryModel();
        $delivery->io           = $io;
        $delivery->status       = $request->status;
        $delivery->date         = $request->date;
        $delivery->remark       = $request->remark;
        $delivery->tracker_by    = $user;
        $delivery->save();
        // dd($request->all());

        return redirect()->back()->with("success", "Telah berhasil tracking delivery product: {$prod_no} menjadi {$request->status}");
    }

    public function history(Request $request)
    {
        $getRecord = DeliveryModel::getRecord($request);
        return view("backend.delivery.history", compact("getRecord"));
    }
}
