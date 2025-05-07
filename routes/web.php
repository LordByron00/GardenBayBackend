<?php

use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::get('/menu', [MenuItemController::class, 'index']);
Route::post('/menu', [MenuItemController::class, 'storeMenu']);
Route::put('/menu/{menuItem}', [MenuItemController::class, 'update']);
Route::get('/menu-image/{filename}', function ($filename) {
    $path = storage_path('app/public/menu_images/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
});

// Route::post('/order', [OrderController::class, 'index']);

