<?php

use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/orders/label', [OrderController::class, 'printLabel']);
Route::post('/orders/label', [OrderController::class, 'printLabel']);

Route::get('/file-download', function (Request $request) {
    $file = $request->input('f', '');

    if(empty($file)){
        abort(404);
    }
    return response()->file(storage_path('app/tmp/label/' . $file));
});

Route::fallback(fn() => view('index'));
