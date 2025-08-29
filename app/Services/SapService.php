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
        ])->get("{$this->baseUrl}/api/inbound/purchase-orders", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function getStockItems($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/stock", $param)->timeout(60);;

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
        ])->get("{$this->baseUrl}/api/inbound/production-orders", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function getSeries($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/series", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function getWarehouses($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/warehouses", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function getCostCenters($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/cost-centers", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function getItems($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/items", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function getProjects($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->get("{$this->baseUrl}/api/inbound/projects", $param)->timeout(60);;

        if ($response->failed()) {
            throw new \Exception('Failed to fetch: ' . $response->body());
        }

        return $response->json();
    }

    public function postGrpo($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->post("{$this->baseUrl}/api/outbound/grpo", $param)->timeout(60);;

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

    public function postGoodIssue($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->post("{$this->baseUrl}/api/outbound/good-issue", $param)->timeout(60);;

        if ($response->failed()) {
            \Log::error('GI API Error', [
                'url'      => "{$this->baseUrl}/api/outbound/good-issue",
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $param,
            ]);
        }
        return $response->json();
    }

    public function postGoodReceipt($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->post("{$this->baseUrl}/api/outbound/good-receipt", $param)->timeout(60);;

        if ($response->failed()) {
            \Log::error('GR API Error', [
                'url'      => "{$this->baseUrl}/api/outbound/good-receipt",
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $param,
            ]);
        }
        return $response->json();
    }

    
    public function postProdIssue($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->post("{$this->baseUrl}/api/outbound/issue-production", $param)->timeout(60);;

        if ($response->failed()) {
            \Log::error('Production Issue API Error', [
                'url'      => "{$this->baseUrl}/api/outbound/issue-production",
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $param,
            ]);
        }
        return $response->json();
    }
    
    public function postProdReceipt($param)
    {
        $response = Http::withHeaders([
            'Accept'     => 'application/json',
            'X-API-Key'  => $this->apiKey,
        ])->post("{$this->baseUrl}/api/outbound/receipt-production", $param)->timeout(60);;

        if ($response->failed()) {
            \Log::error('Production Receipt API Error', [
                'url'      => "{$this->baseUrl}/api/outbound/receipt-production",
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $param,
            ]);
        }
        return $response->json();
    }
}
