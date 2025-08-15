<?php

namespace App\Services\Courier;

use App\Models\LorrigoCarrier;
use App\Models\MarginManagement;
use Illuminate\Support\Facades\Auth;
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
        $this->apiUrl = config('services.lorrigotest.base_url');
        $this->token = $this->getToken(); // Fetch token at service creation
    }

    protected function getToken(): string
    {
        return Cache::remember('lorrigo_token', 55 * 60, function () {
            $response = Http::post($this->apiUrl . '/api/auth/login', [
                'email' => config('services.lorrigotest.email'),
                'password' => config('services.lorrigotest.password'),
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
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/order/b2c', $data);

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

            Log::info('createOrder In Lorrgido Test (Retailer side)', [
                'status' => $response->status(),
                'body' => $response->body(),
                'request' => $response->json(),
            ]);
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
        $validator = Validator::make(
            ['waybill' => $waybill],
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
                Log::info('trackPackage In Lorrgido Test (Retailer side)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => $response->json(),
                ]);
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
                Log::info('checkPincodeAvailability In Lorrgido Test (Retailer side)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => $response->json(),
                ]);
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
            $payload = [
                'name' => 'TEST_' . $data['warehouseName'], // must be unique
                'pincode' => $data['pincode'],
                'address1' => substr($data['addressLine1'], 0, 100), // max 100 chars
                'phone' => $data['phoneNumber'],
                'contactPersonName' => $data['contactName'],
                'isRTOAddressSame' => true,
                'rtoAddress' => "",
                'rtoPincode' => ""
            ];

            Log::info('Sending warehouse to Lorrigo Test', ['payload' => $payload]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/hub', $payload);

            if ($response->successful()) {
                $json = $response->json();
                Log::info('Lorrigo warehouse added', ['response' => $json]);
                return [
                    'status' => true,
                    'warehouseId' => @$json['hub']['_id'],
                    'data' => $json,
                ];
            }

            Log::error('Lorrigo Add Warehouse Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => false,
                'message' => 'Failed to add warehouse via Lorrigo.',
            ];
        } catch (\Exception $e) {
            Log::error('Lorrigo Add Warehouse Exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Lorrigo warehouse creation error.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function updateWarehouse(array $data): array
    {
        try {
            $payload = [
                'name' => $data['warehouseName'], // must be unique
                'pincode' => $data['pincode'],
                'address1' => substr($data['addressLine1'], 0, 100), // max 100 chars
                'phone' => $data['phoneNumber'],
                'contactPersonName' => $data['contactName'],
                'isRTOAddressSame' => true,
                'rtoAddress' => "",
                'rtoPincode' => ""
            ];

            Log::info('Sending warehouse to Lorrigo Test', ['payload' => $payload]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/hub', $payload);

            if ($response->successful()) {
                $json = $response->json();
                Log::info('Lorrigo warehouse added Lorrigo Test', ['response' => $json]);
                return [
                    'status' => true,
                    'warehouseId' => @$json['hub']['_id'],
                    'data' => $json,
                ];
            }

            Log::error('Lorrigo Add Warehouse Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => false,
                'message' => 'Failed to add warehouse via Lorrigo.',
            ];
        } catch (\Exception $e) {
            Log::error('Lorrigo Add Warehouse Exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Lorrigo warehouse creation error.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function calculateRate(array $data): array
    {
        // Step 1: Validate the incoming data
        $validator = Validator::make($data, [
            'source_Pincode' => 'required|digits:6',
            'destination_Pincode' => 'required|digits:6',
            'payment_Mode' => 'required|string',
            'shipment_Weight' => 'required|numeric',
            'shipment_Height' => 'nullable|numeric',
            'shipment_Length' => 'nullable|numeric',
            'shipment_Width' => 'nullable|numeric',
            // 'sizeUnit' => 'required|string',
            'volumetric_Weight' => 'nullable|string',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // Step 2: Prepare payload for Lorrigo
        $payload = [
            'boxHeight' => (string) ($data['shipment_Height'] ?? "10"),
            'boxLength' => (string) ($data['shipment_Length'] ?? "10"),
            'boxWidth' => (string) ($data['shipment_Width'] ?? "10"),
            'collectableAmount' => (string) ($data['amount']) ?? "",
            'deliveryPincode' => (string) $data['destination_Pincode'],
            // 'paymentType' => $data['payment_Mode'] == 'COD' ? 1 : 0, // 1=COD, 0=Prepaid
            'paymentType' => 1,
            'pickupPincode' => (string) $data['source_Pincode'],
            'sizeUnit' => 'cm',
            'weight' => (string) $data['shipment_Weight'],
            'weightUnit' => 'kg',
        ];
        // dd( $payload);
        // Step 3: Call Lorrigo API
        try {
            // dd($payload);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/ratecalculator', $payload);

            if ($response->successful()) {
                // return $response->json();
                Log::info('calculateRate In Lorrgido Test (Retailer side)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => $response->json(),
                ]);

                $responseData = $response->json();
                $rates = collect($responseData['rates'])->where('nickName', 'BDS')->where('minWeight', (float) $data['shipment_Weight']);
                // dd($rates,(float) $data['shipment_Weight']);
                // $marginPercentage = (float)(Auth::user()->userDetail->margin_percentage_tag ?? 0);
                $marginTagName = Auth::check() ? Auth::user()->userDetail->margin_tag_name : null;
                if (!empty($marginTagName)) {
                    $getMargin = MarginManagement::where('margin_name', $marginTagName)->first();
                } else {
                    $getMargin = MarginManagement::where('default', 1)->first();
                }
                // dd($getMargin,Auth::user()->userDetail->margin_tag_name,$rates);
                if (!empty($rates) && $getMargin) {
                    $marginType = $getMargin->type; // 'percentage' or 'flat'
                    $flatAmount = (float)($getMargin->flat_percentage ?? 0);

                    foreach ($rates as $index => $rate) {
                        foreach ($rate as $key => $value) {
                            if ($key !== 'name' && is_numeric($value)) {
                                if ($marginType === 'percentage') {
                                    $rate[$key] = round($value + ($value *  $flatAmount / 100), 2);
                                } elseif ($marginType === 'flat') {
                                    $rate[$key] = round($value + $flatAmount, 2);
                                }
                            }
                        }
                        $rates[$index] = $rate;
                    }
                    // unset($rate); // good practice
                }
                return [
                    'status' => true,
                    'rates' => $rates->toArray(),
                ];
            } else {
                Log::error('Lorrigo Rate Calculation failed', ['response' => $response->body()]);
                return ['status' => false, 'message' => 'Failed to calculate rate.'];
            }
        } catch (\Exception $e) {
            Log::error('Error during Lorrigo Rate Calculation: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while calculating rate.', 'error' => $e->getMessage()];
        }
    }

    public function cancelShipment(array $data): array
    {
        $validator = Validator::make($data, [
            'orderId' => 'required'
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        $payload = [
            'orderIds' =>  [
                $data['orderId']
            ],
            'type' => 'shipment'
        ];

        Log::info("CANCEL POYLOAD");
        Log::info(json_encode($payload));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ])->post($this->apiUrl . '/api/shipment/cancel', $payload);

        if ($response->successful()) {
            Log::info('Lorrigo test Cancel Order Successfull', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            return [true, "Lorrigo test cancle order success", 'cancle'];
        } else {
            Log::info('Lorrigo test Cancel Order fail', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            return [false, "Lorrigo test cancle order fail", 'cancle'];
        }
    }

    // public function reattemptShipment(array $data): array
    // {
    //     return [];
    // }

    public function reattemptShipment(array $data): array
    {
        Log::info('Reattempt payload for Lorrigo Test', $data);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/shipment/order-reattempt', $data);

            if ($response->successful()) {
                Log::info('Reattempt Shipment Success', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $response->json();
            }

            Log::error('Reattempt Shipment Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Reattempt failed'];
        } catch (\Throwable $e) {
            Log::error('Reattempt Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['error' => 'Exception occurred during reattempt'];
        }
    }

    public function courierList(): array
    {
        return [];
    }


    public function createShipment(array $payload): array|bool
    {
        Log::info('Payload being sent Lorrigo Test', $payload);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->apiUrl . '/api/shipment/v2', $payload);

            if ($response->successful()) {
                Log::info('createShipment In Lorrgido Test (Retailer side)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => $response->json(),
                ]);
                return $response->json();
            }

            Log::error('Create shipment failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('Create shipment exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
