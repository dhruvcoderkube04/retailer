<?php

namespace App\Services;

use App\Models\CourierPartner;
use App\Services\Courier\FShipService;
use App\Services\Courier\LorrigoService;
// use App\Services\Courier\YShipService;
// use App\Services\Courier\BluedartService;
// use App\Services\Courier\ProfessionalService;
// use App\Services\Courier\TirupatiService;
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
            'lorrigo'  => new LorrigoService($partner->toArray()),
            // 'yship'    => new YShipService($partner->toArray()),
            // 'bluedart' => new BluedartService($partner->toArray()),
            // 'professional' => new ProfessionalService($partner->toArray()),
            // 'tirupati' =>  new TirupatiService($partner->toArray()),
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
                'lorrigo'  => new LorrigoService($partner->toArray()),
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
}
