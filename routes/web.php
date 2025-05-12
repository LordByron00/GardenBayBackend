<?php

use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use  App\Http\Controllers\StocksController;
use  App\Http\Controllers\AnalyticsController;



// Route::get('/test-cookies', function () {
//     return response()->json(request()->cookies->all());
// });
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::get('/menu', [MenuItemController::class, 'index']);
Route::post('/menu', [MenuItemController::class, 'storeMenu']);
Route::put('/menu/{menuItem}', [MenuItemController::class, 'update']);

Route::post('/order', [OrderController::class, 'Store']);
Route::get('/order', [OrderController::class, 'index']);

Route::post('/stocks', [StocksController::class, 'Store']);
Route::get( '/stocks', [StocksController::class, 'index']);
Route::put('/stock/{stock}', [StocksController::class, 'update']);
Route::delete('/stock/{stock}', [StocksController::class, 'destroy']);

Route::get('/analytics/revenue/day', [AnalyticsController::class, 'revenuePerDay']);
Route::get('/analytics/revenue/week', [AnalyticsController::class, 'revenuePerWeek']);
Route::get('/analytics/revenue/month', [AnalyticsController::class, 'revenuePerMonth']);
Route::get('/analytics/metrics', [AnalyticsController::class, 'dashboardMetrics']);

Route::get('/analytics', [AnalyticsController::class, 'analytics']);







