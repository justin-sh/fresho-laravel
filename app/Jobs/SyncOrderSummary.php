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

            Log::debug("sync order for date [{$this->deliveryDate}] page: " . $curPage);

            $params = [
                'page' => $curPage,
                'per_page' => 200,
                'q[order_state]' => 'all',
                'q[receiving_company_id]' => '',
                'q[delivery_run_code]' => '',
                'q[delivery_date]' => $this->deliveryDate,
                'sort' => '-delivery_date,-submitted_at,-order_number',
            ];

            $resp = Http::withOptions(['debug' => true])->get($url, $params)->json();

            $data = [];
            collect($resp['supplier_orders'])->each(function ($order) use (&$data) {
                $data[] = [
                    'id' => $order['id'],
                    'order_number' => $order['order_number'],
                    'delivery_date' => $order['delivery_date'],
                    'receiving_company_id' => $order['receiving_company_id'],
                    'receiving_company_name' => $order['receiving_company_name'],
                    'additional_notes' => $order['additional_notes'],
                    'contact_name' => $order['contact_name'],
                    'contact_phone' => $order['contact_phone'],
                    'delivery_address' => $order['delivery_address'],
                    'delivery_method' => $order['delivery_method'],
                    'delivery_venue' => $order['delivery_venue'],
                    'external_reference' => $order['external_reference'],
                    'delivery_instructions' => $order['delivery_instructions'],
                    'formatted_cached_payable_total' => $order['formatted_cached_payable_total'],
                    'payable_total_in_cents' => intval(Str::remove(['$', ',', '.'], $order['formatted_cached_payable_total']) ?? '0'),
                    'submitted_at' => Carbon::create($order['submitted_at'] ?? '2000'),
                    'state' => $order['state'],
                    'placed_by_name' => $order['placed_by_name'],
                    'parent_order_id' => $order['parent_order_id'],
                ];

            });

            Order::upsert($data, ['id'], ['delivery_date', 'additional_notes', 'delivery_instructions', 'formatted_cached_payable_total', 'payable_total_in_cents', 'submitted_at', 'state', 'placed_by_name']);

//            Log::debug('updated data:' . json_encode($data));

            if ($resp['meta']['total_pages'] >= $curPage) {
                break;
            } else {
                $curPage++;
            }
        }
    }
}
