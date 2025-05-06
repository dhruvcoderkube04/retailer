<?php

namespace App\Services\Courier;

interface CourierInterface
{
    public function createOrder(array $data): array;
    public function trackPackage(string $waybill): array;
    public function checkPincodeAvailability(array $data): array;
    public function courierList(): array;
    public function addWarehouse(array $data): array;
    public function updateWarehouse(array $data): array;
    public function calculateRate(array $data): array;

    public function cancelShipment(array $data): array;
    public function reattemptShipment(array $data): array;
    public function createShipment(array $payload): array|bool;

}
