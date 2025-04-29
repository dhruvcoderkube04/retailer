<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class LorrigoService implements CourierInterface
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = config('services.lorrigo.base_url');
        $this->token = $this->getToken(); // Fetch token at service creation
    }

    protected function getToken(): string
    {
        return Cache::remember('lorrigo_token', 55 * 60, function () {
            $response = Http::post($this->apiUrl . '/api/auth/login', [
                'email' => config('services.lorrigo.email'),
                'password' => config('services.lorrigo.password'),
            ]);

            if ($response->failed() || !$response->json('user.token')) {
                throw new \Exception('Failed to authenticate with Lorrigo API');
            }

            return $response->json('user.token');
        });
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ];
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
        // Step 1: Validate waybill
        $validator = Validator::make(['waybill' => $waybill],
            [
                'waybill' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        try {
            // Step 2: Make GET request to Lorrigo with Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token, // Assuming $this->token is already set after login
                'Accept' => 'application/json',
            ])->get($this->apiUrl . '/api/order/' . $waybill);

            // Step 3: Handle response
            if ($response->successful()) {
                return $response->json() ?? [];
            } else {
                Log::error('Lorrigo Track Package API failed', [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
                return [
                    'status' => false,
                    'message' => 'Failed to retrieve package tracking information from Lorrigo.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error while tracking package with Lorrigo: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'An error occurred while tracking the package with Lorrigo.',
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

    public function checkPincodeAvailability(array $data): array
    {
        // Step 1: Validate first
        $validator = Validator::make($data, [
            'source_pincode' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // Step 2: Prepare payload
        $payload = [
            'pincode' => (int) ($data['source_pincode'] ?? 0),
        ];

        // dd( $payload);
        // Step 3: Proceed to API call
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/hub/pincode', $payload);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['city']) && isset($result['state'])) {
                    return [
                        'status' => true,
                        'available' => true,
                        'city' => $result['city'],
                        'state' => $result['state'],
                        'message' => 'Service available for this pincode.',
                    ];
                } else {
                    return [
                        'status' => true,
                        'available' => false,
                        'message' => 'Service not available for this pincode.',
                    ];
                }
            } else {
                Log::error('Pincode serviceability check failed', ['response' => $response->body()]);
                return ['status' => false, 'message' => 'Failed to check pincode availability.'];
            }
        } catch (\Exception $e) {
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

    // public function calculateRate(array $data): array
    // {
    //     $response = Http::withHeaders([
    //         'Content-Type' => 'application/json',
    //         'signature' => $this->signature,
    //     ])->post($this->apiUrl . '/ratecalculator', $data);

    //     return $response->json();
    // }

    public function calculateRate(array $data): array
    {
        // dd($data);
        // Step 1: Validate the incoming data
        $validator = Validator::make($data, [
            'source_Pincode' => 'required|digits:6',
            'destination_Pincode' => 'required|digits:6',
            'payment_Mode' => 'required|in:0,1', // 0=COD, 1=Prepaid
            'shipment_Weight' => 'required|numeric',
            'shipment_Height' => 'required|numeric',
            'shipment_Length' => 'required|numeric',
            'shipment_Width' => 'required|numeric',
            // 'sizeUnit' => 'required|string',
            'volumetric_Weight' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // Step 2: Prepare payload for Lorrigo
        $payload = [
            'boxHeight' => (string) ($data['shipment_Height'] ?? '10'),
            'boxLength' => (string) ($data['shipment_Length'] ?? '10'),
            'boxWidth' => (string) ($data['shipment_Width'] ?? '10'),
            'collectableAmount' => (string) ($data['amount']),
            'deliveryPincode' => (string) $data['destination_Pincode'],
            'paymentType' => (int) $data['payment_Mode'] ?? 1,
            'pickupPincode' => (string) $data['source_Pincode'],
            'sizeUnit' => 'cm',
            'weight' => (string) $data['shipment_Weight'],
            'weightUnit' => 'kg',
        ];
        // Step 3: Call Lorrigo API
        try {
            // dd($payload);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/ratecalculator', $payload);
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Lorrigo Rate Calculation failed', ['response' => $response->body()]);
                return ['status' => false, 'message' => 'Failed to calculate rate.'];
            }
        } catch (\Exception $e) {
            Log::error('Error during Lorrigo Rate Calculation: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while calculating rate.', 'error' => $e->getMessage()];
        }
    }

}
