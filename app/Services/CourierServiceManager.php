<?php

namespace App\Services;

use App\Models\CourierPartner;
use App\Models\GstConfiguration;
use App\Models\MarginManagement;
use App\Services\Courier\FShipService;
use App\Services\Courier\LorrigoService;  //for test
use App\Services\Courier\LorrigoServiceLive; // for live
use App\Services\Courier\CourierInterface;
use Illuminate\Support\Facades\Auth;

class CourierServiceManager
{
    public static function getServiceByCode(string $code): CourierInterface
    {
        // Find partner by code
        $partner = CourierPartner::where('code', $code)->firstOrFail();

        // Get the active courier partner from the database
        // $partner = CourierPartner::where('is_active', true)->firstOrFail();
        // Choose the appropriate courier service based on the code
        return match ($partner->code) {
            'fship'    => new FShipService($partner->toArray()),
            'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
            'lorrigotest'  => new LorrigoService($partner->toArray()),
            default    => throw new \Exception("Unsupported courier: {$partner->code}")
        };
    }

    public static function getAllServicesForWarehouse(): array
    {
        $partners = CourierPartner::all(); // all, not just active

        $services = [];

        foreach ($partners as $partner) {
            $service = match ($partner->code) {
                'fship'    => new FShipService($partner->toArray()),
                'lorrigotest'  => new LorrigoService($partner->toArray()),
                'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
                default    => null,
            };

            if ($service) {
                $services[] = [
                    'service' => $service,
                    'partner' => $partner,
                ];
            }
        }

        return $services;
    }

    public static function getService(): CourierInterface
    {
        // Get the active courier partner from the database
        $partner = CourierPartner::where('is_active', true)->firstOrFail();

        // Choose the appropriate courier service based on the code
        return match ($partner->code) {
            'fship' => new FShipService($partner->toArray()),
            'lorrigotest' => new LorrigoService($partner->toArray()),
            'lorrigolive' => new LorrigoServiceLive($partner->toArray()),
            default => throw new \Exception("Unsupported courier: {$partner->code}")
        };
    }


    public static function getAllServicesForTracking(): array
    {
        $partners = CourierPartner::all(); // Fetch all, active and inactive
        $services = [];

        foreach ($partners as $partner) {
            $service = match ($partner->code) {
                'fship'        => new FShipService($partner->toArray()),
                'lorrigotest'  => new LorrigoService($partner->toArray()),
                'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
                default        => null,
            };

            if ($service) {
                $services[$partner->code] = $service;
            }
        }

        return $services;
    }

