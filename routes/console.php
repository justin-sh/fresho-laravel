<?php

use App\Console\Commands\SyncFreshoProductGroup;
use App\Console\Commands\SyncFreshoProducts;
use App\Jobs\SyncOrderDeliveryProof;
use App\Jobs\SyncOrderSummary;
use Illuminate\Console\Scheduling\Schedule as Weekdays;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schedule;

/**
 * Cron: min hour day month weekday
 * SyncOrderSummary             every minute        04:10 - 15:00 Mon/Fri 05:10 - 15:00 Tue-Thur
 * SyncOrderDeliveryProof       every 5 minute      09:00 - 19:00 Mon-Sat
 * SyncFreshoProducts           once per day        09:00 - 19:00 Mon-Sat
 * SyncFreshoProductGroup       once per day        09:00 - 19:00 Mon-Sat
 * SyncFreshoProductGroup -s    once per day        09:00 - 19:00 Mon-Sat
 */
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

$t = Carbon::now();
$his = $t->toTimeString();

Schedule::call(function () use ($t) {
    SyncOrderSummary::dispatchSync($t->toDateString());
})->name(SyncOrderSummary::class)
    ->everyMinute()
    ->skip(Weekdays::SUNDAY)
    ->skip(function () use ($t, $his) {
        if ($t->weekday() == Weekdays::MONDAY || $t->weekday() == Weekdays::FRIDAY) {
            if ($his < '04:10:00' || $his > '15:00:00') {
                return true;
            }
        } else {
            if ($his < '05:10:00' || $his > '15:00:00') {
                return true;
            }
        }

        return false;
    });

Schedule::call(function () {
    SyncOrderDeliveryProof::dispatchSync();
})->name(SyncOrderDeliveryProof::class)
    ->everyFiveMinutes()
    ->skip(Weekdays::SUNDAY)
    ->skip(function () use ($t, $his) {
        if ($t->weekday() == Weekdays::SATURDAY) {
            if ($his < '09:00:00' || $his > '17:00:00') {
                return true;
            }
        } else {
            if ($his < '09:00:00' || $his > '19:00:00') {
                return true;
            }
        }

        return false;
    });

Schedule::command(SyncFreshoProducts::class)
    ->hourlyAt(1)
    ->skip(Weekdays::SUNDAY)
    ->skip(function () use ($t, $his) {
        if ($t->weekday() == Weekdays::MONDAY || $t->weekday() == Weekdays::FRIDAY) {
            if ($his < '05:00:00' || $his > '15:00:00') {
                return true;
            }
        } else {
            if ($his < '05:10:00' || $his > '15:00:00') {
                return true;
            }
        }

        $filename = sys_get_temp_dir() . '/sync-fresho-products-' . $t->toDateString() . '.tmp';
        if (file_exists($filename)) {
            return true;
        }

        return false;
    })->onSuccess(function () use ($t) {
        $filename = sys_get_temp_dir() . '/sync-fresho-products-' . $t->toDateString() . '.tmp';
        touch($filename);
    });;


Schedule::command(SyncFreshoProductGroup::class)
    ->hourlyAt(3)
    ->skip(Weekdays::SUNDAY)
    ->skip(function () use ($t, $his) {
        if ($his < '05:00:00' || $his > '15:00:00') {
            return true;
        }

        $filename = sys_get_temp_dir() . '/sync-fresho-products-' . $t->toDateString() . '.tmp';
        if (!file_exists($filename)) {
            return true;
        }

        $filename = sys_get_temp_dir() . '/sync-fresho-products-group-' . $t->toDateString() . '.tmp';
        if (file_exists($filename)) {
            return true;
        }

        return false;
    })->onSuccess(function () use ($t) {
        $filename = sys_get_temp_dir() . '/sync-fresho-products-group-' . $t->toDateString() . '.tmp';
        touch($filename);
    });;

Schedule::command(SyncFreshoProductGroup::class, ['-s'])
    ->hourlyAt(5)
    ->skip(Weekdays::SUNDAY)
    ->skip(function () use ($t, $his) {
        if ($his < '05:00:00' || $his > '15:00:00') {
            return true;
        }

        $filename = sys_get_temp_dir() . '/sync-fresho-products-' . $t->toDateString() . '.tmp';
        if (!file_exists($filename)) {
            return true;
        }

        $filename = sys_get_temp_dir() . '/sync-fresho-products-group-s-' . $t->toDateString() . '.tmp';
        if (file_exists($filename)) {
            return true;
        }

        return false;
    })->onSuccess(function () use ($t) {
        $filename = sys_get_temp_dir() . '/sync-fresho-products-group-s-' . $t->toDateString() . '.tmp';
        touch($filename);
    });
