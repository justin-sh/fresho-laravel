<?php

namespace App\Http\Controllers;

use App\Models\DailyStock;
use App\Models\Order;
use App\Models\OrderState;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inventoryDateStr = $request->str('d', date('Y-m-d'))->value();

        $orders = DB::table('orders')
            ->join('order_details', 'orders.order_number', 'order_details.order_number')
            ->select('orders.receiving_company_name',
                'order_details.prd_code', 'order_details.prd_name', 'order_details.qty', 'order_details.qty_type',
                'order_details.customer_notes', 'order_details.supplier_notes'
            )
            ->where('orders.delivery_date', $inventoryDateStr)
            ->whereIn('orders.state', [OrderState::Invoiced->value, OrderState::Paid->value])
            ->where('orders.is_credit_note', false)
            ->whereIn('order_details.group', ['Frozen Products', 'Band Saw'])
            ->whereIn('order_details.status', ['substituted', 'supplied'])
            ->get();

        $rv  = $orders->mapToGroups(function ($item){
            return [$item->prd_code . '--' . $item->prd_name => $item];
        });

        return json_encode(['ok'=>true, 'data'=>$rv]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyStock $dailyStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyStock $dailyStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyStock $dailyStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyStock $dailyStock)
    {
        //
    }
}
