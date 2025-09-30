<?php

namespace App\Console\Commands;

use App\Models\FreshoProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncFreshoProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresho:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products info from Fresho';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://app.fresho.com/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/supplier_product_items';
        $params = [
            'order' => 'product+ASC',
            'q' => '',
        ];

        $search_after_key = 0;
        $total_page = 0;
        $loop = 0;

        while ($search_after_key <= $total_page && $loop < 100) {
            Log::info("sync fresho products in loop:" . $loop);
            $loop += 1;

            if ($search_after_key > 0) {
                $params['search_after_key'] = $search_after_key;
            }
            $rv = Http::get($url, $params)->json();

            $total_page = $rv['total_pages'];
            $search_after_key = $rv['search_after_key'];

            $data = [];
            foreach ($rv['data'] as $prd) {
                $idx = strrpos($prd['name'], ' ○ ');
                if ($idx !== false) {
                    $name = substr($prd['name'], 0, $idx);
                    $qty_type = substr($prd['name'], $idx + strlen(' ○ '));
                } else {
                    $name = $prd['name'];
                    $qty_type = '';
                }

                $data[] = [
                    'id' => $prd['id'],
                    'code' => $prd['code'],
                    'name' => $name,
                    'cost' => floatval($prd['cost_price']['price']) * 100,
                    'price_text' => $prd['edit_prices_link_text'],
                    'product_id' => $prd['product_id'],
                    'qty_type' => $qty_type,
                ];

                if (count($data) >= 10) {
                    FreshoProduct::upsert($data, ['id'], ['code', 'name', 'cost', 'price_text', 'product_id', 'qty_type']);

                    $data = [];
                }
            }
            FreshoProduct::upsert($data, ['id'], ['code', 'name', 'cost', 'price_text', 'product_id', 'qty_type']);
        }

        Log::info("sync fresho products finished");
    }
}
