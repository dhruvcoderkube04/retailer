<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class ProfessionalService implements CourierInterface
{
    protected string $apiUrl;
    protected string $signature;

    public function __construct(array $config)
    {
        $this->apiUrl = 'https://capi-qc.fship.in/api';
        $this->signature = '213';
    }

    public function createOrder(array $data): array
    {
        return $this->post('createforwardorder', $data);
    }

    public function getRates(array $data): array
    {
        return $this->post('ratecalculator', $data);
    }

    public function trackPackage(string $waybill): array
    {
        return $this->post('trackinghistory', ['waybill' => $waybill]);
    }

    public function checkPincodeAvailability(array $data): array
    {
        return $this->post('checkserviceability', $data);
    }

    public function courierList(): array
    {
        return $this->get('getallcourier');
    }

    public function addWarehouse(array $data): array
    {
        return $this->post('addwarehouse', $data);
    }

    public function updateWarehouse(array $data): array
    {
        return $this->post('updatewarehouse', $data);
    }

    public function calculateRate(array $data): array
    {
        return $this->post('ratecalculator', $data);
    }

    protected function get(string $endpoint): array
    {
        try {
            $response = Http::withHeaders([
                'signature' => $this->signature,
            ])->timeout(10)->get($this->apiUrl . $endpoint);

            return $this->parseResponse($response);
        } catch (\Exception $e) {
            Log::error("GET $endpoint failed", ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Request failed',
                'error' => $e->getMessage()
            ];
        }
    }

    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => $this->signature,
            ])->timeout(10)->post($this->apiUrl . $endpoint, $data);

            return $this->parseResponse($response);
        } catch (\Exception $e) {
            Log::error("POST $endpoint failed", ['error' => $e->getMessage(), 'data' => $data]);
            return [
                'status' => false,
                'message' => 'Request failed',
                'error' => $e->getMessage()
            ];
        }
    }

    protected function parseResponse(Response $response): array
    {
        if ($response->successful()) {
            $json = $response->json();
            return is_array($json) ? $json : ['status' => false, 'message' => 'Invalid response format'];
        }

        Log::error("API response error", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return [
            'status' => false,
            'message' => 'API returned error',
            'code' => $response->status(),
            'error' => $response->body(),
        ];
    }
}
