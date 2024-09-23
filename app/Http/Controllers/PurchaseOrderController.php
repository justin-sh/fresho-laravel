<?php

namespace App\Http\Controllers;

use App\Events\PurchaseOrderApproved;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\PurchaseStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $pageSize = 50;
        $data = PurchaseOrder::query()
            ->with('warehouse')
            ->orderByDesc('arrival_at')
            ->orderBy('title')
            ->limit($pageSize)
            ->get();
        return PurchaseOrderResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): array
    {
        $data = $request->json()->all();

        $po = new PurchaseOrder();
        $po->title = $data['title'];
        $po->qty = $data['qty'];
        $po->arrival_at = $data['arrivalAt'];
        $po->state = $data['state'];
        $po->warehouse()->associate(Warehouse::find($data['whId']));
        DB::transaction(function () use ($po, $data) {
            $po->save();

            foreach ($data['details'] as $k => $detail) {
                $params = ['row_no' => $k,
                    'qty' => $detail['qty'],
                    'location' => array_key_exists('location', $detail) ? $detail['location'] : '',
                    'comment' => array_key_exists('comment', $detail) ? $detail['comment'] : ''
                ];
                $po->products()->attach($detail['prdId'], $params);
            }
        });

        return ['ok' => true, 'data' => ['id' => $po->id]];
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('products');
        return new PurchaseOrderResource($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): array
    {
        $data = $request->json()->all();
        DB::transaction(function () use ($data, $purchaseOrder) {
            $purchaseOrder->update(['title' => $data['title'], 'qty' => $data['qty'], 'arrival_at' => $data['arrivalAt']]);

            $details = [];
            collect($data['details'])->each(function ($pod) use (&$details) {
                $details[$pod['prdId']] = [
                    'qty' => Arr::get($pod, 'qty'),
                    'location' => Arr::get($pod, 'location'),
                    'comment' => Arr::get($pod, 'comment')
                ];
            });

            $purchaseOrder->products()->sync($details);
        });

        return ['ok' => true, 'data' => []];
    }

    public function approve(PurchaseOrder $purchaseOrder): array
    {
        if (PurchaseStatus::INIT->value == $purchaseOrder->state) {

            $purchaseOrder->state = PurchaseStatus::APPROVE;
            $purchaseOrder->update();

            PurchaseOrderApproved::dispatch($purchaseOrder);
        }

        return ['ok' => true];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
