<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Jobs\SyncOrderDeliveryProof;
use App\Jobs\SyncOrderDetail;
use App\Jobs\SyncOrderSummary;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $delivery_date = $request->str('delivery_date');
        $customer = $request->str('customer');
        $product = $request->str('product');
        $status = $request->input('status');
        $credit = $request->boolean('credit');
        $orders = Order::query()
            ->when($delivery_date, function (Builder $query, string $delivery_date) {
                if ($delivery_date)
                    $query->where('delivery_date', $delivery_date);
            })
            ->when($customer, function (Builder $query, string $customer) {
                if ($customer) {
                    $query->whereLike('receiving_company_name', $customer);
                }
            })
            ->when($product, function (Builder $query, string $product) {
//                $query->where('delivery_date', $product);
            })
            ->when($status, function (Builder $query, array $status) {
                $query->whereIn('state', $status);
            })
            ->limit(300)
            ->get();

        return new OrdersResource($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function syncSummary(Request $request): string
    {
        $delivery_date = $request->str('delivery_date');
        Log::debug("sync order data for $delivery_date");

        SyncOrderSummary::dispatchAfterResponse($delivery_date);

        return json_encode(['ok' => true]);
    }

    public function syncDetail(Request $request): string
    {
        $delivery_date = $request->str('delivery_date');
        Log::debug("sync order detail data for $delivery_date");
        $ids = Order::query()->where('delivery_date', $delivery_date)->get('id')->pluck('id');
        SyncOrderDetail::dispatchAfterResponse($ids);

        return json_encode(['ok' => true]);
    }

    public function syncDeliveryProof(Request $request): string
    {
        SyncOrderDeliveryProof::dispatchAfterResponse();

        return json_encode(['ok' => true]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResource
    {
        return new OrderResource(Order::query()->findOrFail($order->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
