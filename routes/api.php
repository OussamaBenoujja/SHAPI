<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
   
    
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/departments/{department}', [DepartmentController::class, 'show']);
    
    
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/departments/{department}/products', [ProductController::class, 'departmentProducts']);
    Route::get('/products/promotional', [ProductController::class, 'promotionalProducts']);
});


Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

   
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

   
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
    
    
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
   
    Route::get('/stock/critical', [StockController::class, 'criticalStock']);
    Route::put('/stock/{product}', [StockController::class, 'updateStock']);
    Route::get('/stock/statistics', [StockController::class, 'statistics']);
});