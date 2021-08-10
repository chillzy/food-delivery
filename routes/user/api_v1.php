<?php

use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Controllers\Api\V1\User\CartController;
use App\Http\Controllers\Api\V1\User\CartMealController;
use App\Http\Controllers\Api\V1\User\MealController;
use App\Http\Controllers\Api\V1\User\CategoryController;
use App\Http\Controllers\Api\V1\User\OrderController;
use App\Http\Controllers\Api\V1\User\UserAddressController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('v1.user.')->group(function () {
    Route::prefix('/addresses')->name('address.')->middleware('auth')->group(function () {
        Route::get('/', [UserAddressController::class, 'list'])->name('list');
        Route::get('/{id}', [UserAddressController::class, 'get'])->name('get');
        Route::post('/', [UserAddressController::class, 'create'])->name('create');
        Route::put('/{id}', [UserAddressController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserAddressController::class, 'delete'])->name('delete');
    });

    Route::post('/', [UserController::class, 'create'])->name('create');
    Route::get('/verify/{verificationToken}', [UserController::class, 'verify'])->name('verify');

    Route::prefix('meals')->middleware('auth')->name('meal.')->group(function () {
        Route::get('/', [MealController::class, 'list'])->name('list');
        Route::get('/{id}', [MealController::class, 'get'])->name('get');
    });

    Route::prefix('orders')->name('order.')->middleware('auth')->group(function () {
        Route::post('/', [OrderController::class, 'create'])->name('create');
    });
});

Route::prefix('categories')->middleware('auth')->name('v1.category.')->group(function () {
    Route::get('/', [CategoryController::class, 'list'])->name('list');
});

Route::prefix('auth')->name('v1.auth.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::prefix('cart')->name('v1.cart.')->middleware('auth')->group(function () {
    Route::get('/', [CartController::class, 'get'])->name('get');
    Route::post('/', [CartController::class, 'create'])->name('create');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');

    Route::prefix('meals')->name('meal.')->group(function () {
        Route::post('/', [CartMealController::class, 'create'])->name('create');
        Route::put('/{id}', [CartMealController::class, 'update'])->name('update');
        Route::delete('/{id}', [CartMealController::class, 'remove'])->name('remove');
    });
});
