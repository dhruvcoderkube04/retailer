<?php

namespace App\Services\Courier;

use App\Models\MarginManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FShipService implements CourierInterface
{
    protected $apiUrl;
    protected $signature;

    public function __construct()
    {
        $this->apiUrl = config('services.fship.base_url');
        $this->signature = config('services.fship.signature');
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::timeout(300)
                ->withHeaders($this->getHeaders())
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

            $waybill = $response['waybill'];
            $this->registerpick($waybill);

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
        $validator = Validator::make($data, [
            'source_pincode' => 'required|digits:6',
            'destination_pincode' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        try {
            //  Step 2: Send full $data (do not modify payload)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => $this->signature,
            ])->post($this->apiUrl . '/pincodeserviceability', $data);

            if ($response->successful()) {
                return $response->json(); // return original response
            } else {
                Log::error('FShip pincode serviceability check failed', ['response' => $response->body()]);
                return ['status' => false, 'message' => 'Failed to check pincode availability.'];
            }
        } catch (\Exception $e) {
            Log::error('FShip pincode check API error: ' . $e->getMessage());
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
        // Validate inside service
        $validator = Validator::make($data, [
            'source_Pincode' => 'required|digits:6',
            'destination_Pincode' => 'required|digits:6',
            'payment_Mode' => 'required|string',
            'amount' => 'required|numeric',
            'shipment_Weight' => 'required|numeric',
            'shipment_Length' => 'nullable|numeric',
            'shipment_Width' => 'nullable|numeric',
            'shipment_Height' => 'nullable|numeric',
            'volumetric_Weight' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();

        // Now send API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'signature' => $this->signature,
        ])->post($this->apiUrl . '/ratecalculator', $validatedData);

        $data = $response->json();

        if (isset($data['shipment_rates']) && is_array($data['shipment_rates'])) {
            // $marginPercentage = 0.0;
            $marginPercentage = (float)(Auth::user()->userDetail->margin_percentage_tag ?? 0);
            $margin_tag_name = Auth::user()->userDetail->margin_tag_name;

            $getMargin = MarginManagement::where('margin_name', $margin_tag_name)->first();
            if ($getMargin) {
                $marginType = $getMargin->type; // either 'percentage' or 'flat'
                $flatAmount = (float)($getMargin->flat_percentage ?? 0);
                foreach ($data['shipment_rates'] as &$rate) {
                    foreach ($rate as $key => $value) {
                        if ($key !== 'courier_name' && is_numeric($value)) {
                            if ($marginType === 'percentage') {
                                $rate[$key] = round($value + ($value * $marginPercentage / 100), 2);
                            } elseif ($marginType === 'flat') {
                                $rate[$key] = round($value + $flatAmount, 2);
                            }
                        }
                    }
                }
                unset($rate);
            }
        }

        return [
            'status' => true,
            'shipment_rates' => $data['shipment_rates'] ?? [],
        ];
    }

    public function cancelShipment(array $data): array
    {
        $validator = Validator::make($data, [
            "reason"  => "required|max:255",
            "waybill" => "required|numeric",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();

        // Now send API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'signature' => $this->signature,
        ])->post($this->apiUrl . '/cancelorder', $validatedData);

        return $response->json();
    }

    public function reattemptShipment(array $data): array
    {
        // Step 1: Validate input
        $validator = Validator::make($data, [
            'apiorderid'        => 'required|integer',
            'action'            => 'required|in:re-attempt,change-address,change-phone,rto',
            'reattempt_date'    => 'nullable|date_format:Y-m-d\TH:i:s.v\Z',
            'contact_name'      => 'nullable|string|max:255',
            'complete_address'  => 'nullable|string|max:500',
            'landmark'          => 'nullable|string|max:255',
            'mobilenumber'      => 'nullable|string|max:20',
            'remarks'           => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'response' => 'Validation failed.',
                'errors' => $validator->errors()
            ];
        }

        // Step 2: Call FShip API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'signature' => $this->signature,
        ])->post($this->apiUrl . '/reattemptorder', $validator);

        // Step 3: Return response
        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => false,
            'response' => 'Re-attempt request failed.',
            'error' => $response->body()
        ];
    }

    private function registerpick($waybill):string
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl . '/registerpickup', [
                    'waybills' => [$waybill],
                ]);

            // Check if the response was successful
            if ($response->successful()) {
                // Return the response as an array
                return true;
            } else {
                // Log the error and return an empty array or custom error message
                Log::error('Register Pickup ID failed', [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            // Catch any exception, log it, and return an error message
            Log::error('Error while Register Pickup id after order place : ' . $e->getMessage());
            return false;
        }
    }

    public function createShipment($payload):array|bool
    {
        return [];
    }
}

