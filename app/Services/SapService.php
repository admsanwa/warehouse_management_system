<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SapService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('SAP_BASE_URL');
        $this->apiKey  = env('SAP_API_KEY');
    }

    public function getPurchaseOrders($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/purchase-orders", $param);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch Purchase Orders: ' . $response->body());
        }

        return $response->json();
    }

    public function getStockItems($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/stock", $param);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch data: ' . $response->body());
        }
        return $response->json();
    }


    public function getProductionOrders($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/production-orders", $param);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch Purchase Orders: ' . $response->body());
        }

        return $response->json();
    }

    public function postGrpo($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->post("{$this->baseUrl}/api/outbound/grpo", $param);

        if ($response->failed()) {
            \Log::error('GRPO API Error', [
                'url'      => "{$this->baseUrl}/api/outbound/grpo",
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $param,
            ]);
        }

        return $response->json();
    }
}
