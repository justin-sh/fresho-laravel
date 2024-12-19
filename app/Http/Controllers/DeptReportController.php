<?php

namespace App\Http\Controllers;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Illuminate\Support\Facades\Storage;

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
        Log::debug(json_encode($rptParams));

        $tokenUrl = 'https://app.fresho.com/ordering/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/product_groups/filtered_by_date_range?start_date='.$rptParams['reportDate'].'&end_date='.$rptParams['reportDate'];
        // $rv = $client->get($tokenUrl);
        $rv = Http::get($tokenUrl);
        $csrfToken = $rv->cookies()->getCookieByName('fresho-app-csrf-token')->getValue();
        
        Log::debug( 'csrfToken=' . $csrfToken);
        Log::debug('------------');


        $reportType = ['PRD_TOTAL_CUS'=>'operational-product-totals-by-customer','picking-slip'=>'operational-consolidated-picking-slip', 'STICKER'=>'operational-product-stickers'];

        $result = [];

        $jobs = [];

        Log::debug(Date::now());
        foreach(['EE'] as $run){

            $rptUrl = 'https://app.fresho.com/ordering/api/v1/companies/9d10a274-72c3-43a6-92b3-87cde4703ea4/selling/operational_reports';


            $params = [
                ['name' => 'start_date', 'contents' => $rptParams['reportDate']],
                ['name' => 'end_date', 'contents' => $rptParams['reportDate']],
                ['name' => 'delivery_runs[]', 'contents' => $run],
                ['name' => 'order_states[]', 'contents' => 'accepted'],
                ['name' => 'report_type', 'contents' => $reportType[$rptParams['reportType']]],
                ['name' => 'one_product_group_per_page', 'contents' => '1'],
                ['name' => 'report_format', 'contents' => 'pdf'],
            ];

            collect($rptParams['prdGroups'])->each(function($e) use(&$params){
                $params[] = ['name' => 'product_groups[]', 'contents' => $e];
            });

            collect($rptParams['prdGroups'])->each(function($e) use(&$params){
                $params[] = ['name' => 'product_groups[]', 'contents' => $e];
            });

            collect($rptParams['prdStatus'])->each(function($e) use(&$params){
                $params[] = ['name' => 'product_order_statuses[]', 'contents' => $e];
            });
        
            Log::debug(json_encode($params));

            $rv = Http::asMultipart()->withHeaders(['x-csrf-token' => $csrfToken])->post($rptUrl, $params)->object();
            
            $jobId = $rv->job_id;

            $jobs[$run] = $jobId;

            Log::info('submit job for '. $run . '->' . $rptParams['reportType']);
        }

        foreach($jobs as $run => $jobId)
        {
            $this->checkJob($jobId);
        }

        return ['ok' => true, 'data' => ''];
    }

    private function checkJob($jobId)
    {
        $url = 'https://app.fresho.com/api/v1/public/jobs/' . $jobId;
        
        // Sleep::for(800)->milliseconds();
        $rv = Http::get($url)->object();

        Storage::disk('local')->put($filename, $rv4->body());
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
