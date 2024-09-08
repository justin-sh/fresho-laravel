<?php

use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user-info', function (Request $request) {
    $user = $request->user();
    if ($user == null) return json_encode(['id' => 0, 'name' => '', 'full_name' => '']);
    return json_encode($user);
});

Route::get('/orders/init', [OrderController::class, 'init']);
Route::apiResource('/orders', OrderController::class)->only(['index', 'show']);
