<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;

class BluedartService implements CourierInterface
{
    protected string $apiUrl;
    protected string $signature;

    public function __construct(array $config)
    {
        $this->apiUrl = rtrim($config['api_url'], '/') . '/';
        $this->signature = $config['signature'];
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
        try {
            $response = Http::withHeaders([
                'signature' => $this->signature
            ])->timeout(10)->get($this->apiUrl . 'getallcourier');

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'signature' => $this->signature,
                ])
                ->post($this->apiUrl . $endpoint, $data);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
