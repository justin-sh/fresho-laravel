<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $curPage = 1;
        $pageSize = 50;
        $data = PurchaseOrder::query()->orderByDesc('arrival_at')->limit($pageSize)->get();
        return PurchaseOrderResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->json()->all();
        Log::debug('---------PurchaseOrderController.store------');
        Log::debug('---------PurchaseOrderController.store------');
        Log::debug(json_encode($data));
        Log::debug($data['title']);

        $po = new PurchaseOrder();
        $po->title = $data['title'];
        $po->qty = $data['qty'];
        $po->arrival_at = $data['arrivalAt'];
        $po->state = $data['status'];
        $po->warehouse()->associate(Warehouse::find($data['whId']));
        $po->save();

        foreach ($data['details'] as $k => $detail) {
            $params = ['row_no' => $k,
                'qty' => $detail['qty'],
                'location' => array_key_exists('location', $detail) ? $detail['location'] : '',
                'comment' => array_key_exists('comment', $detail) ? $detail['comment'] : ''
            ];
            $po->products()->attach($detail['prdId'], $params);
        }

        Log::debug($po);

        return ['ok' => true, 'data' => ['id' => $po->id]];
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
