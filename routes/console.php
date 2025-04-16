<?php


use Illuminate\Foundation\Configuration\Schedule;
use App\Console\Commands\MarkInactiveOrders;

return function (Schedule $schedule) {
    $schedule->command(MarkInactiveOrders::class)->everyMinute(); //
    // $schedule->command(MarkInactiveOrders::class)->hourly();
};

