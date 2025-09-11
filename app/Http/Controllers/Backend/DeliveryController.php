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
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function index(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 50),
            "U_MEB_NO_IO" => $request->get('io_no'),
            "DocNum" => $request->get('prod_order'),
            "ItemCode" =>  $request->get('prod_no'),
            "ItemName" =>  $request->get('prod_desc'),
            "Series" =>  $request->get('series'),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $user       = Auth::user();
        $sapDataCollection = collect($getProds['data'] ?? []);
        $sapDocEntry = $sapDataCollection
            ->pluck('DocEntry')
            ->map(fn($code) => strtoupper(trim($code)))
            ->toArray();
        $filteredSapData = $sapDataCollection->filter(fn($item) => str_starts_with(strtoupper($item['ItemCode']), 'SI'))->values();

        $deliveryData = DeliveryModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)
            ->get()
            ->groupBy(fn($d) => strtoupper(trim($d->doc_entry)))
            ->map(fn($group) => $group->first());

        $qualityData = QualityModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)
            ->where('result', 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($q) => strtoupper(trim($q->doc_entry)))
            ->map(fn($group) => $group->first());

        $mergedData = $filteredSapData->map(function ($sapItem) use ($qualityData, $deliveryData) {
            // Cari semua SAP yang punya docEntry sama
            $docEntry = strtoupper(trim($sapItem['DocEntry']));

            return [
                'sap'      => $sapItem,
                'quality'  => $qualityData[$docEntry] ?? null,
                'delivery' => $deliveryData[$docEntry] ?? null,
            ];
        })->filter(function ($row) use ($request) {
            if (is_null($row['quality'])) {
                return false;
            }

            if ($request->filled('delivery_status')) {
                return $row['delivery'] && $row['delivery']->status === $request->delivery_status;
            }

            return true;
        })->values();

        // dd([
        //     'sap_data'    => $getProds['data'] ?? [],
        //     'qualityData' => $qualityData,
        //     'mergedData'  => $mergedData,
        // ]);

        return view('backend.delivery.list', [
            'mergedData'  => $mergedData,
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $mergedData->count(),
            'totalPages'  => ceil($getProds['total'] / $param['limit']),
            'user'        => $user
        ]);
    }

    public function estimate(Request $request, $docEntry)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
        ];

        $getProds = $this->sap->getProductionOrders($param);
        if (empty($getProds) || $getProds['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP, Silakan coba lagi nanti');
        }
        $sapData    = collect($getProds['data'] ?? [])->firstWhere('DocEntry', $docEntry);
        $request->validate([
            'status'    => "required",
            "date"      => "required|date",
            "remark"    => "required|string|min:3"
        ]);
        $user   = Auth::user()->username;

        // save db
        $delivery = new DeliveryModel();
        $delivery->doc_entry    = $sapData['DocEntry'];
        $delivery->io           = $sapData['U_MEB_NO_IO'];
        $delivery->prod_order   = $sapData['DocNum'];
        $delivery->prod_no      = $sapData['ItemCode'];
        $delivery->prod_desc    = $sapData['ItemName'];
        $delivery->series        = $sapData['Series'];
        $delivery->status       = $request->status;
        $delivery->date         = $request->date;
        $delivery->remark       = $request->remark;
        $delivery->tracker_by   = $user;
        $delivery->save();
        // dd($request->all());
        return redirect()->back()->with("success", "Telah berhasil tracking delivery product: {$sapData['ItemCode']} menjadi {$request->status}");
    }

    public function history(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
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

        $totalPages  = ceil($getProds['total'] / $param['limit']);
        $user        = Auth::user();
        $sapDocEntry = collect($getProds['data'] ?? [])
            ->pluck('DocEntry')
            ->map(fn($code) => strtoupper(trim($code)))
            ->toArray();

        $deliveryData = DeliveryModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)
            ->get()
            ->groupBy('doc_entry');

        $mergedData = $deliveryData->flatMap(function ($qualities, $docEntry) use ($getProds) {
            $sapData = collect($getProds['data'] ?? [])->firstWhere('DocEntry', $docEntry);

            if (!$sapData) {
                return collect();
            }

            return $qualities->map(function ($delivery) use ($sapData) {
                return [
                    'sap'     => $sapData,
                    'delivery' => $delivery,
                ];
            });
        })->filter(function ($row) use ($request) {
            if ($request->filled('delivery_status')) {
                return $row['delivery'] && $row['delivery']->status === $request->delivery_status;
            }

            return true;
        })->values();

        return view('backend.delivery.history', [
            'mergedData'  => $mergedData,
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $mergedData->count(),
            'totalPages'  => $totalPages,
            'user'        => $user
        ]);
    }
}
