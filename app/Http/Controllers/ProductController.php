<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

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

        $products = Product::query()
            ->when($cat, function (Builder $query, $cat) {
                $query->whereIn('cat', $cat);
            })
            ->when($name, function (Builder $query, $name) {
                $query->whereLike('name', '%' . $name . '%');
            })
            ->orderBy('cat')
            ->orderBy('name')
            ->with('warehouses', function (BelongsToMany $query) use ($wh) {
//                Log::debug($query->getTable());
                if($wh){
                    $query->whereIn('warehouse_id', $wh);
                }
            })
            ->get();

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
