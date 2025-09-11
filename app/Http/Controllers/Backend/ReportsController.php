<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\QualityModel;
use App\Models\RFPModel;
use App\Services\SapService;
use Auth;
use DB;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function semifg(Request $request)
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

        $user               = Auth::user();
        $sapDataCollection  = collect($getProds['data'] ?? []);
        $sapDocEntry = $sapDataCollection
            ->filter(fn($item) => str_starts_with(strtoupper($item['ItemCode']), 'SF'))
            ->pluck('DocEntry')
            ->map(fn($code) => strtoupper(trim($code)))
            ->toArray();

        $qualityQuery = QualityModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)->where('result', 1);
        if ($request->filled('io_no')) {
            $qualityQuery->where('io_no', 'LIKE', '%' . $request->io_no . '%');
        }
        if ($request->filled('prod_desc')) {
            $qualityQuery->where('item_desc', 'LIKE', '%' . $request->prod_desc . '%');
        }
        if ($request->filled('prod_no')) {
            $qualityQuery->where('prod_no', 'LIKE', '%' . $request->prod_no . '%');
        }
        if ($request->filled('prod_desc')) {
            $qualityQuery->where('item_desc', 'LIKE', '%' . $request->prod_desc . '%');
        }

        $qualityData = QualityModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)
            ->where('result', 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('doc_entry')
            ->map(fn($group) => $group->first());

        $mergedData = $qualityData->map(function ($qualities, $itemCode) use ($sapDataCollection) {
            // Cari semua SAP yang punya ItemCode sama
            $sapData = $sapDataCollection->first(function ($item) use ($itemCode) {
                return strtoupper(trim($item['DocEntry'])) === strtoupper(trim($itemCode));
            });

            return [
                'sap'     => $sapData,   // bisa null
                'quality' => $qualities,
            ];
        })->values();

        // dd([
        //     'sap_data'    => $getProds['data'] ?? [],
        //     'qualityData' => $qualityData,
        //     'mergedData'  => $mergedData,
        // ]);

        return view('backend.reports.semifg', [
            'getProds'    => $mergedData,
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $mergedData->count(),
            'totalPages'  => ceil($getProds['total'] / $param['limit']),
            'user'        => $user
        ]);
    }

    public function finish_goods(Request $request)
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

        $user       = Auth::user();
        $sapDataCollection = collect($getProds['data'] ?? []);
        $sapDocEntry = $sapDataCollection
            ->filter(fn($item) => str_starts_with(strtoupper($item['ItemCode']), 'SI'))
            ->pluck('DocEntry')
            ->map(fn($code) => strtoupper(trim($code)))
            ->toArray();

        $qualityQuery = QualityModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)->where('result', 1);
        if ($request->filled('io_no')) {
            $qualityQuery->where('io_no', 'LIKE', '%' . $request->io_no . '%');
        }
        if ($request->filled('prod_desc')) {
            $qualityQuery->where('item_desc', 'LIKE', '%' . $request->prod_desc . '%');
        }
        if ($request->filled('prod_no')) {
            $qualityQuery->where('prod_no', 'LIKE', '%' . $request->prod_no . '%');
        }
        if ($request->filled('prod_desc')) {
            $qualityQuery->where('item_desc', 'LIKE', '%' . $request->prod_desc . '%');
        }

        $qualityData = QualityModel::whereIn(DB::raw('UPPER(TRIM(doc_entry))'), $sapDocEntry)
            ->where('result', 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('doc_entry')
            ->map(fn($group) => $group->first());

        $mergedData = $qualityData->map(function ($qualities, $itemCode) use ($sapDataCollection) {
            // Cari semua SAP yang punya ItemCode sama
            $sapData = $sapDataCollection->first(function ($item) use ($itemCode) {
                return strtoupper(trim($item['DocEntry'])) === strtoupper(trim($itemCode));
            });

            return [
                'sap'     => $sapData,   // bisa null
                'quality' => $qualities,
            ];
        })->values();

        // dd([
        //     'sap_data'    => $getProds['data'] ?? [],
        //     'qualityData' => $qualityData,
        //     'mergedData'  => $mergedData,
        // ]);

        return view('backend.reports.finishgoods', [
            'getProds'    => $mergedData,
            'page'        => $getProds['page'],
            'limit'       => $getProds['limit'],
            'total'       => $mergedData->count(),
            'totalPages'  => ceil($getProds['total'] / $param['limit']),
            'user'        => $user
        ]);
    }
}
