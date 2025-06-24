<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Jobs\SyncOrderDeliveryProof;
use App\Jobs\SyncOrderDetail;
use App\Jobs\SyncOrderSummary;
use App\Models\Order;
use App\Support\MpdfZt411Label;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $delivery_date = $request->str('delivery_date', '')->value();
        $delivery_date2 = $request->str('delivery_date2', '')->value();
        $customer = $request->str('customer', '')->value();
        $product = $request->str('product', '')->value();
        $status = $request->input('status');
        $credit = $request->boolean('credit');

        if (empty($delivery_date . $delivery_date2 . $customer . $product)) {
            return new OrdersResource(collect());
        }

        $orders = Order::query()->with('details')
            ->when($delivery_date, function (Builder $query, string $delivery_date) {
                $query->where('delivery_date', '>=', $delivery_date);
            })
            ->when($delivery_date2, function (Builder $query, string $delivery_date) {
                $query->where('delivery_date', '<=', $delivery_date);
            })
            ->when($customer, function (Builder $query, string $customer) {
                $query->whereLike('receiving_company_name', '%' . $customer . '%');
            })
            ->when($product, function (Builder $query, string $product) {
                $query->whereHas('details', function (Builder $query) use ($product) {
                    $query->whereLike('prd_name', '%' . $product . '%');
                });
            })
            ->when($status, function (Builder $query, array $status) {
                $query->whereIn('state', $status);
            })
            ->orderByDesc('delivery_date')
            ->orderBy('receiving_company_name')
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

    public function printLabel(Request $request, Response $response): Response
    {
        $prds = $request->input('data');

//        $data = request()->all();

        $data = json_decode($prds, true);
//        foreach ($json['products'] as )

//        $data = [];
        Log::debug(json_encode($data));
        if (empty($data)) {
//            //for test
            $data = [
                ['cus' => "Nammi Vietnamese 宋烟如你注意到 83",
                    "prd" => "Pork Belly Boneless Rind On (Fem宋烟 如你注ale)",
                    "qty" => "31.94 <Kg>",
                    "pd" => "20/05/2025",
                    "bbd" => "27/05/2025",
                    "orderNo" => "F40431677",
                    "run" => "CT",
                ],
                ['cus' => "Nammi Vietnamese (Richmond Road) 83",
                    "prd" => "Pork Belly Boneless Rind On (Fem宋烟如你注意到体ale)",
                    "qty" => "31.94 <Kg>",
                    "pd" => "20/05/2025",
                    "bbd" => "27/05/2025",
                    "orderNo" => "F40431676",
                    "run" => "LE",
                ],
            ];
        }
//
        $label = new MpdfZt411Label();
        // $label = new Zt411Label();
        // $label = new TcpdfZt411Label();
        foreach ($data as $item) {
            $label->addNew($item['cus'], $item['prd'], $item['qty'], $item['pd'], $item['bbd'], $item['orderNo'], $item['run']);
        }
//        return $response->setContent() $label->print('', 'I');
        return $response->setContent($label->print(dest: 'S'))
            ->withHeaders([
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public',
            ]);

//        return $response->setContent("OK");
    }

    public function syncSummary(Request $request): string
    {
        $delivery_date = $request->str('delivery_date');
        Log::debug("sync order data for $delivery_date");

        SyncOrderSummary::dispatchSync($delivery_date);

        return json_encode(['ok' => true]);
    }

    public function syncDetail(Request $request): string
    {
        $delivery_date = $request->str('delivery_date');
        Log::debug("sync order detail data for $delivery_date");
        $ids = Order::query()->where('delivery_date', $delivery_date)->get('id')->pluck('id');
        SyncOrderDetail::dispatchSync($ids);

        return json_encode(['ok' => true]);
    }

    public function syncDeliveryProof(Request $request): string
    {
        SyncOrderDeliveryProof::dispatchSync();

        return json_encode(['ok' => true]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResource
    {
        return new OrderResource(Order::query()->findOrFail($order->id));
    }


    public function searchFreshoOrders(Request $request): JsonResource
    {
        $delivery_date = $request->str('delivery_date', '')->value();
        $customer = $request->str('customer', '')->value();
        $product = $request->str('product', '')->value();
        if (empty($customer) && empty($product)) { // init data
            SyncOrderSummary::dispatchSync($delivery_date);
        }
        return $this->index($request);
    }

    public function searchDetailByOrderNo(Request $request)
    {
        $order_no = $request->str('order_no');
        $src = $request->str('src', '');
        $isDetailPage = 'OrderDetailPage' == $src;
        Log::debug("sync order detail data for No:$order_no");

        $order = Order::query()
            ->with('details')
            ->where('order_number', $order_no)
            ->first();

        $quantity_types = [];
        $products = [];
        $prices = [];
        $product_items = [];
        if (count($order->details) == 0 || $isDetailPage) {
            //no detail and sync it from Fresho
            $url = 'https://app.fresho.com/api/v1/my/suppliers/supplier_orders/' . $order->id;

            $rv = Http::get($url)->json();
            Log::debug(json_encode($rv));
            // locked::: {"supplier_order":{"id":"0be5d6f4-451b-4e83-9c7e-9f0b05e8d63a","state":"invoiced","order_number":"40679598","prefixed_order_number":"F40679598","is_locked":true,"receiving_company_name":"Butcher on Deakin","payment_method_available":false}}
            $isLocked = $rv['supplier_order']['is_locked'];
            if ($isLocked) {
                $order->is_locked = true;
                throw new \Exception("Unhandling LOCKED order");
                //get detail from separate page
                // url https://app.fresho.com/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/customer_orders/df6126c9-5540-4f81-a441-1691080b4a50
            } else {
                $quantity_types = $rv['quantity_types'];
                $products = $rv['products'];
                $prices = $rv['prices'];
                $product_items = $rv['product_items'];

                $run = $rv['supplier_order']['delivery_run_code'];
                $picking_instructions = $rv['supplier_order']['picking_instructions'];
                $number_of_boxes = $rv['supplier_order']['number_of_boxes'] ?? 0;
                $state = $rv['supplier_order']['state'];
                $delivery_run_position = $rv['supplier_order']['delivery_run_position'];
                $freight_rule = $rv['supplier_order']['freight_rule'];
                $is_credit_note = $rv['supplier_order']['is_credit_note'];
                $order->state = $state;
                $order->number_of_boxes = $number_of_boxes;
                $order->picking_instructions = $picking_instructions;
                $order->delivery_run = $run;
                $order->delivery_run_position = $delivery_run_position;
                $order->is_credit_note = $is_credit_note;
                $order->freight_rule = $freight_rule;

                $details = $rv['product_orders'];
                $prd_orders = [];
                foreach ($details as $idx=>$d){
                    $prd_orders[] = [
                        'id'=>$d['id'],
                        'order_number'=>$order_no,
                        'prd_code'=>$d['product_code'],
                        'idx'=>$idx,
                        'product_id'=>$d['product_id'],
                        'prd_name'=>$d['product_name'],
                        'qty'=>$d['quantity'],
                        'quantity_type_id'=>$d['quantity_type_id'],
                        'qty_type'=>$d['quantity_type_name'],
                        'original_quantity'=>$d['original_quantity'],
                        'price_cents_per_quantity'=>$d['price_per_quantity'],
                        'cost_cents'=>$d['cost_cents'],
                        'group'=>$d['product_group'],
                        'status'=>$d['supplied_status'],
                        'customer_notes'=>$d['notes']??'',
                        'supplier_notes'=>$d['supplier_notes']??'',
                    ];
                }

                $order->details()->delete();
                $order->details()->createMany($prd_orders);
                $order->save();
            }

            $order->refresh();
        }

//        Log::debug(json_encode($order));

        return new OrderResource($order, $quantity_types, $products, $prices, $product_items);
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
