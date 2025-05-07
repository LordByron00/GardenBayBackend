<?php
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\MenuItemController;
use  App\Http\Controllers\OrderController;


// Route::get('/menu-items', [MenuItemController::class, 'index']);
// Route::post('/orders', [OrderController::class, 'store']);

// Route::middleware(['web', 'cors'])->group(function () {
//     Route::post('/menu', [MenuItemController::class, 'storeMenu']);
// });

// Route::middleware(['cors'])->post('/menu', function() {
//     // Log something here to check if this code is even reached
//     \Illuminate\Support\Facades\Log::info('POST request reached /menu route!');
//     return response()->json(['message' => 'POST request received successfully!'], 200);
// });