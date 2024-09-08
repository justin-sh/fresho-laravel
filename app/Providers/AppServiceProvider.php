<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());

        DB::listen(function (QueryExecuted $query) {
            Log::debug(Str::padRight('sql', 10) . $query->sql);
            Log::debug(Str::padRight('bindings', 10) . json_encode($query->bindings));
            Log::debug(Str::padRight('time', 10) . $query->time);
        });
    }
}
