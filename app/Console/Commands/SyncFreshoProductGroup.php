<?php

namespace App\Console\Commands;

use App\Models\FreshoProduct;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

require_once app_path('Support/simple_html_dom.php');

class SyncFreshoProductGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresho:sync-product-group {--s|single}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync fresho products group.
                                -s: single sync products whose market category is null';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $single = $this->option('single');
//        Log::debug("single:" . ($single ? 'yes' : 'no'));

        if ($single) {
            $this->syncMktCategoryBySingleProductDetail();
        } else {
            $this->syncMktCategoryByGroup();
        }

    }

    private function syncMktCategoryByGroup(): void
    {
        $url = 'https://app.fresho.com/api/v1/my/customers/product_items';
        $params = [
            'modelPath' => 'controller.product-items',
            'page' => 1,
            'per_page' => 30,
            'q[term]' => '',
            'q[marketplace_category]' => '',
            'receiving_company_id' => '9d2bfd7f-c1e2-4f59-b829-d7f032e0b3d3', //retail
            'selling_company_id' => 'b181ee08-2214-46ec-ad1e-926a2bbfb8fb',
            'supplier_id' => '34b3d836-d88d-43b0-87d2-de05bbfc83eb',
        ];
        $rv = Http::get($url, $params)->json();
        $prdMktGroups = $rv['supplier']['marketplace_categories'];

        foreach ($prdMktGroups as $g) {
            Log::info("sync product category for $g");
            // get products by marketplace categories
            $params['page'] = 1;
            $params['q[marketplace_category]'] = $g;

            do {
                $mktRv = Http::get($url, $params)->json();

                $totalPage = $mktRv['meta']['total_pages'];
                $products = $mktRv['products'];

                collect($products)->chunk(30)->each(function ($prds) use ($g) {

                    $bindings = [];

                    foreach ($prds as $prd) {
                        $bindings[] = $prd['id'];
                    }
                    Log::debug(json_encode($bindings));

                    DB::table('fresho_products')
                        ->whereIn('product_id', $bindings)
                        ->update(['mkt_cat' => $g]);
                });

                $params['page'] = $params['page'] + 1;

            } while ($params['page'] <= $totalPage);

//            break;
            Sleep::for(CarbonInterval::seconds());
        }
    }

    private function syncMktCategoryBySingleProductDetail()
    {
        $productsWithoutMktCat = FreshoProduct::query()
            ->whereNull('mkt_cat')
            ->orWhere('mkt_cat', '')
            ->get('id');

        $productsWithoutMktCat->each(function ($prd) {
            $prdDetailUrl = 'https://app.fresho.com/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/supplier_product_items/%s/edit';

            $url = sprintf($prdDetailUrl, $prd->id);

            $rv2 = Http::get($url)->body();
            $html2 = str_get_html($rv2);
            $mktCat = $html2->find('input#supplier_product_item_marketplace_category', 0);
            $mktCatValue = trim($mktCat->value);
            if (empty($mktCatValue)) {
                $mktCatValue = 'NA';
            }
            Log::info($prd->id . '->' . $mktCatValue);

            DB::table('fresho_products')
                ->where('id', $prd->id)
                ->update(['mkt_cat' => $mktCatValue]);

            Sleep::for(CarbonInterval::seconds());
        });
    }
}
