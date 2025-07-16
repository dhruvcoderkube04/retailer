<?php

namespace App\Services;

use App\Models\CourierPartner;
use App\Services\Courier\FShipService;
use App\Services\Courier\LorrigoService;  //for test
use App\Services\Courier\LorrigoServiceLive; // for live
use App\Services\Courier\CourierInterface;

class CourierServiceManager
{
    public static function getService(): CourierInterface
    {
        // Get the active courier partner from the database
        $partner = CourierPartner::where('is_active', true)->firstOrFail();
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
}
