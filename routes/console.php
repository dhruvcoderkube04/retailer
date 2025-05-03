<?php
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\TrackShipments;
use Illuminate\Support\Facades\Log;

Schedule::command(TrackShipments::class)
    ->everyMinute()
    ->onSuccess(fn () => Log::info('✅ TrackShipments success'))
    ->onFailure(fn () => Log::error('❌ TrackShipments failed'));
