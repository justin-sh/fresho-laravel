<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/orders/label', [OrderController::class, 'printLabel']);
Route::post('/orders/label', [OrderController::class, 'printLabel']);

Route::fallback(fn() => view('index'));
