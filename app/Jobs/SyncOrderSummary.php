<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncOrderSummary implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $deliveryDate)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->deliveryDate)) return;

        $url = 'https://app.fresho.com/api/v1/my/suppliers/supplier_orders';

        $curPage = 1;
        while ($curPage < 100) {

            Log::info("sync order for date [{$this->deliveryDate}] page: " . $curPage);

            $params = [
                'page' => $curPage,
                'per_page' => 200,
                'q[order_state]' => 'all',
                'q[receiving_company_id]' => '',
                'q[delivery_run_code]' => '',
                'q[delivery_date]' => $this->deliveryDate,
                'sort' => '-delivery_date,-submitted_at,-order_number',
            ];

//            Log::debug(json_encode($params));
            $resp = Http::get($url, $params)->json();
//            Log::debug(json_encode($resp));


            $data = [];
            collect($resp['supplier_orders'])->each(function ($order) use (&$data) {

//                Log::debug("delivery_instructions=". $order['delivery_instructions']);

                $data[] = [
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


                if(count($data) >= 100){
                    Order::upsert($data, ['id'], ['additional_notes', 'payable_total_in_cents', 'delivery_date', 'delivery_instructions', 'formatted_cached_payable_total', 'external_reference', 'is_locked', 'number_of_boxes', 'placed_by_name', 'submitted_at', 'state']);

                    $data = [];
                }
//                Log::debug("length of data: " . count($data));

            });

            Order::upsert($data, ['id'], ['additional_notes', 'payable_total_in_cents', 'delivery_date', 'delivery_instructions', 'formatted_cached_payable_total', 'external_reference', 'is_locked', 'number_of_boxes', 'placed_by_name', 'submitted_at', 'state']);

//            Log::debug('updated data:' . json_encode($data));

            if ($resp['meta']['total_pages'] <= $curPage) {
                break;
            } else {
                $curPage++;
            }
        }
    }
}
