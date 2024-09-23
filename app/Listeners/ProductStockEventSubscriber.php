<?php

namespace App\Listeners;

use App\Events\PurchaseOrderApproved;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class ProductStockEventSubscriber
{

    public function handlePurchaseApproved(): void
    {
        Log::debug('processing product stock adjustment');
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            PurchaseOrderApproved::class,
            [ProductStockEventSubscriber::class, 'handlePurchaseApproved']);

//        return [
//            PurchaseOrderApproved::class => 'handlePurchaseApproved',
//        ];
    }
}
