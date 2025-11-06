<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule: Cancel inactive virtual addresses daily at 2:00 AM
Schedule::command('virtualaddress:cancel-inactive')->dailyAt('02:00');
