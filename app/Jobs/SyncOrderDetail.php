<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class SyncOrderDetail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $orderIds)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // step1 download file
        $orderDetails = $this->donwloadFile();
        // step2 parse csv file
    }

    private function donwloadFile(): string
    {
        // step 1: https://app.fresho.com/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb
        $rv1 = Http::get('https://app.fresho.com/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb');

        $csrfToken = $rv1->cookies()->getCookieByName('fresho-app-csrf-token')->getValue();

        Log::debug("csrf token:{$csrfToken}");

        $params = [
                    'type'=> "product-totals-by-customer-batch",
                    'args' => [
                        'selected_order_ids'=> $this->orderIds,
                        'pagination' => true,
                        'supplied_statuses' => ['supplied'],
                        "format" => "CSV"  # PDF CSV
                    ]
                ];

        $rv2 = Http::withHeaders(['x-csrf-token'=>$csrfToken])->post('https://app.fresho.com/api/v1/my/suppliers/reports', $params)->object();
        $jobId = $rv2->job_id;
        $loopCnt = 1;

        $fileUrl = '';
        Sleep::for(800)->milliseconds();
        while($loopCnt < 10){
            $loopCnt ++;
            $rv3 = Http::get("https://app.fresho.com/api/v1/public/jobs/". $jobId )->object();

            if('complete' == $rv3->status){
                $fileUrl = $rv3->result->result_data->report->temporary_url;
                break;
            }
        }

        $rv4 = Http::get($fileUrl);
        return $rv4->body();
    }
}
