<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MarkOldOrdersInactive;
use App\Console\Commands\TrackShipments;
use Illuminate\Support\Facades\Log;

return function (Schedule $schedule) {
    // Log scheduler initialization
    Log::info('Current timezone: ' . config('app.timezone'));

    // Schedule the TrackShipments command
    $schedule->command(TrackShipments::class)
             ->everyFiveMinutes() // Temporarily set to every 5 seconds for testing
             ->onSuccess(function () {
                 Log::info('✅ TrackShipments command executed successfully at ' . now()->toDateTimeString());
             })
             ->onFailure(function () {
                 Log::error('❌ TrackShipments command failed at ' . now()->toDateTimeString());
             });

    // Schedule the MarkOldOrdersInactive command
    // $schedule->command(MarkOldOrdersInactive::class)
    //          ->everyFiveSeconds() // Temporarily set to every 5 seconds for testing
    //          ->onSuccess(function () {
    //              Log::info('✅ MarkOldOrdersInactive command executed successfully at ' . now()->toDateTimeString());
    //          })
    //          ->onFailure(function () {
    //              Log::error('❌ MarkOldOrdersInactive command failed at ' . now()->toDateTimeString());
    //          });

    // Log scheduled tasks for debugging
    Log::info('Scheduled tasks registered: TrackShipments and MarkOldOrdersInactive');
};
