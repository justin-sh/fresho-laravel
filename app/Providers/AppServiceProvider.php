<?php

namespace App\Providers;

use App\Listeners\ProductStockEventSubscriber;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;

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

        Http::globalOptions(['timeout'=>60]);

        Http::globalRequestMiddleware(function (RequestInterface $request) {
//            Log::debug(gettype($request));
            return $request->withHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36')
                ->withHeader('Accept', 'application/json, text/javascript, */*; q=0.01')
                ->withHeader('cookie', config('app.fresho.cookie'));
        });

//        Log::debug(config('app.fresho.cookie'));

        DB::listen(function (QueryExecuted $query) {
//            Log::debug(Str::padRight('sql', 20) . $query->sql);
//            Log::debug(Str::padRight('bindings', 10) . json_encode($query->bindings));
//            Log::debug(Str::padRight('elapsed time', 20) . $query->time);
        });

        DB::whenQueryingForLongerThan(500, function (Connection $conn, QueryExecuted $q){
//            Log::warning("------- Long sql than 1000ms --------");
//            Log::warning(Str::padRight('conn name', 15) . ':' . $conn->getName());
//            Log::warning(Str::padRight('sql text', 15) . ':' . $q->sql);
//            Log::warning(Str::padRight('sql bindings', 15) . ':' . json_encode($q->bindings));
//            Log::warning(Str::padRight('sql time', 15) . ':' . $q->time . 'milliseconds');
//            Log::warning("------- Long sql than 1000ms --------");
        });
    }
}
