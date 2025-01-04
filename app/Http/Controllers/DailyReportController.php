<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $report_date): array
    {
        $orders = Order::query()
            ->with('details')
            ->where('delivery_date', $report_date)
            ->get(['id', 'order_number', 'receiving_company_name', 'additional_notes']);

        $prds = [
            'belly',
            'bellyROff',
            'bellyBoneIn',
            'bbq',
            'pribs',
            'pmeatyribs',
            'pexmeatyribs',
            'pfrzribs',

            'ckbr',
            'cksoff',
            'ckson',
            'ckbi',
            'ckthoff',
            'ckthon',
            'cklegette',
            'ckwings',
            'ckwingette',
            'cktdr',
        ];

        $rv = [];
        foreach ($prds as $p) {
            $rv[$p] = ['sum' => 0, 'details' => []];
        }

        $envPrdMap = [
            'REPORT_DAILY_PORK_BELLY_RON_BL' => 'belly',
            'REPORT_DAILY_PORK_BELLY_ROFF_BL' => 'bellyROff',
            'REPORT_DAILY_PORK_BELLY_RON_BI' => 'bellyBoneIn',
            'REPORT_DAILY_PORK_BBQ' => 'bbq',
            'REPORT_DAILY_PORK_RIBS' => 'pribs',
            'REPORT_DAILY_PORK_RIBS_MEATY' => 'pmeatyribs',
            'REPORT_DAILY_PORK_RIBS_EX_MEATY' => 'pexmeatyribs',
            'REPORT_DAILY_PORK_RIBS_FRZ' => 'pfrzribs',

            'REPORT_DAILY_CK_BREAST' => 'ckbr',
            'REPORT_DAILY_CK_SOFF' => 'cksoff',
            'REPORT_DAILY_CK_SON' => 'ckson',
            'REPORT_DAILY_CK_BI' => 'ckbi',
            'REPORT_DAILY_CK_TH_OFF' => 'ckthoff',
            'REPORT_DAILY_CK_TH_ON' => 'ckthon',
            'REPORT_DAILY_CK_LEGETTE' => 'cklegette',
            'REPORT_DAILY_CK_WINGS' => 'ckwings',
            'REPORT_DAILY_CK_WINGETTE' => 'ckwingette',
            'REPORT_DAILY_CK_TDR' => 'cktdr',
        ];

        //REPORT_DAILY_PORK_BELLY_RON_BI_EXCLUDED

        $codePrdMap = [];
        foreach ($envPrdMap as $k => $v) {
            collect(explode(',', env($k, '')))->each(function ($code) use ($v, &$codePrdMap) {
                $codePrdMap[$code] = $v;
            });
        }


        foreach ($orders as $odr) {
            foreach ($odr->details as $d) {

                if (array_key_exists($d->prd_code, $codePrdMap)) {
                    $prd = $codePrdMap[$d->prd_code];

                    $rv[$prd]['sum'] += $d->qty;
                    $rv[$prd]['details'][] = [
                        'customer' => $odr->receiving_company_name,
                        'qty' => $d->qty,
                        'customer_notes' => $d->customer_notes ?? '',
                        'supplier_notes' => $d->supplier_notes ?? '',
                    ];
                }
            }
        }

        foreach ($prds as $p) {
            $rv[$p]['sum'] = (float)(string)($rv[$p]['sum']);
        }

        return ['ok' => true, 'data' => $rv];
    }
}
