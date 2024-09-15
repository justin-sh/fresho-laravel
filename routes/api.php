<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user-info', function (Request $request) {
    $user = $request->user();
    if ($user == null) return json_encode(['id' => 0, 'name' => '', 'full_name' => '']);
    return json_encode($user);
});

Route::get('/orders/sync-summary', [OrderController::class, 'syncSummary']);
Route::get('/orders/sync-detail', [OrderController::class, 'syncDetail']);
Route::get('/orders/sync-delivery-proof', [OrderController::class, 'syncDeliveryProof']);
Route::apiResource('/orders', OrderController::class)->only(['index', 'show']);

Route::apiResource('/products', ProductController::class);
