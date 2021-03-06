<?php

use App\Http\Controllers\Api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthenticatedController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/admin/user', function(Request $request) {
  if(Auth::guard('admin')->check()) {
    return Auth::guard('admin')->user();
  }
  return response()->json(['message' => 'fail'], 400);
})->middleware('auth:admin');
Route::get('/test', function () {
  setcookie('name', 'jota');
  return response()->json(['message' => 'success']);
});
Route::get('/session/delete', function(Request $request) {
  $request->session()->flush();
});

// user
Route::prefix('/user')->group(function () {
  Route::get('/', [UserController::class, 'getUser']);
  Route::post('/login', [UserController::class, 'login'])->name('login');
  // user認証済み
  Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/schedule', [UserController::class, 'getSchedule']);
    Route::post('/schedule', [UserController::class, 'addSchedule']);
    Route::get('/history', [UserController::class, 'getHistory']);
    Route::get('/history/time', [UserController::class, 'getHistoryTime']);
    Route::post('/absence', [UserController::class, 'addAbsence']);
    Route::post('/clockin', [UserController::class, 'clockIn']);
    Route::post('/clockout', [UserController::class, 'clockOut']);
    Route::get('/payslip', [UserController::class, 'getPayslip']);
  });
});

// admin
Route::prefix('/admin')->group(function() {
  Route::get('/', [AdminController::class, 'getAdmin']);
  Route::post('/login', [AdminController::class, 'login']);
  // 認証済みグループ
  Route::middleware(['auth:admin'])->group(function () {
    Route::post('/logout', [AdminController::class, 'logout']);
    // user
    Route::prefix('/user')->group(function () {
      Route::get('/', [AdminController::class, 'getUser']);
      Route::post('/', [AdminController::class, 'addUser']);
      Route::put('/', [AdminController::class, 'updateUser']);
      Route::delete('/', [AdminController::class, 'deleteUser']);
      Route::get('/salary', [AdminController::class, 'getSalary']);
      Route::post('/salary', [AdminController::class, 'addSalary']);
      Route::get('/delete', [AdminController::class, 'getDeleteUser']);
      Route::post('/delete', [AdminController::class, 'deleteUser']);
      Route::put('/delete', [AdminController::class, 'restoreDeleteUser']);
      Route::delete('/delete', [AdminController::class, 'forceDeleteUser']);
    });
    Route::get('/shift', [AdminController::class, 'getShift']);
    Route::get('/shift/{id}', [AdminController::class, 'getShiftId']);
    Route::get('/schedule', [AdminController::class, 'getSchedule']);
    Route::post('/schedule', [AdminController::class, 'addSchedule']);
    Route::get('/absence', [AdminController::class, 'getAbsence']);
    Route::put('/absence', [AdminController::class, 'putAbsence']);
    Route::get('/history', [AdminController::class, 'getHistory']);
    Route::get('/option', [AdminController::class, 'getOption']);
    Route::put('/option', [AdminController::class, 'putOption']);
  });
});

// 認証済みアカウント
Route::middleware(['auth:sanctum,admin'])->group(function () {
  Route::get('schedule', [AuthenticatedController::class, 'getSchedule']);
});