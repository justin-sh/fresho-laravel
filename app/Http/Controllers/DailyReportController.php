<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Carbon $reportDate): array
    {
        Log::debug('params:' . $reportDate->toDateString());
        $orders = Order::query()
            ->with('details')
            ->where('delivery_date', $reportDate->toDateString())
            ->get(['id', 'order_number', 'receiving_company_name', 'additional_notes']);

        Log::debug('' . count($orders));

        $orders->each(function (Order $odr){
            Log::debug('customer:' . $odr->receiving_company_name . ' details:' . count($odr->details));
        });


        return ['ok' => true];
    }
}
