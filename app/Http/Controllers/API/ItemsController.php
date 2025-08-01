<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SyncItemsFromSAPJob;
use App\Models\ItemsModel;
use App\Models\StockModel;
use Carbon\Carbon;
use Http;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $sapUrl     = config('services.sap_b1.base_url') . '/Items';
            $sapToken   = config('services.sap_b1.token');

            $response   = Http::withHeader([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $sapToken"
            ])->get($sapUrl);

            if ($response->failed()) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Failed to retrieve data from SAP B1',
                    'error'     => $response->body()
                ], $response->status());
            }

            $sapItems       = $response->json()['value'];
            $insertedItems  = [];

            foreach ($sapItems as $item) {
                $newItem = ItemsModel::updateOrCreate(
                    ['code' => $item['ItemCode'] ?? null],
                    [
                        'name'          => $item['ItemName'],
                        'group'         => $item['ItemGroup'],
                        'uom'           => $item['InventoryUom'],
                        'in_stock'      => $item['InStock'],
                        'stock_min'     => $item['ToleranceQty'],
                        'created_at'    => isset($item['CreateDate']) ? Carbon::parse($item['CreateDate']) : now(),
                        'updated_at'    => now(),
                    ]
                );

                $insertedItems[] = $newItem;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Succesfully synced from SAP B1',
                'data'    => $insertedItems,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function syncFromSAP()
    {
        SyncItemsFromSAPJob::dispatch();

        return response()->json([
            'success' => true,
            'message' => 'Sync job has been dispatched',
        ]);
    }
}
