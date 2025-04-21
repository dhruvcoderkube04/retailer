<?php

namespace App\Services\Courier;

class YShipService implements CourierInterface
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
        // Replace with actual API logic
        return ['status' => 'order created'];
    }

    public function getRates(array $data): array
    {
        // Replace with actual API logic
        return ['rate' => 100];
    }

    public function trackPackage(string $waybill): array
    {
        // Replace with actual API logic
        return ['status' => 'tracking info'];
    }

    public function checkPincodeAvailability(array $data): array
    {
        // Replace with actual API logic
        return ['available' => true];
    }

    public function courierList(): array
    {
        // Replace with actual API logic
        return ['couriers' => ['YShip']];
    }
}
