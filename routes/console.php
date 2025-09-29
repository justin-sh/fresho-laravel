<?php

use App\Console\Commands\SyncFreshoProductGroup;
use App\Console\Commands\SyncFreshoProducts;
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

//Schedule::job(new SyncOrderSummary(Carbon::now('Australia/Adelaide')->toDateString()))
//    ->everyMinute()
//    ->skip(function () {
//        $t = Carbon::now('Australia/Adelaide');
////        Log::debug("current time: " . $t->weekday() . ' hour:' . $t->hour);
//
//        if ($t->weekday() == Weekdays::SUNDAY) {
//            return true;
//        }
//        if ($t->hour < 4 || $t->hour > 18) {
//            return true;
//        }
//        return false;
//    });

Schedule::command(SyncFreshoProducts::class)->hourly()->between('5:00', '15:00')->skip(function () {
    $t = Carbon::now('Australia/Adelaide');

    if ($t->weekday() == Weekdays::SUNDAY || $t->weekday() == Weekdays::SATURDAY) {
        return true;
    }

    $filename = sys_get_temp_dir() . '/sync-fresho-products-'.$t->toDateString().'.tmp';
    if(file_exists($filename)){
        return true;
    }

    touch($filename);
    return false;
});


Schedule::command(SyncFreshoProductGroup::class)->hourly()->between('5:00', '15:00')->skip(function () {
    $t = Carbon::now('Australia/Adelaide');

    if ($t->weekday() == Weekdays::SUNDAY || $t->weekday() == Weekdays::SATURDAY) {
        return true;
    }

    $filename = sys_get_temp_dir() . '/sync-fresho-products-'.$t->toDateString().'.tmp';
    if(!file_exists($filename)){
        return true;
    }

    $filename = sys_get_temp_dir() . '/sync-fresho-products-group-'.$t->toDateString().'.tmp';
    if(file_exists($filename)){
        return true;
    }

    touch($filename);
    return false;
});

Schedule::command(SyncFreshoProductGroup::class, ['-s'])->hourly()->between('5:00', '15:00')->skip(function () {
    $t = Carbon::now('Australia/Adelaide');

    if ($t->weekday() == Weekdays::SUNDAY || $t->weekday() == Weekdays::SATURDAY) {
        return true;
    }

    $filename = sys_get_temp_dir() . '/sync-fresho-products-'.$t->toDateString().'.tmp';
    if(!file_exists($filename)){
        return true;
    }

    $filename = sys_get_temp_dir() . '/sync-fresho-products-group-s-'.$t->toDateString().'.tmp';
    if(file_exists($filename)){
        return true;
    }

    touch($filename);
    return false;
});
