<?php

namespace App\Http\Controllers;

use App\Models\DailyStock;
use App\Models\FreshoProduct;
use App\Models\Order;
use App\Models\OrderPrdState;
use Carbon\Carbon;
use Illuminate\Support\Arr;
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
            ->whereIn("state", ['submitted', 'accepted', 'invoiced', 'paid'])
            ->where("payable_total_in_cents", ">", 0)
            ->get(['id', 'order_number', 'receiving_company_name', 'additional_notes']);

        $prds = [
            'belly',
            'belly_fresh',
            'bellyROff',
            'bellyBoneIn',
            'bbq',
            'bbqleg',
            'plegmeat',
            'pribs',
            'pneck',
            'pmeatyribs',
            'pexmeatyribs',
            'pfrzribs',
            'pcutlets',
            'pmeatyriblet',
            'pmeatylegbone',
            'pexmeatylegbone',
            'pmeatyneckbone',
            'pexmeatyneckbone',
            'ploinrindoff',
            'ploinrindon',
            'pshrindon',
            'pmiddle',
            'pfat',

            'ckbr',
            'cksoff',
            'ckson',
            'ckbi',
            'ckthoff',
            'ckthoff16',
            'ckthoff22',
            'ckthoff28',
            'ckthon',
            'ckthon16',
            'cklegette',
            'ckwings',
            'ckwingette',
            'cktdr',
            'ckrib',
            'ckbutt',
            'ckbron',
            'cklegetteon',
            'ckdrumstick',
            'ckchopon',
            'ckwboff15',
            'ckbrbf',

            'lshoulder',
            'lleg',
            'lrump',
        ];

        $rv = ['others' => []];
        foreach ($prds as $p) {
            $rv[$p] = ['sum' => 0, 'details' => []];
        }

        $envPrdMap = [
            'REPORT_DAILY_PORK_BELLY_RON_BL' => 'belly',
            'REPORT_DAILY_PORK_BELLY_ROFF_BL' => 'bellyROff',
            'REPORT_DAILY_PORK_BELLY_RON_BI' => 'bellyBoneIn',
            'REPORT_DAILY_PORK_BBQ' => 'bbq',
            'REPORT_DAILY_PORK_BBQ_LEG' => 'bbqleg',
            'REPORT_DAILY_PORK_LEG_MEAT' => 'plegmeat',
            'REPORT_DAILY_PORK_RIBS' => 'pribs',
            'REPORT_DAILY_PORK_NECK' => 'pneck',
            'REPORT_DAILY_PORK_RIBS_MEATY' => 'pmeatyribs',
            'REPORT_DAILY_PORK_RIBS_EX_MEATY' => 'pexmeatyribs',
            'REPORT_DAILY_PORK_RIBS_FRZ' => 'pfrzribs',
            'REPORT_DAILY_PORK_CUTLETS' => 'pcutlets',
            'REPORT_DAILY_PORK_RIBLET_MEATY' => 'pmeatyriblet',
            'REPORT_DAILY_PORK_LEG_BONE_MEATY' => 'pmeatylegbone',
            'REPORT_DAILY_PORK_LEG_BONE_EX_MEATY' => 'pexmeatylegbone',
            'REPORT_DAILY_PORK_NECK_BONE_MEATY' => 'pmeatyneckbone',
            'REPORT_DAILY_PORK_NECK_BONE_EX_MEATY' => 'pexmeatyneckbone',
            'REPORT_DAILY_PORK_LOIN_RIND_OFF' => 'ploinrindoff',
            'REPORT_DAILY_PORK_LOIN_RIND_ON' => 'ploinrindon',
            'REPORT_DAILY_PORK_SHOULDER_RIND_ON' => 'pshrindon',
            'REPORT_DAILY_PORK_MIDDLE' => 'pmiddle',
            'REPORT_DAILY_PORK_FAT' => 'pfat',

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
            'REPORT_DAILY_CK_RIB' => 'ckrib',
            'REPORT_DAILY_CK_BUTT' => 'ckbutt',
            'REPORT_DAILY_CK_BR_ON' => 'ckbron',
            'REPORT_DAILY_CK_DRUMSTICK' => 'ckdrumstick',
            'REPORT_DAILY_CK_CHOP_ON' => 'ckchopon',
            'REPORT_DAILY_CK_BR_BUTTERFLIED' => 'ckbrbf',

            'REPORT_DAILY_LAMB_SHOULDER' => 'lshoulder',
            'REPORT_DAILY_LAMB_LEG' => 'lleg',
            'REPORT_DAILY_LAMB_RUMP' => 'lrump',
        ];

        //REPORT_DAILY_PORK_BELLY_RON_BI_EXCLUDED
        $envNameMap = [
            'REPORT_DAILY_PORK_BELLY_RON_BI_NAME' => 'bellyBoneIn',
            'REPORT_DAILY_CK_TH_OFF_NAME' => 'ckthoff',
            'REPORT_DAILY_CK_LEGETTE_NAME' => 'cklegette',
            'REPORT_DAILY_CK_LEGETTE_ON_NAME' => 'cklegetteon',
            'REPORT_DAILY_CK_TH_OFF_NAME22' => 'ckthoff22',
        ];

        $codePrdMap = [];
        foreach ($envPrdMap as $k => $v) {
            collect(explode(',', env($k, '')))->each(function ($code) use ($v, &$codePrdMap) {
                $codePrdMap[$code] = $v;
            });
        }

        $namePrdMap = [];
        foreach ($envNameMap as $k => $v) {
            collect(explode(',', env($k, '')))->each(function ($name) use ($v, &$namePrdMap) {
                $namePrdMap[$name] = $v;
            });
        }

        $cusForFreshBoningBelly = explode(",", env("REPORT_DAILY_PORK_BELLY_RON_BL_FRESH_CUS",''));
        $codeForBellyBL = explode(",", env("REPORT_DAILY_PORK_BELLY_RON_BL",''));

        $products = FreshoProduct::query()->get(['code', 'name', 'mkt_cat']);
        $freshoPrdMap = [];

        $products->each(function ($p) use (&$freshoPrdMap) {
            $freshoPrdMap[$p->code] = $p;
        });

        $ignore_mkt_cats = ['BEEF', 'ANGUS BEEF', 'DUCK', 'GOAT', 'HOT POT', '-LAMB', 'SEA FOOD', 'SEAFOOD', 'SMALL GOODS', '-WAGYU'];

        foreach ($orders as $odr) {
            foreach ($odr->details as $d) {

                // not chicken and not pork then skip
                if (array_key_exists($d->prd_code, $freshoPrdMap)
                    && in_array($freshoPrdMap[$d->prd_code]->mkt_cat, $ignore_mkt_cats)) {
                    continue;
                }

                // back order
                if($d->status->name == OrderPrdState::BackOrder->name){
                    continue;
                }

                // special rules
                if (str_contains($d->supplier_notes, "用16号鸡切 鸡上腿肉")) {

                    $rv['ckthon16']['sum'] += $d->qty;
                    $rv['ckthon16']['details'][] = [
                        'customer' => $odr->receiving_company_name,
                        'prd_code' => $d->prd_code,
                        'prd_name' => $d->prd_name,
                        'qty' => $d->qty,
                        'customer_notes' => $d->customer_notes ?? '',
                        'supplier_notes' => $d->supplier_notes ?? '',
                    ];

                    continue;
                }
                if (str_contains($d->supplier_notes, "Whole chicken Skinless size 14/15")) {

                    $rv['ckwboff15']['sum'] += $d->qty;
                    $rv['ckwboff15']['details'][] = [
                        'customer' => $odr->receiving_company_name,
                        'prd_code' => $d->prd_code,
                        'prd_name' => $d->prd_name,
                        'qty' => $d->qty,
                        'customer_notes' => $d->customer_notes ?? '',
                        'supplier_notes' => $d->supplier_notes ?? '',
                    ];

                    continue;
                }
                // Chicken Thigh Fillet Skin Off
                if ('3023' == $d->prd_code) {

                    if (str_contains($d->supplier_notes, "size22")) {
                        $rv['ckthoff22']['sum'] += $d->qty;
                        $rv['ckthoff22']['details'][] = [
                            'customer' => $odr->receiving_company_name,
                            'prd_code' => $d->prd_code,
                            'prd_name' => $d->prd_name,
                            'qty' => $d->qty,
                            'customer_notes' => $d->customer_notes ?? '',
                            'supplier_notes' => $d->supplier_notes ?? '',
                        ];

                        continue;
                    }
                    if (str_contains($d->supplier_notes, "USE SZE 28 ONLY")) {

                        $rv['ckthoff28']['sum'] += $d->qty;
                        $rv['ckthoff28']['details'][] = [
                            'customer' => $odr->receiving_company_name,
                            'prd_code' => $d->prd_code,
                            'prd_name' => $d->prd_name,
                            'qty' => $d->qty,
                            'customer_notes' => $d->customer_notes ?? '',
                            'supplier_notes' => $d->supplier_notes ?? '',
                        ];
                        continue;
                    }

                }

                // Linh Vietnamese Fast Food + Vuche & Co Viet Eatery sum bbq shoulder to bbq leg
                if( '1055' == $d->prd_code){
                    if(in_array($odr->receiving_company_name, ["Linh Vietnamese Fast Food", "Vuche & Co Viet Eatery"])){
                        $rv['bbqleg']['sum'] += $d->qty;
                        $rv["bbqleg"]['details'][] = [
                            'customer' => $odr->receiving_company_name,
                            'prd_code' => $d->prd_code,
                            'prd_name' => $d->prd_name,
                            'qty' => $d->qty,
                            'customer_notes' => $d->customer_notes ?? '',
                            'supplier_notes' => $d->supplier_notes ?? '',
                        ];

                        continue;
                    }
                }

                // normal rules
                if (array_key_exists($d->prd_code, $codePrdMap)) {
//                    Log::debug("Fresh boning belly for ".$odr->receiving_company_name);
                    if(in_array($d->prd_code, $codeForBellyBL) && in_array($odr->receiving_company_name, $cusForFreshBoningBelly)){
                        Log::debug("Fresh boning belly for $odr->receiving_company_name");
                        $prd = 'belly_fresh';
                    }else{
                        $prd = $codePrdMap[$d->prd_code];
                    }


                    $rv[$prd]['sum'] += $d->qty;
                    $rv[$prd]['details'][] = [
                        'customer' => $odr->receiving_company_name,
                        'prd_code' => $d->prd_code,
                        'prd_name' => $d->prd_name,
                        'qty' => $d->qty,
                        'customer_notes' => $d->customer_notes ?? '',
                        'supplier_notes' => $d->supplier_notes ?? '',
                    ];

                    continue;
                }


                if (array_key_exists($d->prd_name, $namePrdMap)) {
                    $prd = $namePrdMap[$d->prd_name];

                    $rv[$prd]['sum'] += $d->qty;
                    $rv[$prd]['details'][] = [
                        'customer' => $odr->receiving_company_name,
                        'prd_code' => $d->prd_code,
                        'prd_name' => $d->prd_name,
                        'qty' => $d->qty,
                        'customer_notes' => $d->customer_notes ?? '',
                        'supplier_notes' => $d->supplier_notes ?? '',
                    ];

                    continue;
                }

                if (!array_key_exists($d->prd_name, $rv['others'])) {
                    $rv['others'][$d->prd_name] = ['sum' => 0, 'details' => []];
                }

                $rv['others'][$d->prd_name]['sum'] += $d->qty;
                $rv['others'][$d->prd_name]['details'][] = [
                    'customer' => $odr->receiving_company_name,
                    'prd_code' => $d->prd_code,
                    'prd_name' => $d->prd_name,
                    'qty' => $d->qty,
                    'customer_notes' => $d->customer_notes ?? '',
                    'supplier_notes' => $d->supplier_notes ?? '',
                ];

            }
        }

        foreach ($prds as $p) {
            if ('others' == $p) continue;

            $rv[$p]['sum'] = (float)(string)($rv[$p]['sum']);

            usort($rv[$p]['details'], fn($x, $y) => strcmp($x['customer'], $y['customer']));
        }

        foreach ($rv['others'] as $pname => $v) {
            $rv['others'][$pname]['sum'] = (float)(string)($rv['others'][$pname]['sum']);

            usort($rv['others'][$pname]['details'], fn($x, $y) => strcmp($x['customer'], $y['customer']));
        }

        ksort($rv['others']);

        // get stock of previous day
        $rd = Carbon::create($report_date)->subDay();
        if ($rd->isSunday()) {
            $rd->subDay();
        }

        $prevDayStock = DailyStock::query()->where('stock_date', $rd->toDateString())->first()?->stock ?? [];
        $dailyStockKV = [
            'belly_fresh' => 'NA',
            'belly' => 'Pork-Belly Rind On',
            'bellyROff' => 'Pork-Belly Rind Off',
            'bellyBoneIn' => 'NA',
            'ploinrindoff' => 'NA',
            'ploinrindon' => 'NA',
            'bbq' => 'Pork-BBQ',
            'bbqleg' => 'Pork-BBQ-LeG',
            'plegmeat' => 'Pork-Leg Rind Off',
            'pribs' => 'NA',
            'pneck' => 'Pork-Necks',

            'ckbr' => 'Chicken-Breast',
            'cksoff' => 'Chicken-Maryland Fillet Off',
            'ckson' => 'Chicken-Maryland Fillet ON',
            'ckthoff' => 'Chicken-Thing Skin Off',
            'ckthon' => 'Chicken-Thing Skin On',
            'cklegette' => 'Chicken-Legettes',
            'ckwings' => 'Chicken-Wings',
            'ckwingette' => 'NA',
            'cktdr' => 'Chicken-Tenderloin',
        ];

        $dailyStock = [];
        foreach ($dailyStockKV as $k=>$v){
            if(key_exists($v, $prevDayStock)){
                $dailyStock[$k] = $prevDayStock[$v];
            }else{
                $dailyStock[$k] = 0;
            }
        }


        return ['ok' => true, 'data' => $rv, 'dailyStock' => $dailyStock];
    }
}
