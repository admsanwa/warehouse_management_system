<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProductionModel;
use App\Models\ProductionOrderDetailsModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\QualityModel;
use App\Models\StockModel;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MailQcResult;
use App\Models\User;

class QualityController extends Controller
{
    public function index(Request $request)
    {

        $query = ProductionModel::with('qualityTwo')
            ->addSelect([
                'latest_quality_id' => QualityModel::select('id')
                    ->whereColumn('quality.io', 'production_order.io_no')
                    ->orderByDesc('id')
                    ->limit(1)
            ])
            ->orderByDesc('latest_quality_id')
            ->filter($request);

        if (auth()->user()->department === 'Production') {
            $query->whereHas('qualityTwo', function ($q) {
                $q->where('result', 4);
            });
        }

        $getRecord = $query->paginate(10);

        $getRecord->getCollection()->transform(function ($record) {
            $orderQty   = ProductionOrderDetailsModel::where("doc_num", $record->doc_num)->sum("qty");
            $stockQty   = StockModel::where("prod_order", $record->doc_num)->sum("qty");

            $record->result         = $orderQty - $stockQty; // tambahkan field baru ke object
            $record->is_completed   = $orderQty <= $stockQty; // true/false

            return $record;
        });
        // dd($getRecord->first()->qualityTwo);
        // dd(DB::getQueryLog());
        $user = Auth::user();

        $notifications = $getRecord->getCollection()->filter(function ($item) use ($user) {
            return $item->qualityTwo
                && $item->qualityTwo->result === 3   // Need Approval
                && $user->nik === "06067";           // sesuai nik
        });

        $filtered = $getRecord->getCollection()->filter(function ($item) {
            return $item->is_completed;
        });

        $getRecord->setCollection($filtered);

        return view("backend.quality.list", compact("getRecord", "notifications", "user"));
    }

    public function result(Request $request, $prod_no)
    {
        $request->validate([
            'check'     => "required|numeric|min:1",
            'remark'    => "required"
        ]);

        $io     = ProductionModel::where("prod_no", $prod_no)->value("io_no");
        $statusMap = [
            1 => "OK",
            2 => "NG",
            3 => "Need Approval",
            4 => "Need Paint",
            5 => "Painting by Inhouse",
            6 => "Painting by Makloon"
        ];

        $check  = $request->check !== null ? ($statusMap[$request->check] ?? "-") : "-";
        $user   = Auth::user()->username;

        $quality = new QualityModel();
        $quality->io        = $io;
        $quality->result    = $request->check;
        $quality->remark    = $request->remark;
        $quality->result_by = $user;
        $quality->save();

        $recipients = User::where('department', 'Procurement, Installation and Delivery')
            ->where('level', 'Manager')
            ->get();
        $dev_users = User::where('department', 'IT')->get();

        $recipients = $recipients->merge($dev_users);
        Notification::send($recipients, new MailQcResult(
            $io,
            $check,
            $request->remark ?? '',
            url('admin/quality/list')
        ));


        return redirect()->back()->with("success", "Telah berhasil menilai product: {$prod_no} menjadi {$check}");
    }

    public function history(Request $request)
    {
        $getRecord = QualityModel::getRecord($request);
        $user      = Auth::user();
        return view("backend.quality.history", compact("getRecord", "user"));
    }
}
