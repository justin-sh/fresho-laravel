<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user-info', function (Request $request) {
    $user = $request->user();
    if ($user == null) return json_encode(['id' => 0, 'name' => '', 'full_name' => '']);
    return json_encode($user);
});

// order api
Route::get('/orders/sync-summary', [OrderController::class, 'syncSummary']);
Route::get('/orders/sync-detail', [OrderController::class, 'syncDetail']);
Route::get('/orders/sync-delivery-proof', [OrderController::class, 'syncDeliveryProof']);
Route::apiResource('/orders', OrderController::class)->only(['index', 'show']);


//product api
Route::get('/products/all', [ProductController::class, 'all']);
Route::apiResource('/products', ProductController::class);

Route::apiResource('/warehouses', WarehouseController::class);

//purchase order api
Route::apiResource('/purchase-orders', PurchaseOrderController::class);
Route::put('/purchase-orders/{purchase_order}/approve', [PurchaseOrderController::class, 'approve']);


//sale order api
Route::apiResource('/sale-orders', SaleOrderController::class);
Route::put('/sale-orders/{sale_order}/approve', [SaleOrderController::class, 'approve']);
