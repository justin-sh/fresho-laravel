<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Run;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

class SyncOrderDetail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Collection $orderIds)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // step1 download file
        $filename = $this->downloadFile();
//        $filename = "tmp/order-detail-1726059049.6213.csv";
        // step2 parse csv file
        $runs = Run::all(['code', 'name'])->mapWithKeys(function ($run) {
            return [$run['name'] => $run['code']];
        });

        $absFilename = Storage::path($filename);

        $order = [];
        $orderDetail = [];
        if (($fp = fopen($absFilename, 'r')) !== false) {
            $row = 1;
            $header = true;
            while (($data = fgetcsv($fp)) !== false) {
                if ($header === true) {
                    $header = false;
                    continue;
                }
//                Log::debug("Row $row:" . $data[0]);
                if (!empty($data[8])) { // when there is run data
                    $order[] = [
                        'run' => $runs->get($data[8], '~NR'),
                        'order_no' => $data[12],
                    ];
                }

                if ("'STD_FREIGHT_BOX'" == $data[1]) {
                    continue;
                }
                $orderDetail[] = [
                    'group' => $data[0],
                    'prd_code' => Str::trim($data[1], "'"),
                    'prd_name' => $data[2],
                    'qty_type' => $data[3],
                    'qty' => floatval($data[4]),
                    'customer_notes' => $data[5],
                    'supplier_notes' => $data[6],
                    'status' => $data[7],
                    'order_number' => $data[12],
                ];
//                Log::debug();
//                Log::debug($data[1]);

                $row++;
            }

            fclose($fp);
        }

        DB::transaction(function () use ($order, $orderDetail) {
            collect($order)->each(function ($x) {
                Order::query()->where('order_number', $x['order_no'])
                    ->update(['delivery_run' => $x['run']]);
            });

            // did not handle the deleted product case
            OrderDetail::upsert($orderDetail, ['order_number', 'prd_code'], ['qty_type', 'qty', 'supplier_notes', 'customer_notes', 'status']);

        });

//        Log::debug(json_encode($order));
//        Log::debug(json_encode($orderDetail));
    }

    private function downloadFile(): string
    {
        // step 1: https://app.fresho.com/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb
        $rv1 = Http::get('https://app.fresho.com/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb');

        $csrfToken = $rv1->cookies()->getCookieByName('fresho-app-csrf-token')->getValue();

//        Log::debug("csrf token:{$csrfToken}");

        $params = [
            'type' => "product-totals-by-customer-batch",
            'args' => [
                'selected_order_ids' => $this->orderIds->all(),
                'pagination' => true,
                'supplied_statuses' => ['supplied'],
                "format" => "CSV"  # PDF CSV
            ]
        ];

        $rv2 = Http::withHeaders(['x-csrf-token' => $csrfToken])->post('https://app.fresho.com/api/v1/my/suppliers/reports', $params)->object();
        $jobId = $rv2->job_id;
        $loopCnt = 1;

        $fileUrl = '';
        Sleep::for(800)->milliseconds();
        while ($loopCnt < 10) {
            $loopCnt++;
            $rv3 = Http::get("https://app.fresho.com/api/v1/public/jobs/" . $jobId)->object();

            if ('complete' == $rv3->status) {
                $fileUrl = $rv3->result->result_data->report->temporary_url;
                break;
            }
        }

        $rv4 = Http::get($fileUrl);

        $filename = sprintf('tmp/order-detail-%s.csv', microtime(true));
        Storage::disk('local')->put($filename, $rv4->body());

//        Log::debug("save order detail file to $filename");
        return $filename;
    }
}
