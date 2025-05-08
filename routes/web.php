<?php

use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::get('/menu', [MenuItemController::class, 'index']);
Route::post('/menu', [MenuItemController::class, 'storeMenu']);
Route::put('/menu/{menuItem}', [MenuItemController::class, 'update']);
Route::post('/order', [OrderController::class, 'Store']);
Route::get('/order', [OrderController::class, 'index']);
Route::get('/test-cookies', function () {
    return response()->json(request()->cookies->all());
});


