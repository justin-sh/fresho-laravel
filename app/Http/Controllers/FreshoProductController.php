<?php

namespace App\Http\Controllers;

use App\Http\Resources\FreshoProductResource;
use App\Models\FreshoProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreshoProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $code = $request->input('code', '');
        $name = $request->str('name', '')->value();
        $cat = $request->input('cat', []);
        $pageSize = $request->input('page_size', 50);

        $products = FreshoProduct::query()
            ->whereLike('name', '%' . $name . '%')
            ->whereLike('code', $code . '%')
            ->where(function (Builder $query) use ($cat) {
                if (!empty($cat)) {
                    $query->whereIn('mkt_cat', $cat);
                }
            })
            ->orderBy('code')
            ->paginate($pageSize);

        return FreshoProductResource::collection($products);
    }

    public function all(Request $request): JsonResource
    {
        $products = Product::query()->get(['id', 'cat', 'name']);
        return FreshoProductResource::collection($products);
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
