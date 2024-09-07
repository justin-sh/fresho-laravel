<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user-info', function (Request $request) {
    $user = $request->user();
    if ($user == null) return json_encode(['id' => 0, 'name' => '', 'full_name' => '']);
    return json_encode($user);
});
