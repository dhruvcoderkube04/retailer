<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FShipService implements CourierInterface
{
    protected $apiUrl;
    protected $signature;

    public function __construct()
    {
        $this->apiUrl = 'https://capi-qc.fship.in/api'; // Define the base URL here
        $this->signature = '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb'; // Define your signature or load from config
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl . '/createforwardorder', $data);

            if ($response->failed() || $response->json() === null) {
                Log::error('Failed to create order', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => $data,
                ]);

                return [
                    'status' => false,
                    'message' => 'Failed to create order. Invalid response from API.',
                    'data' => [],
                ];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Exception while creating order', [
                'error' => $e->getMessage(),
                'request' => $data,
            ]);

            return [
                'status' => false,
                'message' => 'Exception occurred while creating order.',
                'error' => $e->getMessage(),
            ];
        }
    }


    public function trackPackage(string $waybill): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl . '/trackinghistory', [
                    'waybill' => $waybill,
                ]);

            // Check if the response was successful
            if ($response->successful()) {
                // Return the response as an array
                return $response->json() ?? []; // Return an empty array if response is null
            } else {
                // Log the error and return an empty array or custom error message
                Log::error('Track package API failed', [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
                return [
                    'status' => false,
                    'message' => 'Failed to retrieve package tracking information.',
                ];
            }
        } catch (\Exception $e) {
            // Catch any exception, log it, and return an error message
            Log::error('Error while tracking package: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'An error occurred while tracking the package.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function courierList(): array
    {
        try {
            $response = Http::withHeaders([
                'signature' => $this->signature
            ])->get($this->apiUrl . '/getallcourier');

            // If request failed or response is not valid JSON
            if ($response->failed() || $response->json() === null) {
                Log::error('Failed to fetch courier list', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return []; // fallback to empty array
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Exception while fetching courier list', [
                'error' => $e->getMessage()
            ]);

            return []; // fallback to empty array
        }
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'signature' => $this->signature,
        ];
    }

    public function checkPincodeAvailability(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => $this->signature,
            ])->post($this->apiUrl . '/pincodeserviceability', $data);

            if ($response->successful()) {
                return $response->json(); // return response as array
            } else {
                // Log and return an empty array or appropriate error message
                Log::error('Pincode serviceability check failed', ['response' => $response->body()]);
                return ['status' => false, 'message' => 'Failed to check pincode availability.'];
            }
        } catch (\Exception $e) {
            // Log the exception and return an error response
            Log::error('Pincode check API error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while checking pincode availability.'];
        }
    }

    public function addWarehouse(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => $this->signature,
            ])->post($this->apiUrl . '/addwarehouse', $data);

            // Check if the response was successful
            if ($response->successful()) {
                // Return the response as an array or fallback to an empty array if JSON is null
                return $response->json() ?? [];
            } else {
                // Log the error response and return a custom error message in an array
                Log::error('Add Warehouse API failed', [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
                return [
                    'status' => false,
                    'message' => 'Failed to add warehouse. Please try again.',
                ];
            }
        } catch (\Exception $e) {
            // Catch any exception, log it, and return a custom error message
            Log::error('Error while adding warehouse: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'An error occurred while adding the warehouse.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function updateWarehouse(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => $this->signature,
            ])->post($this->apiUrl . '/updatewarehouse', $data);

            if ($response->failed()) {
                Log::error('Courier API request failed.', [
                    'url' => $this->apiUrl . 'updatewarehouse',
                    'request_data' => $data,
                    'response_body' => $response->body(),
                    'status_code' => $response->status(),
                ]);

                return [
                    'status' => false,
                    'message' => 'Courier API call failed.',
                    'error' => $response->body(),
                    'data' => [],
                ];
            }

            $resData = $response->json();

            return [
                'status' => $resData['status'] ?? false,
                'message' => $resData['message']
                            ?? $resData['response']
                            ?? 'No message returned from API.',
                'data' => $resData,
            ];

        } catch (\Exception $e) {
            Log::error('Error in API request:', [
                'error' => $e->getMessage(),
                'request_data' => $data,
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while calling the courier API.',
                'error' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function calculateRate(array $data): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'signature' => $this->signature,
        ])->post($this->apiUrl . '/ratecalculator', $data);

        return $response->json();
    }
}
