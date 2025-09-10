<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;
use App\Services\SapService;
use Illuminate\Support\Arr;

class InventorytfController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function create() {}

    public function list(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 5),
            "DocNum" => $request->get('number'),
            "DocDate" => $request->get('date'),
            "DocStatus" =>  $request->get('status', 'O'),
            "Series" =>  $request->get('series'),
        ];
        $getInvtf = $this->sap->getInventoryTransfers($param);
        if (empty($getInvtf) || $getInvtf['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // Filter ulang kalau ada Series
        if (!empty($param['Series']) && !empty($getInvtf['data'])) {
            $getInvtf['data'] = collect($getInvtf['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            $getInvtf['total'] = count($getInvtf['data']);
        }
        $currentCount = $getInvtf['total'] ?? count($getInvtf['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view('api.inventorytf.list', [
            'getInvtf'      => $getInvtf['data'],
            'page'        => $getInvtf['page'],
            'limit'       => $getInvtf['limit'],
            'total'       => $getInvtf['total'],
            'totalPages'  => $totalPages,
        ]);
    }


    public function view(Request $request)
    {
        $param = [
            "Page" => 1,
            "limit" => 1,
            "DocNum" =>  $request->query('docNum'),
            "DocEntry" => $request->query('docEntry')
        ];
        $getInvtf = $this->sap->getInventoryTransfers($param);

        if (empty($getInvtf) || !Arr::get($getInvtf, 'success')) {
            return back()->with(
                'error',
                Arr::get($getInvtf, 'message', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.')
            );
        }
        $invtf = Arr::get($getInvtf, 'data.0', []);
        $lines = Arr::get($invtf, 'Lines', []);

        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $invtf['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);
        return view(
            'api.inventorytf.view',
            [
                'invtf'    => $invtf,
                'lines' => $lines,
                'series' => $series,
            ]
        );
    }
}
