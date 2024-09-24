<?php

namespace App\Listeners;

use App\Events\PurchaseOrderApproved;
use App\Models\Product;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

class ProductStockEventSubscriber
{

    public function handlePurchaseApproved(PurchaseOrderApproved $event): void
    {
        DB::transaction(function () use ($event) {
            $purchaseOrder = $event->purchaseOrder;

            $purchaseOrder->products();
            $wh_id = $purchaseOrder->wh_id;

            collect($purchaseOrder->products)->each(function (Product $prd) use ($wh_id) {

                $w = $prd->warehouses()->wherePivot('warehouse_id', $wh_id)->first();

                if (empty($w)) {
                    $prd->warehouses()->attach($wh_id, ['onhand_qty' => $prd->pivot->qty]);
                } else {
                    $total = $w->pivot->onhand_qty + $prd->pivot->qty;
                    $prd->warehouses()->syncWithoutDetaching([$wh_id => ['onhand_qty' => $total]]);
                }

            });
        });

    }

    public function subscribe(Dispatcher $events): void
    {
//        $events->listen(
//            PurchaseOrderApproved::class,
//            [ProductStockEventSubscriber::class, 'handlePurchaseApproved']);

//        return [
//            PurchaseOrderApproved::class => 'handlePurchaseApproved',
//        ];
    }
}
