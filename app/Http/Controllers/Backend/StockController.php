<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockModel;
use App\Services\SapService;

class StockController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function index(Request $request)
    {
        $param = [
            'ItemCode' => $request->get('item_code'),
            // "WhsCode" =>  'BK001',
            "ItemName" => $request->get('item_desc'),
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 10),
        ];
        $getRecord      = $this->sap->getStockItems($param);

        if (empty($getRecord) || $getRecord['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        $currentCount = $getProds['total'] ?? count($getProds['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;
        return view("api.stock.list", [
            'getRecord'      => $getRecord['data'] ?? [],
            'page'        => $getRecord['page'],
            'limit'       => $getRecord['limit'],
            'total'       => $getRecord['total'],
            'totalPages'  => $totalPages,
            'stockNotes' => $request->get('stockNotes', 2),
            'defaultWh' => 'BK001',
        ]);
    }

    public function index_old(Request $request)
    {
        $getRecord = StockModel::getRecord($request);
        return view('backend.stock.list', ['stocks' => $getRecord]);
    }
}
