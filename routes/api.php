<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['message' => 'pong']));
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World']);
});
