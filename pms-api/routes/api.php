<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportsController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Health Check
    |--------------------------------------------------------------------------
    */
    Route::get('/health', function () {
        return response()->json([
            'status'  => 'running',
            'service' => 'PMS API Running',
            'version' => '1.0',
        ]);
    });

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {


        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', function (Request $request) {
            return response()->json($request->user());
        });


        Route::apiResource('projects', ProjectController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::apiResource('reports', ReportsController::class)
            ->only(['index', 'store', 'update', 'destroy']);


        Route::patch('/reports/{id}/status', [ReportsController::class, 'updateStatus']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});