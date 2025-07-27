<?php

namespace App\Http\Controllers;

use App\Models\DailyStock;
use App\Models\OrderState;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inventoryDateStr = $request->str('d', date('Y-m-d'))->value();

        $orders = DB::table('orders')
            ->join('order_details as d', 'orders.order_number', 'd.order_number')
            ->select(
                'd.prd_code', 'd.prd_name',
                'd.qty_type',
                'd.customer_notes',
                'd.supplier_notes',
                DB::raw('sum(d.qty) as qty')
            )
            ->where('orders.delivery_date', $inventoryDateStr)
            ->whereIn('orders.state', [OrderState::Invoiced->value, OrderState::Paid->value])
            ->where('orders.is_credit_note', false)
            ->whereIn('d.group', ['Frozen Products', 'Band Saw'])
            ->whereIn('d.status', ['substituted', 'supplied'])
            ->groupBy('d.prd_code', 'd.prd_name', 'd.qty_type', 'd.customer_notes', 'd.supplier_notes')
            ->orderBy('prd_code')
            ->get();

        $rv = $orders
            ->mapToGroups(function ($item) {
                return [$item->prd_code . '.' . $item->prd_name => $item];
            });


        $hocPrds = DB::connection('mysql2')->select('select prd_code as code, prd_name as name from hoc_products');
        return json_encode(['ok' => true, 'data' => $rv, 'hocPrds'=>$hocPrds]);
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
