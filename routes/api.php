<?php

use App\Http\Controllers\MovementController;
use App\Http\Controllers\Order\OrderCancelController;
use App\Http\Controllers\Order\OrderCompleteController;
use App\Http\Controllers\Order\OrderIndexController;
use App\Http\Controllers\Order\OrderResumeController;
use App\Http\Controllers\Order\OrderStoreController;
use App\Http\Controllers\Order\OrderUpdateController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('orders')->group(function () {
    Route::get('', OrderIndexController::class);
    Route::post('', OrderStoreController::class);

    Route::prefix('{id}')->group(function () {
        Route::put('', OrderUpdateController::class);

        Route::patch('cancel', OrderCancelController::class);
        Route::patch('complete', OrderCompleteController::class);
        Route::patch('resume', OrderResumeController::class);
    });
});


Route::prefix('warehouses')->group(function () {
    Route::get('', [WarehouseController::class, 'index']);
    Route::get('/with-products', [WarehouseController::class, 'productsByWarehouse']);
});

Route::get('stocks', [StockController::class, 'index']);

Route::get('movements', [MovementController::class, 'index']);
