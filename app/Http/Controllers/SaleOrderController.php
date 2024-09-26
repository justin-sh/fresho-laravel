<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleOrderResource;
use App\Models\SaleOrder;
use App\Models\Warehouse;
use App\SalesStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageSize = 50;
        $data = SaleOrder::query()
            ->with('warehouse')
            ->orderByDesc('pickup_at')
            ->orderBy('title')
            ->limit($pageSize)
            ->get();
        return SaleOrderResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): array
    {
        $data = $request->json()->all();

        $so = new SaleOrder();
        $so->title = $data['title'];
        $so->qty = $data['qty'];
        $so->pickup_at = $data['pickupAt'];
        $so->state = $data['state'];
        $so->warehouse()->associate(Warehouse::find($data['whId']));
        DB::transaction(function () use ($so, $data) {
            $so->save();

            foreach ($data['details'] as $k => $detail) {
                $params = ['row_no' => $k,
                    'qty' => $detail['qty'],
                    'location' => array_key_exists('location', $detail) ? $detail['location'] : '',
                    'comment' => array_key_exists('comment', $detail) ? $detail['comment'] : ''
                ];
                $so->products()->attach($detail['prdId'], $params);
            }
        });

        return ['ok' => true, 'data' => ['id' => $so->id]];
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleOrder $saleOrder)
    {
        $saleOrder->load('products');
        return new SaleOrderResource($saleOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleOrder $saleOrder): array
    {
        $data = $request->json()->all();
        DB::transaction(function () use ($data, $saleOrder) {
            $saleOrder->update(['title' => $data['title'], 'qty' => $data['qty'], 'pickup_at' => $data['pickupAt']]);

            $details = [];
            collect($data['details'])->each(function ($pod) use (&$details) {
                $details[$pod['prdId']] = [
                    'qty' => Arr::get($pod, 'qty'),
                    'location' => Arr::get($pod, 'location'),
                    'comment' => Arr::get($pod, 'comment')
                ];
            });

            $saleOrder->products()->sync($details);
        });

        return ['ok' => true, 'data' => []];
    }

    public function approve(SaleOrder $saleOrder): array
    {
        if (SalesStatus::INIT->value == $saleOrder->state) {

            $saleOrder->state = SalesStatus::APPROVE;
            $saleOrder->update();

//            PurchaseOrderApproved::dispatch($saleOrder);
        }

        return ['ok' => true];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleOrder $saleOrder)
    {
        //
    }
}
