<?php

use App\Jobs\SyncOrderSummary;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Console\Scheduling\Schedule as Weekdays;

//Artisan::command('inspire', function () {
//    Log::debug("current time: " . Carbon::now('Australia/Adelaide'));
//    Log::debug("current time: " . Carbon::now());
//    Log::debug("current time: " . date('Y-m-d H:i:s'));
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->everyTenSeconds()->timezone('Australia/Adelaide');

Schedule::job(new SyncOrderSummary(Carbon::now('Australia/Adelaide')->toDateString()))
    ->everyMinute()
    ->skip(function () {
        $t = Carbon::now('Australia/Adelaide');
//        Log::debug("current time: " . $t->weekday() . ' hour:' . $t->hour);

        if ($t->weekday() == Weekdays::SUNDAY) {
            return true;
        }
        if ($t->hour < 4 || $t->hour > 18) {
            return true;
        }
        return false;
    });

//Schedule::call(function (){
//    Artisan::call('stocktake-daily');
//})->everyMinute();
