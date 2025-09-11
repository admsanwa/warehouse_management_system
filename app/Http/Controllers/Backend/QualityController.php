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
            "limit" => (int) $request->get('limit', 10),
            "DueDate" => formatDateSlash($request->get('date')),
            "DocNum" => $request->get('doc_num'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
            "Status" =>  $request->get('status', 'Closed'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

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
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            "DocNum" => $request->get('doc_num'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "Series" =>  $request->get('series'),
            "Status" =>  $request->get('status', 'Closed'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP, Silakan coba lagi nanti');
        }
        $sapData    = collect($getProds['data'] ?? [])->firstWhere('DocEntry', $docentry);
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
        $quality->series        = $sapData['Series'];
        $quality->result        = $request->check;
        $quality->remark        = $request->remark;
        $quality->result_by     = $user->username;
        $quality->save();

        $isProcManager = $user->department === 'Procurement, Installation and Delivery'
            && $user->level === 'Manager';
        $dev_users = User::where('department', 'IT')->get();

        if ($isProcManager) {
            $recipients = User::where('department', 'Quality Control')
                ->where('level', 'Operator')
                ->get();
            $recipients = $recipients->merge($dev_users);

            Notification::send($recipients, new MailQcApproval(
                $sapData['U_MEB_NO_IO'],
                $check,
                $request->remark ?? '',
                url('admin/quality/list')
            ));
        } else if ($request->check == 3) {
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
        return redirect()->back()->with("success", "Telah berhasil menilai product: {$itemCode} menjadi {$check}");
    }

    public function history(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
            "DueDate" => formatDateSlash($request->get('date')),
            "DocNum" => $request->get('doc_num'),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
            "Status" =>  $request->get('status', 'Close'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $totalPages  = ceil($getProds['total'] / $param['limit']);
        $user        = Auth::user();
        $sapDocEntry = collect($getProds['data'] ?? [])
            ->pluck('DocEntry')
            ->map(fn($code) => strtoupper(trim($code)))
            ->toArray();

        $qualityQuery = QualityModel::whereIn('doc_entry', $sapDocEntry);
        if ($request->filled('qc_status')) {
            $qualityQuery->where('result', $request->qc_status);
        }
        $qualityData = $qualityQuery->get()->keyBy('doc_entry');

        $mergedData = collect($getProds['data'])->map(function ($sapItem) use ($qualityData) {
            $docEntry = $sapItem['DocEntry'];

            return [
                'sap'     => $sapItem,
                'quality' => $qualityData[$docEntry] ?? null,
            ];
        })->filter(function ($row) {
            // SELALU butuh quality, jadi hanya ambil data yang punya pasangan QC
            return !is_null($row['quality']);
        })->values();


        // dd([
        //     'sap_data'    => $getProds['data'] ?? [],
        //     'qualityData' => $qualityData,
        //     'mergedData'  => $mergedData,
        // ]);

        return view('backend.quality.history', [
            'mergedData'    => $mergedData,
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $mergedData->count(),
            'totalPages'  => $totalPages,
            'user'        => $user
        ]);
    }
}
