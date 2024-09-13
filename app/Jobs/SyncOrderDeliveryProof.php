<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

require_once app_path('Support/simple_html_dom.php');

class SyncOrderDeliveryProof implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //step 1: get delivery page url
//        Log::debug("SyncOrderDeliveryProof step 0");
        $url0 = 'https://app.fresho.com/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/deliveries';
        $rv = Http::get($url0)->body();

//        Log::debug('response = ' . $rv->status() . ' ' . $rv->reason());
//        Log::debug('response = ' . $rv->getBody());

        $html = str_get_html($rv);
        $pageUrl = $html->find('a[data-fresho-item="view-recent-pods"]', 0)?->href;

        if (empty($pageUrl)) {
            Log::warning('cannot get page url on step 1 while parsing the delivery proof');
            return;
        }

        //step 2: get delivery info
        $pageUrl = 'https://app.fresho.com' . $pageUrl;
        $rv2 = Http::get($pageUrl)->body();
        $html2 = str_get_html($rv2);
        $trs = $html2->find('tr.d-lg-table-row.mb-3');
//        Log::debug("tr.count=" . count($trs));

        $deliveredInfos = collect();
        collect($trs)->each(function ($tr) use ($deliveredInfos) {
            $tds = $tr->find('td');
            $a = $tds[0]->find('a', 0);

            $dAt = $tds[4]->find('span', 0)->getAttribute('data-timestamp');

            $info = [
                'delivery_proof_url' => $a->href,
                'delivery_proof' => trim($tds[2]->innertext),
                'delivery_by' => trim($tds[3]->innertext),
                'delivery_at' => Carbon::create($dAt),
            ];

            $deliveredInfos->put(substr($a->innertext, 1), $info);
        });

//        Log::debug(json_encode($deliveredInfos));

        DB::transaction(function () use ($deliveredInfos) {
            $deliveredInfos->each(function ($info, $orderNo) {
                Order::query()
                    ->where('order_number', $orderNo)
                    ->update($info);
            });
        });
    }
}
