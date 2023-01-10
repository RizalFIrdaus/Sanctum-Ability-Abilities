<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::post('me', [AuthController::class, 'about']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'abilities:update,read'])->group(function () {
    Route::get('admin', function () {
        return response()->json([
            'status' => true,
            'message' => 'Kamu admin'
        ]);
    });
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
