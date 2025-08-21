<?php

namespace App\Http\Controllers;

use App\Http\Dto\OrderData;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Jobs\SyncOrderDeliveryProof;
use App\Jobs\SyncOrderDetail;
use App\Jobs\SyncOrderSummary;
use App\Models\Order;
use App\Support\MpdfZt411Label;
use App\Support\MpdfZt411LabelLarge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Output\Destination;

require_once app_path('Support/simple_html_dom.php');

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

    public function printLabelLarge(Request $request, Response $response)
    {
        $data = $request->json()->all();

//        $data = json_decode($prds, true);
//        foreach ($json['products'] as )

//        $data = [];
        Log::debug(json_encode($data));
        if (empty($data)) {
//            //for test
            $data = [
                ['cus' => "Nammi Vietnamese 宋烟如你注意到 83",
                    "prd" => "Pork Neck (Fem宋烟 如你注ale)",
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
        $label = new MpdfZt411LabelLarge();
        foreach ($data as $item) {
            $label->addNew($item['prd'], $item['qty'], $item['pd'], $item['bbd']);
        }

        $filename = sprintf('label-large-%s.pdf', date('YmdHis'));
        if (!is_dir(storage_path('app/tmp/label/'))) {
            mkdir(storage_path('app/tmp/label/'), 0777, true);
        }

        $label->print(name: storage_path('app/tmp/label/' . $filename), dest: Destination::FILE);
        return json_encode(['ok' => true, 'data' => $filename]);

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

    public function deleteAllDetailOn(Request $request): string
    {
        $delivery_date = $request->str('delivery_date');
        Log::debug("delete order detail data for $delivery_date");

        if (empty($delivery_date)) {
            Log::error("delete order detail data for empty delivery_date");
            return json_encode(['ok' => false, 'msg' => 'empty delivery_date']);
        }

        DB::delete('delete from order_details where order_details.order_number in ( select orders.order_number from orders where delivery_date = ? )', [$delivery_date]);

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


    public function searchFreshoOrders(Request $request)
    {
        $delivery_date = $request->str('delivery_date', '')->value();
        $customer = $request->str('customer', '')->value();
        $status = $request->str('status', 'all')->value();
        $run = $request->str('run', 'ALL')->value();
        if ('ALL' == $run) {
            $run = '';
        }
//        Log::debug($status);
//        $product = $request->str('product', '')->value();
//        if (empty($customer) && empty($product)) { // init data
//            SyncOrderSummary::dispatchSync($delivery_date);
//        }
        // get data from fresho
        $url = 'https://app.fresho.com/api/v1/my/suppliers/supplier_orders';
        $params = [
            'page' => 1,
            'per_page' => 200,
            'q[order_state]' => $status,
            'q[receiving_company_id]' => '',
            'q[delivery_run_code]' => $run,
            'q[delivery_date]' => $delivery_date,
            'sort' => '-delivery_date,-submitted_at,-order_number',
        ];
//        Log::debug($params);
        $_s = microtime(true);
        $resp = Http::get($url, $params)->json();
        Log::debug('elapse time:' . (microtime(true) - $_s));
        $resp_data = $resp['supplier_orders'];
        $resp_data2 = [];
        if ($resp['meta']['total_pages'] > 1) {
            $params['page'] = 2;
            $resp2 = Http::get($url, $params)->json();
            $resp_data2 = $resp2['supplier_orders'];
        }

        $data = [];
        collect($resp_data)->concat($resp_data2)->each(function ($order) use ($customer, &$data) {
//            Log::debug($order['receiving_company_name'] . '--->' . $customer);
            $orderData = [
                'additional_notes' => $order['additional_notes'],
                'payable_total_in_cents' => intval($order['cached_payable_total_in_cents'] ?? '0'),
                'contact_name' => $order['contact_name'],
                'contact_phone' => $order['contact_phone'],
                'delivery_address' => $order['delivery_address'],
                'delivery_date' => $order['delivery_date'],
                'delivery_instructions' => $order['delivery_instructions'],
                'delivery_method' => $order['delivery_method'],
                'delivery_venue' => $order['delivery_venue'],
                'external_reference' => $order['external_reference'],
                'formatted_cached_payable_total' => $order['formatted_cached_payable_total'],
                'id' => $order['id'],
                'is_credit_note' => $order['is_credit_note'],
                'is_locked' => $order['is_locked'],
                'number_of_boxes' => $order['number_of_boxes'] ?? 0,
                'order_number' => $order['order_number'],
                'parent_order_id' => $order['parent_order_id'],
                'placed_by_name' => $order['placed_by_name'],
                'receiving_company_id' => $order['receiving_company_id'],
                'receiving_company_name' => $order['receiving_company_name'],
                'state' => $order['state'],
                'submitted_at' => Carbon::create($order['submitted_at'] ?? '2000'),
            ];

            Order::upsert($orderData, ['id'], ['additional_notes', 'payable_total_in_cents', 'delivery_date', 'delivery_instructions', 'formatted_cached_payable_total', 'external_reference', 'is_locked', 'number_of_boxes', 'placed_by_name', 'submitted_at', 'state']);

            if (empty($customer) || Str::contains($order['receiving_company_name'], $customer, true)) {
                $data[] = [
                    'id' => $order['id'],
                    'orderNo' => $order['order_number'],
                    'deliveryDate' => $order['delivery_date'],
                    'customer' => $order['receiving_company_name'],
                    'state' => $order['state'],
                    'isLocked' => $order['is_locked'],
                ];
            }
        });
        return json_encode($data);
    }

    public function searchDetailByOrderNo(Request $request): JsonResource
    {
        $order_id = $request->str('id');
        $src = $request->str('src', '');
        $isDetailPage = 'OrderDetailPage' == $src || 'OrderPage' == $src;
        Log::debug("sync order detail data for id:$order_id");

        $order = Order::query()
            ->with('details')
            ->where('id', $order_id)
            ->firstOrNew([
                'id' => $order_id
            ]);

        if ($order->is_locked) {
            if (count($order->details) == 0) {
                $url = "https://app.fresho.com/companies/" . config('app.fresho.company_id') . "/selling/customer_orders/" . $order_id;
                $rv = Http::get($url)->body();
                $html = str_get_html($rv);
                $table = $html->find('div[data-fresho-item="order-details"] table', 0);

                $prd_orders = [];
                $idx = 0;
                foreach ($table->find('tbody>tr') as $tr) {
                    list($qty, $qtyUnit) = explode(' ', trim($tr->find('td[data-title="Quantity"]', 0)->plaintext));
                    $prd_orders[] = [
                        'order_number' => $order->order_number,
                        'idx' => $idx,
                        'customer_notes' => trim($tr->find('span[data-fresho-group="product-order-customer-note"]', 0)->plaintext),
                        'original_quantity' => $qty,
                        'price_cents_per_quantity' => trim($tr->find('td[data-title="Price per quantity"]', 0)->plaintext),
                        'prd_code' => '',
                        'group' => '', // todo
                        'prd_name' => trim($tr->find('span[data-fresho-group="product-name"]', 0)->plaintext),
                        'qty' => $qty,
                        'qty_type' => $qtyUnit,
                        'status' => trim($tr->find('td[data-title="Status"]', 0)->plaintext),
                        'supplier_notes' => trim($tr->find('span[data-fresho-group="product-order-supplier-note"]', 0)->plaintext),
                        'unit_of_order' => '',
                    ];

                    $idx = $idx + 1;
                }

                $order->details()->createMany($prd_orders);

                $order->refresh();
            }
            return new OrderResource($order);
        }

        $quantity_types = [];
        $products = [];
        $prices = [];
        $product_items = [];
        $csrfToken = '';
//        Log::debug($order);
        if (count($order->details) == 0 || $isDetailPage) {
            //no detail and sync it from Fresho
            $url = 'https://app.fresho.com/api/v1/my/suppliers/supplier_orders/' . $order->id;

            $resp = Http::get($url);
            $csrfToken = $resp->cookies()->getCookieByName('fresho-app-csrf-token')->getValue();
            $rv = $resp->json();

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

//            $order->order_number = $rv['supplier_order']['order_number'];
            $order->delivery_date = $rv['supplier_order']['delivery_date'];
//            $order->receiving_company_id = $rv['supplier_order']['receiving_company_id'];
//            $order->receiving_company_name = $rv['supplier_order']['receiving_company_name'];
            $order->additional_notes = $rv['supplier_order']['additional_notes'];
            $order->contact_name = $rv['supplier_order']['contact_name'];
            $order->contact_phone = $rv['supplier_order']['contact_phone'];
            $order->delivery_address = $rv['supplier_order']['delivery_address'];
//            if ('PICKUP FROM: 20 Tolley Street, Wingfield SA 5013, Australia' == $order->delivery_address) {
//                $order->delivery_method = 'Pickup'; // NO this value in this api
//            } else {
//                $order->delivery_method = 'Delivery'; // NO this value in this api
//            }
            $order->delivery_venue = $rv['supplier_order']['delivery_venue'];
            $order->external_reference = $rv['supplier_order']['external_reference'];
            $order->delivery_instructions = $rv['supplier_order']['delivery_instructions'];
            $order->picking_instructions = $picking_instructions;
            $order->number_of_boxes = $number_of_boxes;
            $order->payable_total_in_cents = $rv['supplier_order']['cached_payable_total_in_cents'];
            $order->formatted_cached_payable_total = '$' . number_format($order->payable_total_in_cents / 100, 2);
            $order->submitted_at = 'in_progress' == $state ? null : Carbon::create($rv['supplier_order']['submitted_at']);
            $order->state = $state;
            $order->is_locked = $rv['supplier_order']['is_locked'];
            // todo no available in this api
//                $order->placed_by_name = $rv['supplier_order']['placed_by_name'];
            $order->delivery_run = $run;
            $order->delivery_run_position = $delivery_run_position;
            $order->parent_order_id = $rv['supplier_order']['parent_order_id'];
            $order->is_credit_note = $is_credit_note;
            $order->freight_rule = $freight_rule;


            $details = collect($rv['product_orders'])
                ->sortBy('product_group')
                ->sortBy('product_code')
                ->all();
            $prd_orders = [];
            $idx = 0;

            $detailMap = [];
            foreach ($order->details() as $detail) {
                $detailMap[$detail->id] = $detail;
            }

            foreach ($details as $d) {
                $qtyDetail = $d['quantity'];
                if (array_key_exists($d['id'], $detailMap)) {
                    $detail = $detailMap[$d['id']];
                    if ($detail->qty == $d['quantity']) {
                        $qtyDetail = $detail->qty_detail;
                    }
                }
                $prd_orders[] = [
                    'best_before_date' => $d['best_before_date'],
                    'currency_symbol' => $d['currency_symbol'], // $
                    'customer_order_type' => $d['customer_order_type'], //SupplierOrder
                    'cost_cents' => $d['cost_cents'],
                    'id' => $d['id'],
                    'order_number' => $order->order_number,
                    'idx' => $idx,
                    'customer_notes' => $d['notes'] ?? '',
                    'original_quantity' => $d['original_quantity'],
                    'packed_on_date' => $d['packed_on_date'],
                    'price_cents_per_quantity' => $d['price_per_quantity'],
                    'prd_code' => $d['product_code'],
                    'group' => $d['product_group'],
                    'product_id' => $d['product_id'],
                    'prd_name' => $d['product_name'],
                    'qty' => $d['quantity'],
                    'qty_detail' => $qtyDetail,
                    'quantity_type_id' => $d['quantity_type_id'],
                    'qty_type' => $d['quantity_type_name'],
                    'status' => $d['supplied_status'],
                    'supplier_notes' => $d['supplier_notes'] ?? '',
                    'tax_applicable' => $d['tax_applicable'] ?? false,
                    'unit_of_order' => $d['unit_of_order'] ?? '',
                    'use_by_date' => $d['use_by_date'],
                ];
                $idx = $idx + 1;
            }
            $order->details()->delete();
            $order->details()->createMany($prd_orders);
            $order->save();

            $order->refresh();
        }

        return new OrderResource($order, $quantity_types, $products, $prices, $product_items, $csrfToken);
    }

    public function searchProductsByKey(Request $request)
    {
        $prdKey = $request->str('s', '');
        $orderId = $request->str('order_id', '');
//        $sellingCompanyId = $request->str('selling_company_id','');
        Log::debug("product key=$prdKey, orderId=$orderId");

        $url = 'https://app.fresho.com/api/v1/my/customers/search_products';
        $params = [
            'q[term]' => $prdKey,
            'order_id' => $orderId,
            'selling_company_id' => 'b181ee08-2214-46ec-ad1e-926a2bbfb8fb',
        ];

        $rv = Http::withHeader('fresho-mode', 'sell')->get($url, $params)->json();

        return json_encode($rv);
    }

    public function getProductInfoById(Request $request)
    {
        $prdId = $request->str('pid', '');
        $orderId = $request->str('order_id', '');
//        $sellingCompanyId = $request->str('selling_company_id','');
        Log::debug("product id=$prdId, orderId=$orderId");

        $url = 'https://app.fresho.com/api/v1/my/customers/product_items';
        $params = [
            'customer_order_id' => $orderId,
            'product_id' => $prdId,
            // 'selling_company_id' => 'b181ee08-2214-46ec-ad1e-926a2bbfb8fb',
        ];

        $rv = Http::withHeader('fresho-mode', 'sell')->get($url, $params)->json();

        return json_encode($rv);
    }

    public function updateFreshoOrder(Request $request, string $order_id)
    {

//        Log::debug("update fresho order: {$order_id}");

//        Log::debug(json_decode('["a":"","b":null,"c":1]'));
        $data = $request->json()->all();
//        Log::debug(json_encode($data));
//        Log::debug($data['deliveryDate']);
//        Log::debug($data['numberOfBoxes']);
//        Log::debug($data['additionalNotes']);
//        Log::debug($data['details']);
//        Log::debug($data['csrf_cookie']);

        $order = Order::query()
            // ->with('details')
            ->where('id', $order_id)
            ->first();

        $order->delivery_date = $data['deliveryDate'];
        $order->number_of_boxes = intval($data['numberOfBoxes'] ?? '0');
        $order->additional_notes = $data['additionalNotes'];
        $order->save();

        foreach ($data['details'] as $detail) {
//            Log::debug($detail['id'] . '->' . $detail['_destroy'] . ' --> qty-detail:'. $detail['qty_detail']);
            $d = $order->details()->where('id', $detail['id'])->first();
            if ($d) {
                if ($detail['_destroy']) {
                    // delete this one
                    $d->delete();
                } else {
                    // update this one
                    $d->update([
                        'qty' => $detail['qty'],
                        'qty_detail' => $detail['qty_detail'],
                        'quantity_type_id' => $detail['qtyTypeId'],
                        'qty_type' => $detail['qtyType'],
                        'price_cents_per_quantity' => $detail['price'] * 100,
                        'status' => $detail['status'],
                        'supplier_notes' => $detail['supplier_notes'] ?? '',
                        'best_before_date' => $detail['best_before_date'],
                        'packed_on_date' => $detail['packed_on_date'],
                        'use_by_date' => $detail['use_by_date'],
                    ]);
                }
            } else {
                // create a new one
                $order->details()->create([
                    'id' => $detail['id'],
                    'order_number' => $order->order_number,
                    'prd_code' => $detail['code'],
                    'product_id' => $detail['product_id'],
                    'prd_name' => $detail['name'],
                    'qty' => $detail['qty'],
                    'qty_detail' => $detail['qty_detail'],
                    'original_quantity' => $detail['qty'],
                    'quantity_type_id' => $detail['qtyTypeId'],
                    'qty_type' => $detail['qtyType'],
                    'price_cents_per_quantity' => $detail['price'] * 100,
                    'cost_cents' => $detail['cost_cents'],
                    'group' => $detail['group'],
                    'status' => $detail['status'],
                    'customer_notes' => $detail['customer_notes'] ?? '',
                    'supplier_notes' => $detail['supplier_notes'] ?? '',
                    'best_before_date' => $detail['best_before_date'],
                    'packed_on_date' => $detail['packed_on_date'],
                    'use_by_date' => $detail['use_by_date'],
                    'currency_symbol' => $detail['currency_symbol'],
//                    'customer_order_type'=>$detail['customer_order_type'],
                    'unit_of_order' => $detail['unit_of_order'] ?? '',
                    'tax_applicable' => $detail['tax_applicable'],
                ]);
            }
        }

        $freshoOrder = new OrderData($order, $data['details']);

        $url = 'https://app.fresho.com/api/v1/my/suppliers/supplier_orders/' . $order_id;

//        $rv = [];
        $rv = Http::withHeaders(['content-type' => 'application/json; charset=UTF-8', 'x-csrf-token' => $data['csrf_cookie']])
            ->put($url, ['supplier_order' => $freshoOrder])->json();

//        Log::debug($rv);

        return json_encode(['ok' => true, 'data' => $rv]);
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
