<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProductionModel;
use App\Models\QualityModel;
use Auth;
use DB;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index(Request $request)
    {
        DB::enableQueryLog();

        $getRecord = ProductionModel::with('qualityTwo')
            ->addSelect([
                'latest_quality_id' => QualityModel::select('id')
                    ->whereColumn('quality.io', 'production_order.io_no')
                    ->orderByDesc('id')
                    ->limit(1)
            ])
            ->orderByDesc('latest_quality_id')
            ->filter($request) // <-- using the scope
            ->get();
        // dd($getRecord->first()->qualityTwo);
        // dd(DB::getQueryLog());

        return view("backend.quality.list", compact("getRecord"));
    }

    public function result(Request $request, $prod_no)
    {
        $request->validate([
            'check'     => "required|numeric|min:1",
            'remark'    => "required"
        ]);

        $io     = ProductionModel::where("prod_no", $prod_no)->value("io_no");
        $check  = $request->check == 1 ? "OK" : "NG";
        $user   = Auth::user()->username;

        $quality = new QualityModel();
        $quality->io        = $io;
        $quality->result    = $request->check;
        $quality->remark    = $request->remark;
        $quality->result_by = $user;
        $quality->save();

        return redirect()->back()->with("success", "Telah berhasil menilai product: {$prod_no} menjadi {$check}");
    }

    public function history(Request $request)
    {
        $getRecord = QualityModel::getRecord($request);
        return view("backend.quality.history", compact("getRecord"));
    }
}
