<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PnCategoryController;
use App\Http\Controllers\PnUnitController;
use App\Http\Controllers\PnSubUnitController;
use App\Http\Controllers\PnOfficeController;
use App\Http\Controllers\PnSubOfficeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/ping', fn () => response()->json(['message' => 'pong']));
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World']);
});

// Public Auth Routes
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Routes with Sanctum Middleware
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Hierarchical Filter APIs
    // Categories
    Route::apiResource('categories', PnCategoryController::class);
    // Units - Filter by category_id query parameter
    Route::apiResource('units', PnUnitController::class);
    // Sub-units - Filter by unit_id query parameter
    Route::apiResource('sub-units', PnSubUnitController::class);
    // Offices - Filter by sub_unit_id query parameter
    Route::apiResource('offices', PnOfficeController::class);
    // Sub-offices - Filter by office_id query parameter
    Route::apiResource('sub-offices', PnSubOfficeController::class);

    // User Management
    Route::apiResource('users', UserController::class);
    Route::get('users/rank/{rank}', [UserController::class, 'getByRank']);
    Route::get('users/search', [UserController::class, 'search']);
});
