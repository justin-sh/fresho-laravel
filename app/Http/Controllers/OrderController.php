<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Models\Order;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;
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

    public function init(Request $request): string
    {
        $delivery_date = $request->str('delivery_date');
        Log::debug("init order data for $delivery_date");

        $headers = [
            "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "Connection" => "keep-alive",
        ];

        $url = 'https://app.fresho.com/api/v1/my/suppliers/supplier_orders';
        $params = [
            'page' => 1,
            'per_page' => 200,
            'q[order_state]' => 'all',
            'q[receiving_company_id]' => '',
            'q[delivery_run_code]' => '',
            'q[delivery_date]' => $delivery_date,
            'sort' => '-delivery_date,-submitted_at,-order_number',
        ];

        $resp = Http::withOptions(['debug' => true])->get($url, $params)->json();
        Log::debug('data:' . json_encode($resp));
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
