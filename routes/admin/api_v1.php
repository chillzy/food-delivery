<?php

use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\MealController;
use App\Http\Controllers\Api\V1\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::name('v1.admin.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        });

        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    Route::prefix('categories')->middleware('auth')->name('category.')->group(function () {
        Route::get('/', [CategoryController::class, 'list'])->name('list');
        Route::post('/', [CategoryController::class, 'create'])->name('create');
        Route::get('/{id}', [CategoryController::class, 'get'])->name('get');
        Route::delete('/{id}', [CategoryController::class, 'delete'])->name('delete');
    });

    Route::prefix('meals')->middleware('auth')->name('meal.')->group(function () {
        Route::get('/', [MealController::class, 'list'])->name('list');
        Route::post('/', [MealController::class, 'create'])->name('create');
        Route::get('/{id}', [MealController::class, 'get'])->name('get');
        Route::put('/{id}', [MealController::class, 'update'])->name('update');
        Route::delete('/{id}', [MealController::class, 'delete'])->name('delete');
    });

    Route::prefix('orders')->name('order.')->middleware('auth')->group(function () {
        Route::get('/', [OrderController::class, 'list'])->name('list');
        Route::put('/{id}/status', [OrderController::class, 'moveStatus'])->name('moveStatus');
        Route::delete('/{id}', [OrderController::class, 'cancel'])->name('cancel');
    });
});
