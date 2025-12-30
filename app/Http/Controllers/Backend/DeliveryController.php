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
        // get series based on user
        $getYear = now()->year;
        $year = substr($getYear, -2);

        if (Auth::user()->default_series_prefix === 'SBY') {
            $series = 'SBY-' . $year;

            $getSeries = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'ObjectCode' => 67, 'SeriesName' => $series]);
            if (!empty($getSeries['data'][0]['Series'])) {
                $series = $getSeries['data'][0]['Series'];
            }
        }

        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 5),
            "U_MEB_NO_IO" => $request->get('io'),
            "DocNum" => $request->get('inv_transfer'),
            "DocStatus" =>  $request->get('status', 'O'),
            "Series" =>  $series ?? $request->get('series'),
        ];

        // get data SAP
        $getInvtf = $this->sap->getInventoryTransfers($param);
        if (empty($getInvtf) || $getInvtf['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // filter 
        $getInvtf['data'] = collect($getInvtf['data'])
            ->map(function ($doc) use ($request) {

                $lines = collect($doc['Lines'] ?? []);

                // 🔍 Filter ItemCode (LIKE)
                if ($request->filled('ItemCode')) {
                    $keyword = strtolower($request->get('ItemCode'));
                    $lines = $lines->filter(
                        fn($line) =>
                        isset($line['ItemCode']) &&
                            str_contains(strtolower($line['ItemCode']), $keyword)
                    );
                }

                // 🔍 Filter ItemName (LIKE)
                if ($request->filled('ItemName')) {
                    $keyword = strtolower($request->get('ItemName'));
                    $lines = $lines->filter(
                        fn($line) =>
                        isset($line['ItemName']) &&
                            str_contains(strtolower($line['ItemName']), $keyword)
                    );
                }

                // 🔥 Filter SEMIFG (SF)
                $lines = $lines->filter(
                    fn($line) =>
                    isset($line['ItemCode']) &&
                        str_starts_with(strtoupper($line['ItemCode']), 'SF')
                );

                $doc['Lines'] = $lines->values()->all();

                return $doc;
            })
            ->filter(fn($doc) => count($doc['Lines']) > 0)
            ->values()
            ->all();

        // pagination and merger data
        $currentCount   = $getInvtf['total'] ?? count($getInvtf['data'] ?? []);
        $totalPages     = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        $sapDocEntry    = collect($getInvtf['data'])->pluck('DocEntry')->toArray();
        $deliveryQuery    = DeliveryModel::whereIn('doc_entry', $sapDocEntry);

        if ($request->filled('deliv_status')) {
            $deliveryQuery->where('status', $request->deliv_status);
        }
        $deliveryData    = $deliveryQuery->get()->keyBy('doc_entry');

        $mergedData = collect($getInvtf['data'])->map(function ($sapItem) use ($deliveryData) {
            $docEntry = $sapItem['DocEntry'];

            return [
                'sap'     => $sapItem,
                'delivery' => $deliveryData[$docEntry] ?? null,
            ];
        })->filter(function ($row) use ($request) {
            if ($request->filled('deliv_status')) {
                return !is_null($row['delivery']);
            }
            return true;
        })->values();

        return view('backend.delivery.list', [
            'mergedData'  => $mergedData,
            'getInvtf'    => $getInvtf['data'],
            'page'        => $getInvtf['page'],
            'limit'       => $getInvtf['limit'],
            'total'       => $getInvtf['total'],
            'totalPages'  => $totalPages,
        ]);
    }

    public function estimate(Request $request, $docEntry)
    {
        // dd($docEntry);
        // validation
        $request->validate([
            'status'    => "required",
            "date"      => "required|date",
            "remark"    => "required|string|min:2"
        ]);

        $user   = Auth::user()->username;

        $param = [
            "DocEntry" => $docEntry,
        ];

        // get data
        $getInvtf = $this->sap->getInventoryTransfers($param);
        if (empty($getInvtf) || $getInvtf['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP, Silakan coba lagi nanti');
        }
        $sapData    = collect($getInvtf['data'] ?? [])->firstWhere('DocEntry', $docEntry);
        if (!$sapData) {
            return back()->with('error', "Data dengan docEntry {$docEntry} tersebut tidak ditemukan di SAP");
        }
        $lines = $sapData['Lines'][0] ?? [];

        // save db
        $delivery = new DeliveryModel();
        $delivery->doc_entry    = $sapData['DocEntry'];
        $delivery->io           = $sapData['U_MEB_NO_IO'];
        $delivery->inv_transfer = $sapData['DocNum'];
        $delivery->prod_no      = $lines['ItemCode'];
        $delivery->prod_desc    = $lines['ItemName'];
        $delivery->series       = $sapData['Series'];
        $delivery->status       = $request->status;
        $delivery->date         = $request->date;
        $delivery->remark       = $request->remark;
        $delivery->is_temp      = 1;
        $delivery->tracker_by   = $user;
        $delivery->save();


        // dd($request->all());
        return redirect()->back()->with("success", "Telah berhasil tracking delivery product: {$lines['ItemCode']} menjadi {$request->status}");
    }

    public function history(Request $request)
    {
        $getRecord = DeliveryModel::getRecordTwo()->paginate('10');

        return view('backend.delivery.history', compact('getRecord'));
    }

    public function updateDeliveryTemp()
    {
        DeliveryModel::where('is_temp', 0)->update(['is_temp' => 1]);
        return response()->json(['status' => 'success']);
    }
}
