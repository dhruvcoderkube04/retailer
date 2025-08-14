<?php

namespace App\Services;

use App\Models\CourierPartner;
use App\Services\Courier\FShipService;
use App\Services\Courier\LorrigoService;  //for test
use App\Services\Courier\LorrigoServiceLive; // for live
use App\Services\Courier\CourierInterface;

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

    // public static function calculateRatesFromAllCouriers(array $payload): array
    // {
    //     $partners = CourierPartner::where('is_active', true)->get();
    //     $results = [];

    //     foreach ($partners as $partner) {
    //         $service = match ($partner->code) {
    //             'lorrigotest'  => new LorrigoService($partner->toArray()),
    //             'fship'        => new FShipService($partner->toArray()),
    //             'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
    //             default        => null,
    //         };

    //         \Log::info("Trying partner: {$partner->code}");

    //         if (!$service) continue;

    //         try {
    //             $response = $service->calculateRate($payload);

    //             if (!empty($response['status']) && !empty($response['rates'])) {
    //                 foreach ($response['rates'] as $rate) {
    //                     $results[] = [
    //                         'courier_code'        => $partner->code,
    //                         'courier_name'        => $partner->name,
    //                         'zone'                => $rate['order_zone'] ?? ($rate['zone'] ?? null),
    //                         'estimated_delivery'  => $rate['expectedPickup'] ?? ($rate['estimated_delivery'] ?? null),
    //                         'total_price'         => $rate['charge'] ?? ($rate['shipping_charge'] ?? null),
    //                         'shipping_charge'     => $rate['charge'] ?? ($rate['shipping_charge'] ?? null),
    //                         'cod_charge'          => $rate['cod'] ?? ($rate['cod_charge'] ?? null),
    //                         'rto_charge'          => $rate['rtoCharges'] ?? ($rate['rto_charge'] ?? null),
    //                         'weight'              => $payload['shipment_Weight'],
    //                         'service_name'        => $rate['name'] ?? ($rate['courier_name'] ?? null),
    //                         'service_mode'        => $rate['type'] ?? ($rate['service_mode'] ?? null),
    //                     ];
    //                 }
    //             } else {
    //                 \Log::warning("Empty or invalid response for: {$partner->code}", $response);
    //             }

    //         } catch (\Throwable $e) {
    //             \Log::error("Rate fetch failed for {$partner->code}: {$e->getMessage()}");
    //         }
    //     }

    //     // Optional: sort by cheapest rate
    //     // usort($results, fn($a, $b) => $a['total_price'] <=> $b['total_price']);
    //     // dd($results);
    //     return $results;
    // }

    public static function calculateRatesFromAllCouriers(array $payload): array
    {
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
                if (!empty($response['status']) && !empty($response['rates'])) {
                    foreach ($response['rates'] as $rate) {
                        // fship come differnt rto,shipping, cod charge (shiiping + cod)
                        // lorrigo come shipping not comming so charge - rto  charge after get shipping charge
                        // $shippingCharge = null;
                        // if (isset($rate['charge'], $rate['rtoCharges'])) {
                        // } elseif (isset($rate['shipping_charge'])) {
                        //     $shippingCharge = $rate['shipping_charge'];
                        // }
                        
                        $shippingCharge = $rate['fwCharge'] ?? ($rate['shipping_charge'] ?? null);
                        $codCharge = $rate['cod'] ?? ($rate['cod_charge'] ?? null);
                        $rtoCharge = $rate['rtoCharges'] ?? ($rate['rto_charge'] ?? null);

                        // GST amounts only (18%)
                        $gstShippingCharge = $shippingCharge ? round($shippingCharge * 0.18, 2) : null;
                        $gstCodCharge = $codCharge ? round($codCharge * 0.18, 2) : null;
                        $gstRtoCharge = $rtoCharge ? round($rtoCharge * 0.18, 2) : null;

                        $totalPrice = $shippingCharge + $codCharge;

                        $results[] = [
                            'courier_code'         => $partner->code,
                            'courier_name'         => $partner->name,
                            'zone'                 => $rate['order_zone'] ?? ($rate['zone'] ?? null),
                            'estimated_delivery'   => $rate['expectedPickup'] ?? ($rate['estimated_delivery'] ?? null),
                            'total_price'          => $totalPrice,
                            'shipping_charge'      => $shippingCharge,
                            'cod_charge'           => $codCharge,
                            'rto_charge'           => $rtoCharge,
                            'g_shipping_charge'    => $gstShippingCharge,
                            'g_cod_charge'         => $gstCodCharge,
                            'g_rto_charge'         => $gstRtoCharge,
                            'weight'               => $payload['shipment_Weight'],
                            'service_name'         => $rate['name'] ?? ($rate['courier_name'] ?? null),
                            'service_mode'         => $rate['type'] ?? ($rate['service_mode'] ?? null),
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
