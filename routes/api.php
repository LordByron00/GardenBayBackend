<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Http\Request;
use  App\Http\Controllers\KDSController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public route for user login
Route::post('/login', [LoginController::class, 'login']);
// Public route for user registration
Route::post('/register', [LoginController::class, 'register']);

// Protected routes that require Sanctum authentication
Route::middleware('auth:sanctum')->group(function () {
    // Get authenticated user details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route for user logout (revoking the token)
    Route::post('/logout', [LoginController::class, 'logout']);

    // Add other protected API routes here
    // Route::get('/protected-data', [YourController::class, 'getData']);
});