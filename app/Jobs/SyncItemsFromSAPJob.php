<?php

namespace App\Jobs;

use App\Models\ItemsModel;
use Carbon\Carbon;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncItemsFromSAPJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 3;
    public $timeout = 120; // 2 minutes

    public function handle()
    {
        $sapUrl     = config('services.sap_b1.base_url') . '/Items';
        $sapToken   = config('services.sap_b1.token');

        $response   = Http::withHeader([
            'Content-Type'  => 'application/json',
            'Authorization' => "Bearer $sapToken",
        ])->get($sapUrl);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch data from SAP");
        }

        $sapItems   = $response->json()['value'] ?? [];

        foreach ($sapItems as $item) {
            ItemsModel::updateOrCreate(
                ['code' => $item['ItemCode'] ?? null],
                [
                    'name'      => $item['ItemName'],
                    'group'     => $item['ItemGroup'],
                    'uom'       => $item['InventoryUom'],
                    'in_stock'  => $item['InStock'],
                    'stock_min' => $item['ToleranceQty'],
                    'created_at'    => isset($item['CreateDate']) ? Carbon::parse($item['CreateDate']) : now(),
                    'updated_at'    => now(),
                ]
            );
        }
    }
}
