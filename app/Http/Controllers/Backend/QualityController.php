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
use App\Notifications\MailQcApproval;
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
        $user   = Auth::user();

        // Cek apakah user manager Procurement, Installation and Delivery
        $isProcManager = $user->department === 'Procurement, Installation and Delivery'
            && $user->level === 'Manager';

        $dev_users = User::where('department', 'IT')->get();

        if ($isProcManager) {
            $lastApproval = QualityModel::where("io", $io)
                ->where("result", 3) // 3 = Need Approval
                ->latest()
                ->first();

            if ($lastApproval) {
                // Update ke hasil terbaru
                $lastApproval->result    = $request->check;
                $lastApproval->remark    = $request->remark;
                $lastApproval->result_by = $user->username;
                $lastApproval->save();

                $recipients = User::where('department', 'Quality Control')
                    ->where('level', 'Operator')
                    ->get();

                $recipients = $recipients->merge($dev_users);

                Notification::send($recipients, new MailQcApproval(
                    $io,
                    $check,
                    $request->remark ?? '',
                    url('admin/quality/list')
                ));
                return redirect()->back()->with("success", "Approval telah diperbarui menjadi {$check}");
            }
        }

        // Ini jika bukan approval tapi cek baru
        $quality = new QualityModel();
        $quality->io        = $io;
        $quality->result    = $request->check;
        $quality->remark    = $request->remark;
        $quality->result_by = $user->username;
        $quality->save();

        // Jika status Need Approval kirim email ke manager Procurement, Installation and Delivery
        if ((int) $request->check === 3) {
            $recipients = User::where('department', 'Procurement, Installation and Delivery')
                ->where('level', 'Manager')
                ->get();

            $recipients = $recipients->merge($dev_users);

            Notification::send($recipients, new MailQcResult(
                $io,
                $check,
                $request->remark ?? '',
                url('admin/quality/list')
            ));
        }

        return redirect()->back()->with("success", "Telah berhasil menilai product: {$prod_no} menjadi {$check}");
    }


    public function history(Request $request)
    {
        $getRecord = QualityModel::getRecord($request);
        $user      = Auth::user();
        return view("backend.quality.history", compact("getRecord", "user"));
    }
}
