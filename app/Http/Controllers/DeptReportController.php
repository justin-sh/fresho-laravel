<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

class DeptReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return "ok";
    }

    /**
     * Store a newly created resource in storage.
     * @throws ConnectionException
     * @throws GuzzleException
     */
    public function store(Request $request)
    {
        $rptParams = $request->json()->all();
        // Log::debug(json_encode($rptParams));

        $tokenUrl = 'https://app.fresho.com/ordering/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/product_groups/filtered_by_date_range?start_date=' . $rptParams['reportDate'] . '&end_date=' . $rptParams['reportDate'];
        // $rv = $client->get($tokenUrl);
        $rv = Http::get($tokenUrl);
        $csrfToken = $rv->cookies()->getCookieByName('fresho-app-csrf-token')->getValue();

//        Log::debug('csrfToken=' . $csrfToken);
        Log::debug('------------');

        $reportType = ['dept-report' => 'operational-product-totals-by-customer', 'picking-slip' => 'operational-consolidated-picking-slip', 'sticker' => 'operational-product-stickers'];

        $jobs = [];

        // Log::debug(Date::now());
        foreach ($rptParams['orderRuns'] as $run) {

            $rptUrl = 'https://app.fresho.com/ordering/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/operational_reports';

            $params = [
                ['name' => 'start_date', 'contents' => $rptParams['reportDate']],
                ['name' => 'end_date', 'contents' => $rptParams['reportDate']],
                ['name' => 'delivery_runs[]', 'contents' => $run],
                ['name' => 'report_type', 'contents' => $reportType[$rptParams['reportType']]],
                ['name' => 'one_product_group_per_page', 'contents' => '1'],
                ['name' => 'report_format', 'contents' => 'pdf'],
            ];

            collect($rptParams['orderStatus'])->each(function ($e) use (&$params) {
                $params[] = ['name' => 'order_states[]', 'contents' => $e];
            });

            collect($rptParams['prdGroups'])->each(function ($e) use (&$params) {
                $params[] = ['name' => 'product_groups[]', 'contents' => $e];
            });

            collect($rptParams['prdStatus'])->each(function ($e) use (&$params) {
                $params[] = ['name' => 'product_order_statuses[]', 'contents' => $e];
            });

            // Log::debug(json_encode($params));

            $rv = Http::asMultipart()->withHeaders(['x-csrf-token' => $csrfToken])->post($rptUrl, $params)->object();

            $jobId = $rv->job_id;

            $jobs[$run] = $jobId;

            Log::info('submit job for ' . $run . '->' . $rptParams['reportType']);
        }

        $result = [];
        $reportDir = 'dept-report-picking-slip/' . $rptParams['reportDate'] . '/';
        foreach ($jobs as $run => $jobId) {
            $filename = $reportDir . $run . '-' . $rptParams['reportType'] . '-' . Date::now()->rawFormat('his') . '.pdf';
            $fz = $this->downloadReportFile($filename, $jobId);
            $result[$filename] = ['status' => 'downloaded', 'size' => $fz];
        }

        // print pdf file
        $printCmd = env('PDF_PRINT_CMD', '');
        if (Str::length($printCmd) > 0) {

            if (env('PDF_PRINT_BATCH', false)) {
                Log::debug("print pdf file in batch...");
            } else {
                Log::info("print pdf file one by one...");

                foreach ($result as $filename => $v) {

                    $absPath = Storage::path($filename);
                    $fz = Storage::size($filename);

                    if ($fz < 10240) {
                        Log::info("Print file:" . $absPath . ' is too small(' . $fz . 'bytes) and maybe empty. NO PRINT.');
                    } else {
                        $cmd = Str::replace('%FILENAME%', $absPath, $printCmd);
//                        Log::debug($cmd);
                        $prv = Process::run($cmd);
                        Log::info("Print file:" . $absPath . ($prv->exitCode() ?? -1 ? ' fail' : ' success'));
//                        Log::info('    ExitCode:' . $prv->exitCode());
//                        Log::info('    Output  :' . $prv->output());
                    }
                }
            }
        }

        return ['ok' => true, 'data' => $result];
    }

    /**
     * @throws ConnectionException
     */
    private function downloadReportFile($filename, $jobId): int
    {
        Log::debug('save to file:' . $filename . ' JobId:' . $jobId);
        $url = 'https://app.fresho.com/api/v1/public/jobs/' . $jobId;

        $i = 1;
        $fileDownloadUrl = '';
        while ($i < 100) {
            // Log::debug("try to download file:" . $i);
            $rv = Http::get($url)->object();
            if ($rv->status == 'complete') {
                // Log::debug(json_encode($rv));
                $fileDownloadUrl = $rv->result->result_data->report->temporary_url;
                break;
            }
            $i += 1;
            Sleep::for(50)->milliseconds();
        }
        $rv = Http::get($fileDownloadUrl);

        $fz = $rv->getBody()->getSize();
        Log::debug('size of ' . $filename . ": " . $fz);
        Storage::disk('local')->put($filename, $rv->getBody()->getContents());
//        if ($rv->getBody()->getSize() < 10240) {
//            Log::info($filename . " size is too small, maybe empty.");
//        }

        return $fz;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
