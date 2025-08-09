<?php

use App\Jobs\DailWinnersChoseAndPay;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::job(new DailWinnersChoseAndPay)->dailyAt('00:00');
Schedule::call(function () {
    \Log::info('Scheduler is working at ' . now());
})->everyMinute();
