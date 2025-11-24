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
use App\Services\SapService;
use Arr;

class QualityController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function index(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 20),
            "DocNum" => $request->get('doc_num'),
            "DueDate" => formatDateSlash($request->get('date')),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
            "Status" =>  $request->get('status', 'Released'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }
        // dd($getProds);

        $getProds['data'] = collect($getProds['data'] ?? [])
            // ->filter(fn($item) => str_starts_with(strtoupper($item['ItemCode']), 'SI'))
            ->values()
            ->all();

        // $totalPages = ceil($getProds['total'] / $param['limit']);
        $currentCount   = $getProds['total'] ?? count($getProds['data'] ?? []);
        $totalPages     = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $user           = Auth::user();
        $sapDocEntry    = collect($getProds['data'])->pluck('DocEntry')->toArray();
        $qualityQuery    = QualityModel::whereIn('doc_entry', $sapDocEntry);

        if ($request->filled('qc_status')) {
            $qualityQuery->where('result', $request->qc_status);
        }
        $qualityData    = $qualityQuery->get()->keyBy('doc_entry');

        $mergedData = collect($getProds['data'])->map(function ($sapItem) use ($qualityData) {
            $docEntry = $sapItem['DocEntry'];

            return [
                'sap'     => $sapItem,
                'quality' => $qualityData[$docEntry] ?? null,
            ];
        })->filter(function ($row) use ($request) {
            if ($request->filled('qc_status')) {
                return !is_null($row['quality']);
            }
            return true;
        })->values();

        return view('backend.quality.list', [
            'mergedData'  => $mergedData,
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $mergedData->count(),
            'totalPages'  => $totalPages,
            'user'        => $user
        ],);
    }

    public function result(Request $request, $docentry)
    {
        $param = [
            "DocEntry" => $docentry,
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP, Silakan coba lagi nanti');
        }
        $sapData    = collect($getProds['data'] ?? [])->firstWhere('DocEntry', $docentry);
        if (!$sapData) {
            return back()->with('error', "Data dengan DocEntry {$docentry} tersebut tidak ditemukan di SAP");
        }
        // dd($docentry, $getProds['data']);

        $itemCode   = $sapData['ItemCode'];
        $request->validate([
            'check'     => "required|numeric|min:1",
            'remark'    => "required"
        ]);

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

        $quality                = new QualityModel();
        $quality->doc_entry     = $sapData['DocEntry'];
        $quality->io            = $sapData['U_MEB_NO_IO'];
        $quality->prod_order    = $sapData['DocNum'];
        $quality->prod_no       = $sapData['ItemCode'];
        $quality->prod_desc     = $sapData['ItemName'];
        $quality->qty           = $sapData['CmpltQty'];
        $quality->series        = $sapData['Series'];
        $quality->result        = $request->check;
        $quality->remark        = $request->remark;
        $quality->result_by     = $user->fullname;
        $quality->save();

        $isProcManager = $user->department === 'Procurement, Installation and Delivery'
            && $user->level === 'Manager';

        $dev_users = User::where('department', 'IT')->get();

        /**
         * 1) Jika hasil QC = Need Approval (3)
         *    → Notifikasi ke Manager Procurement, Installation and Delivery + IT
         */
        if ($request->check == 3) {
            $recipients = User::where('department', 'Procurement, Installation and Delivery')
                ->where('level', 'Manager')
                ->get();
            $recipients = $recipients->merge($dev_users);

            Notification::send($recipients, new MailQcResult(
                $sapData['U_MEB_NO_IO'],
                $check,
                $request->remark ?? '',
                url('admin/quality/list')
            ));
        }

        /**
         * 2) Jika user adalah Manager Procurement
         *    → Notifikasi ke semua user Quality Control + IT
         */
        if ($isProcManager) {
            $recipients = User::where('department', 'Quality Control')->get();
            $recipients = $recipients->merge($dev_users);

            Notification::send($recipients, new MailQcApproval(
                $sapData['U_MEB_NO_IO'],
                $check,
                $request->remark ?? '',
                url('admin/quality/list')
            ));
        }
        /**
         * 3) Jika user berasal dari Quality Control
         *    dan hasil = OK (1) / NG (2) / Need Paint (4)
         *    → Notifikasi ke Production + IT
         */
        else if ($user->department === "Quality Control") {
            if ($request->check == 1 || $request->check == 2 || $request->check == 4) {
                $recipients = User::where('department', 'Production')->get();
                $recipients = $recipients->merge($dev_users);

                Notification::send($recipients, new MailQcResult(
                    $sapData['U_MEB_NO_IO'],
                    $check,
                    $request->remark ?? '',
                    url('admin/quality/list')
                ));
            }
        }
        /**
         * 4) Jika user berasal dari Production
         *    dan hasil = OK (1) / NG (2) / Painting by Inhouse (5) / Painting by Makloon (6)
         *    → Notifikasi ke Quality Control + IT
         */
        else if ($user->department === "Production") {
            if ($request->check == 1 || $request->check == 2 || $request->check == 5 || $request->check == 6) {
                $recipients = User::where('department', 'Quality Control')->get();
                $recipients = $recipients->merge($dev_users);

                Notification::send($recipients, new MailQcResult(
                    $sapData['U_MEB_NO_IO'],
                    $check,
                    $request->remark ?? '',
                    url('admin/quality/list')
                ));
            }
        }

        return redirect()->back()->with("success", "Telah berhasil menilai product: {$itemCode} menjadi {$check}");
    }

    public function result_two(Request $request, $docEntry, $itemCode)
    {
        $param = [
            "DocEntry" => $docEntry,
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP, Silakan coba lagi nanti');
        }
        $sapData    = collect($getProds['data'] ?? [])->firstWhere('DocEntry', $docEntry);
        $prod = Arr::get($getProds, 'data.0', []);
        $lines = collect(Arr::get($prod, 'Lines', []))->firstWhere('ItemCode', $itemCode);
        // dd($docEntry, $itemCode, $getProds['data'], $lines);

        $request->validate([
            'check'     => "required|numeric|min:1",
            'remark'    => "required"
        ]);

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

        $quality                = new QualityModel();
        $quality->doc_entry     = $sapData['DocEntry'];
        $quality->io            = $sapData['U_MEB_NO_IO'];
        $quality->prod_order    = $sapData['DocNum'];
        $quality->prod_no       = $lines['ItemCode'];
        $quality->prod_desc     = $lines['ItemName'];
        $quality->qty           = $lines['IssuedQty'];
        $quality->series        = $sapData['Series'];
        $quality->result        = $request->check;
        $quality->remark        = $request->remark;
        $quality->result_by     = $user->fullname;
        $quality->save();

        $isProcManager = $user->department === 'Procurement, Installation and Delivery'
            && $user->level === 'Manager';

        $dev_users = User::where('department', 'IT')->get();

        /**
         * 1) Jika hasil QC = Need Approval (3)
         *    → Notifikasi ke Manager Procurement, Installation and Delivery + IT
         */
        if ($request->check == 3) {
            $recipients = User::where('department', 'Procurement, Installation and Delivery')
                ->where('level', 'Manager')
                ->get();
            $recipients = $recipients->merge($dev_users);

            Notification::send($recipients, new MailQcResult(
                $sapData['U_MEB_NO_IO'],
                $check,
                $request->remark ?? '',
                url('admin/quality/list')
            ));
        }

        /**
         * 2) Jika user adalah Manager Procurement
         *    → Notifikasi ke semua user Quality Control + IT
         */
        if ($isProcManager) {
            $recipients = User::where('department', 'Quality Control')->get();
            $recipients = $recipients->merge($dev_users);

            Notification::send($recipients, new MailQcApproval(
                $sapData['U_MEB_NO_IO'],
                $check,
                $request->remark ?? '',
                url('admin/quality/list')
            ));
        }
        /**
         * 3) Jika user berasal dari Quality Control
         *    dan hasil = OK (1) / NG (2) / Need Paint (4)
         *    → Notifikasi ke Production + IT
         */
        else if ($user->department === "Quality Control") {
            if ($request->check == 1 || $request->check == 2 || $request->check == 4) {
                $recipients = User::where('department', 'Production')->get();
                $recipients = $recipients->merge($dev_users);

                Notification::send($recipients, new MailQcResult(
                    $sapData['U_MEB_NO_IO'],
                    $check,
                    $request->remark ?? '',
                    url('admin/quality/list')
                ));
            }
        }
        /**
         * 4) Jika user berasal dari Production
         *    dan hasil = OK (1) / NG (2) / Painting by Inhouse (5) / Painting by Makloon (6)
         *    → Notifikasi ke Quality Control + IT
         */
        else if ($user->department === "Production") {
            if ($request->check == 1 || $request->check == 2 || $request->check == 5 || $request->check == 6) {
                $recipients = User::where('department', 'Quality Control')->get();
                $recipients = $recipients->merge($dev_users);

                Notification::send($recipients, new MailQcResult(
                    $sapData['U_MEB_NO_IO'],
                    $check,
                    $request->remark ?? '',
                    url('admin/quality/list')
                ));
            }
        }
        return redirect()->back()->with("success", "Telah berhasil menilai product: {$itemCode} menjadi {$check}");
    }

    public function history(Request $request)
    {
        $getRecord = QualityModel::getRecord($request)
            ->paginate(10);

        return view('backend.quality.history', compact('getRecord'));
    }
}
