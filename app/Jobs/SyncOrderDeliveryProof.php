<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        Log::debug("SyncOrderDeliveryProof step 0");
        $url0 = 'https://app.fresho.com/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/deliveries';
        $rv = Http::get($url0);

        Log::debug('response = ' . $rv->status() . ' '. $rv->reason());


        $pageUrl = '';
        $dom = new \DOMDocument();

        $dom->loadHTML($rv->body());
        $aList = $dom->getElementsByTagName('a');
        foreach ($aList as $item) {
            if ('view-recent-pods' === $item->getAttribute('data-fresho-item')) {
                $pageUrl = $item->getAttribute('href');
                break;
            }
        }
        if (empty($pageUrl)) {
            Log::warning('cannot get page url on step 1 while parsing the delivery proof');
            return;
        }

        $pageUrl = 'https://app.fresho.com' . $pageUrl;
        $rv2 = Http::get($pageUrl)->body();
        $dom->loadHTML($rv2);
        $divList = $dom->getElementsByTagName('div');
        $trList = null;
        foreach ($divList as $item) {
            if('test-recent-deliveries-table' === $item->getAttribute('class')){
                $trList = $item->firstChild->lastChild->childNodes;

                break;
            }
        }
        Log::debug("tr.count=" . count($trList));
    }
}
