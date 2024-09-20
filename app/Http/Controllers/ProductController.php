<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $cat = $request->input('cat', []);
        $wh = $request->input('wh', []);
        $name = $request->str('name', '')->value();
        $hasStock = $request->boolean('hasStock', false);

        $products = Product::query()
            ->when($cat, function (Builder $query, $cat) {
                $query->whereIn('cat', $cat);
            })
            ->when($name, function (Builder $query, $name) {
                $query->whereLike('name', '%' . $name . '%');
            })
            ->whereRelation('warehouses', function (Builder $query) use ($wh, $hasStock) {
                if ($wh) {
                    $query->whereIn('warehouse_id', $wh);
                }
                if ($hasStock) {
                    $query->where('onhand_qty', '>', 0);
                }
            })
            ->with('warehouses')
            ->orderBy('cat')
            ->orderBy('name')
            ->get();

        return ProductResource::collection($products);
    }

    public function all(Request $request): JsonResource
    {
        $products = Product::query()->get(['id', 'cat', 'name']);
        return ProductResource::collection($products);
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
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