    public static function calculateRatesFromAllCouriers(array $payload): array
    {

        // Margin Calculation
        $user = Auth::user();
        $marginPercentage = (float) ($user->userDetail?->margin_percentage_tag ?? 0);
        $marginTagName = $user->userDetail?->margin_tag_name;

        $getMargin = MarginManagement::where('margin_name', $marginTagName)->first();

        if ($getMargin) {
            $marginType = $getMargin->type; // 'percentage' or 'flat'
            $flatAmount = (float) ($getMargin->flat_percentage ?? 0);
        } else {
            $marginType = 'percentage';
            $flatAmount = 18;
        }

        // GST Calculation
        $gst_config = GstConfiguration::where('status', true)->first();
        if ($gst_config) {
            if ($gst_config->gst_mode == 'same') {
                // Use only GST field, default to 0 if null
                $gstRate = floatval($gst_config->gst ?? 0);
            } else {
                // Sum IGST + CGST + SGST, default to 0 if null
                $igstRate = floatval($gst_config->igst ?? 0);
                $cgstRate = floatval($gst_config->cgst ?? 0);
                $sgstRate = floatval($gst_config->sgst ?? 0);

                $gstRate = $igstRate + $cgstRate + $sgstRate;
            }
        } else {
            // default 18 % set if not found
            $gstRate = 18.0;
        }

        $partners = CourierPartner::where('is_active', true)->get();
        $results = [];

        foreach ($partners as $partner) {
            $service = match ($partner->code) {
                'lorrigotest'  => new LorrigoService($partner->toArray()),
                'fship'        => new FShipService($partner->toArray()),
                'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
                default        => null,
            };

            \Log::info("Trying partner: {$partner->code}");

            if (!$service) continue;
            try {
                $response = $service->calculateRate($payload);
                \Log::info("Response from partner {$partner->code}: " . json_encode($response));
                if (!empty($response['status']) && !empty($response['rates'])) {
                    foreach ($response['rates'] as $rate) {

                        $shippingCharge = $rate['fwCharge'] ?? ($rate['shipping_charge'] ?? null);
                        $codCharge = $rate['cod'] ?? ($rate['cod_charge'] ?? null);
                        $rtoCharge = $rate['rtoCharges'] ?? ($rate['rto_charge'] ?? null);

                        if ($marginType === 'percentage' && $marginPercentage > 0) {
                            $shippingCharge = $shippingCharge +  ($shippingCharge * $marginPercentage) / 100;
                            $codCharge = $codCharge +  ($codCharge * $marginPercentage) / 100;
                            $rtoCharge = $rtoCharge +  ($rtoCharge * $marginPercentage) / 100;
                        } elseif ($marginType === 'flat' && $flatAmount > 0) {
                            $shippingCharge = $shippingCharge + $flatAmount;
                            $codCharge = $codCharge + $flatAmount;
                            $rtoCharge = $rtoCharge + $flatAmount;
                        }

                        $totalPrice = $shippingCharge + $codCharge;

                        $results[] = [
                            'courier_code'         => $partner->code,
                            'courier_name'         => $partner->name,
                            'zone'                 => $rate['order_zone'] ?? ($rate['zone'] ?? null),
                            'estimated_delivery'   => $rate['expectedPickup'] ?? ($rate['estimated_delivery'] ?? null),
                            'total_price'          => $totalPrice,
                            // 'total_price_wgst'     => $totalPricewgst,
                            'shipping_charge'      => $rate['fwCharge'] ?? ($rate['shipping_charge'] ?? null),
                            'cod_charge'           => $rate['cod'] ?? ($rate['cod_charge'] ?? null),
                            'rto_charge'           => $rate['rtoCharges'] ?? ($rate['rto_charge'] ?? null),
                            // 'g_shipping_charge'    => $gstShippingCharge,
                            // 'g_cod_charge'         => $gstCodCharge,
                            // 'g_rto_charge'         => $gstRtoCharge,
                            'weight'               => $payload['shipment_Weight'],
                            'service_name'         => $rate['name'] ?? ($rate['courier_name'] ?? null),
                            'service_mode'         => ($rate['type'] ?? $rate['service_mode'] ?? null)
                                                        ? ucfirst($rate['type'] ?? $rate['service_mode'])
                                                        : null,
                            // 'service_mode'         => $rate['type'] ?? ($rate['service_mode'] ?? null),
                            'courierId'            => $rate['courierId'] ?? null,
                            'nickName'             => $rate['nickName'] ?? null,
                            'carrierID'            => $rate['id'] ?? null,
                            'logoUrl'              => $rate['logoUrl'] ?? '',
                            'delivery_score'       => self::calculateDeliveryScore($rate['estimated_delivery'] ?? null),
                        ];
                    }
                } else {
                    \Log::warning("Empty or invalid response for: {$partner->code}", $response);
                }
            } catch (\Throwable $e) {
                \Log::error("Rate fetch failed for {$partner->code}: {$e->getMessage()}");
            }
        }

        // Sort by total price first, then by delivery score (lower is better)
        usort($results, function ($a, $b) {
            if ($a['total_price'] === $b['total_price']) {
                return $a['delivery_score'] <=> $b['delivery_score'];
            }
            return $a['total_price'] <=> $b['total_price'];
        });
        return $results;
    }

    // Helper to convert estimated delivery like "2 days" or "Tomorrow" into score (lower is better)
    protected static function calculateDeliveryScore($delivery): int
    {
        if (!$delivery) return 999; // fallback worst case

        if (is_numeric($delivery)) return (int) $delivery;

        $delivery = strtolower(trim($delivery));

        if (str_contains($delivery, 'today')) return 0;
        if (str_contains($delivery, 'tomorrow')) return 1;
        if (preg_match('/(\d+)\s*day/', $delivery, $match)) {
            return (int) $match[1];
        }

        return 999; // fallback
    }

    public static function checkServiceAvailableFromAllCouriers(array $payload): array
    {
        $partners = CourierPartner::where('is_active', true)->get();

        foreach ($partners as $partner) {
            $service = match ($partner->code) {
                'lorrigotest'  => new LorrigoService($partner->toArray()),
                'fship'        => new FShipService($partner->toArray()),
                'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
                default        => null,
            };

            \Log::info("Trying partner: {$partner->code}");

            if (!$service) continue;

            try {
                $response = $service->checkPincodeAvailability($payload);

                // Normalize response
                $normalized = [
                    'city' => $response['city'] ?? $response['source'] ?? null,
                    'state' => $response['state'] ?? null,
                    'source' => $response['source'] ?? null,
                    'destination' => $response['destination'] ?? null,
                    'zone' => $response['zone'] ?? null,
                    'pickup' => $response['pickup'] ?? 'No',
                    'delivery' => $response['delivery'] ?? 'No',
                    'cod' => $response['cod'] ?? 'No',
                    'message' => $response['message'] ?? $response['response'] ?? 'No message',
                    'available' => $response['available'] ?? $response['status'] ?? false,
                ];

                if ($normalized['available']) {
                    return [
                        'success' => true,
                        'courier' => $partner->code,
                        'data' => $normalized,
                    ];
                }
            } catch (\Throwable $e) {
                \Log::error("Availability check failed for {$partner->code}: {$e->getMessage()}");
            }
        }

        return [
            'success' => false,
            'message' => 'No courier is available for the given pincode.',
        ];
    }
}
