<?php

namespace App\Http\Controllers;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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


        $client = Http::buildClient();
        $cookieJar = new CookieJar();

        $tokenUrl = 'https://app.fresho.com/ordering/api/v1/companies/b181ee08-2214-46ec-ad1e-926a2bbfb8fb/selling/product_groups/filtered_by_date_range?start_date='.$rptParams['reportDate'].'&end_date='.$rptParams['reportDate'];
        $rv = Http::setClient($client)->withOptions(['cookies' => $cookieJar])->get($tokenUrl);
        Log::debug(json_encode( $rv->headers() ));
        Log::debug('------------');
        Log::debug(json_encode( $rv->header('Set-Cookie') ));
        Log::debug(json_encode( $rv->header('Set-Cookie') ));
        Log::debug(json_encode( $rv->header('Set-Cookie') ));
        Log::debug('------cookies------');
        Log::debug(json_encode( $rv->cookies() ));
//        Log::debug($rv->cookies()->getCookieByName("fresho-app-csrf-token"));
        Log::debug($rv->body());

//        $rptUrl = 'https://app.fresho.com/ordering/api/v1/companies/9d10a274-72c3-43a6-92b3-87cde4703ea4/selling/operational_reports';
//        $params = [
//            ['name' => 'start_date', 'contents' => $rptParams['reportDate']],
//            ['name' => 'end_date', 'contents' => $rptParams['reportDate']],
//            ['name' => 'delivery_runs[]', 'contents' => 'EE'],
//            ['name' => 'order_states[]', 'contents' => 'accepted'],
//            ['name' => 'product_groups[]', 'contents' => 'bandsaw'],
//            ['name' => 'product_order_statuses[]', 'contents' => 'topicked'],
//            ['name' => 'report_type', 'contents' => $rptParams['reportType']],
//            ['name' => 'one_product_group_per_page', 'contents' => '1'],
//            ['name' => 'report_format', 'contents' => 'pdf'],
////            'start_date' => $rptParams['reportDate'],
////            'end_date' => $rptParams['reportDate'],
////            'delivery_runs[]' => 'EE', // $rptParams['orderRuns'],
////            'order_states[]' => 'accepted',
////            'product_groups[]' => 'bandsaw',
////            'product_order_statuses[]' => 'topicked',
//////            'delivery_runs[]' => $rptParams['orderRuns'],
//////            'order_states[]' => $rptParams['orderStatus'],
//////            'product_groups[]' => $rptParams['prdGroups'],
//////            'product_order_statuses[]' => $rptParams['prdStatus'],
////            'report_type' => $rptParams['reportType'],
////            'one_product_group_per_page' => '1',
////            'report_format' => 'pdf',
//        ];
//
//        Log::debug(json_encode($params));
//
//        $rv = Http::get('https://app.fresho.com/api/v1/public/jobs/89cbd6a54110b6668f907f83')->json();
//        Log::debug(json_encode($rv));
//        Log::debug('-----------');
//        Log::debug('-----------');
//        Log::debug('-----------');
//        Log::debug('-----------');
//        Log::debug('-----------');
//        Log::debug('-----------');
//        $rv = Http::asMultipart()->post($rptUrl, $params)->body();
//        Log::debug(json_encode($rv));

        return ['ok' => true, 'data' => ''];
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
