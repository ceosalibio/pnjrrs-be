<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PnCategoryController;
use App\Http\Controllers\PnUnitController;
use App\Http\Controllers\PnSubUnitController;
use App\Http\Controllers\PnOfficeController;
use App\Http\Controllers\PnSubOfficeController;
use App\Http\Controllers\PnSerialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemAfposController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingPersonnelController;
use App\Http\Controllers\SettingOrganizationController;
use App\Http\Controllers\ReportPersonnelController;
use App\Http\Controllers\ReportTrainingController;
use App\Http\Controllers\ReportEquipmentController;
use App\Http\Controllers\TrainingItemController;
use App\Http\Controllers\EquipmentItemController;




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

    // PN Serials Management
    Route::apiResource('pn-serials', PnSerialController::class);
    Route::get('pn-serials/report-month/{reportMonth}', [PnSerialController::class, 'getByReportMonth']);
    Route::get('pn-serials/personnel-report/{personnelReportId}', [PnSerialController::class, 'getByPersonnelReport']);

    // User Management
    Route::get('users/get-rank', [UserController::class, 'getRank']);
    Route::get('users/rank/{rankId}', [UserController::class, 'getByRank']);
    Route::get('users/search', [UserController::class, 'search']);
    Route::apiResource('users', UserController::class);
   

    // Item AFPOS Management
    Route::apiResource('item-afpos', ItemAfposController::class);
    Route::get('item-afpos/division/{divisionId}', [ItemAfposController::class, 'getByDivision']);

    // route for reports
    Route::prefix('report')->group(function () {
        Route::get('personnels/grouped-by-office', [ReportPersonnelController::class, 'getItemsGroupedByOffice']);
        Route::apiResource('personnels', ReportPersonnelController::class);
        Route::get('trainings/grouped-by-office', [ReportTrainingController::class, 'getItemsGroupedByOffice']);
        Route::apiResource('trainings', ReportTrainingController::class);
        Route::get('equipments/grouped-by-office', [ReportEquipmentController::class, 'getItemsGroupedByOffice']);
        Route::apiResource('equipments', ReportEquipmentController::class);
    });
    // route for settings
    Route::prefix('settings')->group(function () {
        Route::apiResource('organizations', SettingOrganizationController::class);
        Route::apiResource('personnels', SettingPersonnelController::class);
        // Training Items Management
        Route::apiResource('training-items', TrainingItemController::class);
        Route::get('training-items/unit/{unitId}', [TrainingItemController::class, 'getByUnit']);
        Route::get('training-items/year/{year}', [TrainingItemController::class, 'getByYear']);
        // Equipment Items Management
        Route::get('equipment-items/template', [EquipmentItemController::class, 'getTemplate']);
        Route::get('equipment-items/template/grouped', [EquipmentItemController::class, 'getTemplateGrouped']);
        Route::apiResource('equipment-items', EquipmentItemController::class);
        Route::get('equipment-items/unit/{unitId}', [EquipmentItemController::class, 'getByUnit']);
        Route::get('equipment-items/year/{year}', [EquipmentItemController::class, 'getByYear']);
    });

    
});
