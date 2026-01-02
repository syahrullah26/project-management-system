<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\ProjectController;


Route::prefix('v1')->group(function(){
    Route::get('health',function(){
         return response()->json([
            'status'  => 'running',
            'service' => 'PMS API Running',
            'version' => '1.0',
        ]);
    });

    Route::get('project',[ProjectController::class,'index']);
    Route::post('route',[ProjectController::class,'store']);
    Route::put('project/{id}', [ProjectController::class,'update']);
    Route::delete('project/{id}',[ProjectController::class,'destroy']);


    Route::get('reports',[ReportsController::class,'index']);
    Route::post('reports',[ReportsController::class,'store']);
    Route::put('reports/{id}',[ReportsController::class,'update']);
    Route::delete('reports/{id}',[ReportsController::class,'destroy']);

});




Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
