<?php

namespace App\Console\Commands;

use App\Models\Run;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

require_once app_path('Support/simple_html_dom.php');

class SyncFreshoRuns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresho:sync-runs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Runs info from Fresho';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://app.fresho.com/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/deliveries/supplier_delivery_runs';

        Log::info("fresho sync runs");

        $rv = Http::get($url)->body();

        $html = str_get_html($rv);
        $runDivs = $html->find('a[data-fresho-group="drilldown-item-link"]');

        foreach ($runDivs as $runA) {
            $runCode = $runA->find('span[data-fresho-group="supplier-delivery-run-code"]',0)->innertext;
            $runName = $runA->find('span[data-fresho-group="supplier-delivery-run-name"]',0)->innertext;
            $runName = html_entity_decode($runName);
            $runId = substr($runA->getAttribute('href'), 90, -5);

            $this->info($runCode . '->' . $runName . '->' . $runId);

            $data = [
                'id' => $runId,
                'code' => $runCode,
                'name' => $runName
            ];

            Run::upsert($data, ['id'], ['code', 'name']);
        }

        Log::info("fresho sync runs finished");
    }
}
