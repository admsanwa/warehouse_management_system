<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockModel;
use App\Services\SapService;
use Auth;

class StockController extends Controller
{
    protected $sap;
    protected $default_warehouse;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
        $this->middleware(function ($request, $next) {
            $this->default_warehouse = Auth::user()->warehouse_access;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $param = [
            'ItemCode' => $request->get('item_code'),
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
            'stockNotes' => $request->get('stockNotes', ''),
            'defaultWh' => $request->get('warehouse', $this->default_warehouse),
        ]);
    }

    public function index_old(Request $request)
    {
        $getRecord = StockModel::getRecord($request);
        return view('backend.stock.list', ['stocks' => $getRecord]);
    }
}
