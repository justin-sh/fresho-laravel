<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
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
        Log::debug('show id=' . $purchaseOrder->id);

        $purchaseOrder->load('products');
        return new PurchaseOrderResource($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): array
    {
        $data = $request->json()->all();
        Log::debug('update....');
        Log::debug('update..data->qty..' . $data['qty']);
        Log::debug('update..->qty..' . $purchaseOrder->qty);

        DB::transaction(function () use ($data, $purchaseOrder) {
            $purchaseOrder->update(['title' => $data['title'], 'qty' => $data['qty'], 'arrival_at' => $data['arrivalAt']]);
            //todo update details
        });

        return ['ok' => true, 'data' => []];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
