<?php

use App\Console\Commands\ProcessRetailerPendingImagesLink;
use App\Console\Commands\ProcessRetailerPendingVideosLink;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\TrackShipments;
use Illuminate\Support\Facades\Log;

Schedule::command(TrackShipments::class)
    ->everyMinute()
    ->onSuccess(fn() => Log::info('✅ TrackShipments success'))
    ->onFailure(fn() => Log::error('❌ TrackShipments failed'));

Schedule::command(ProcessRetailerPendingImagesLink::class)
    ->everyThirtyMinutes()
    ->onSuccess(fn() => Log::info('✅ ProcessRetailerPendingImagesLink success'))
    ->onFailure(fn() => Log::error('❌ ProcessRetailerPendingImagesLink failed'));

Schedule::command(ProcessRetailerPendingVideosLink::class)
    ->everyThirtyMinutes()
    ->onSuccess(fn() => Log::info('✅ ProcessRetailerPendingVideosLink success'))
    ->onFailure(fn() => Log::error('❌ ProcessRetailerPendingVideosLink failed'));
