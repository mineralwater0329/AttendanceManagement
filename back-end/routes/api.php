<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/authenticate', [AuthController::class, 'authenticate']);
Route::post('/session', [AuthController::class, 'session']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    setcookie('name', 'jota');
    return response()->json(['message' => 'success']);
});