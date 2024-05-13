<?php

use App\Http\Controllers\TripController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusStationController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\VeXeController;

Route::apiResource('bus', BusController::class);
Route::apiResource('bus-station', BusStationController::class);
Route::apiResource('route', RouteController::class);
Route::apiResource('trip', TripController::class);
Route::apiResource('khuyen-mai', KhuyenMaiController::class);


Route::post('/customer/quen-mat-khau', [ForgotPasswordController::class, 'sendOTPForgotPassword']);
Route::post('/customer/quen-mat-khau/doi-mat-khau', [ForgotPasswordController::class, 'changePasswordForgotPassword']);
Route::get('/customer/tra-cuu-ve', [VeXeController::class, 'getVeXe']);
Route::post('/customer/register', [CustomerController::class, 'register']);
Route::post('/customer/confirm-email', [CustomerController::class, 'confirmEmail']);
Route::post('/customer/gui-lai-ma-xac-thuc-email', [CustomerController::class, 'sendBackConfirmEmail']);
Route::post('/customer/login', [CustomerController::class, 'login']);
Route::get('/customer/thanh-toan', [ThanhToanController::class, 'get']);

Route::group([
    'middleware' => ["auth:customer_api"],
    // 'middleware' => ["cookie", "auth:khach_hang_api"],
    'prefix' => 'customer'
], function ($router) {
    Route::post('thanh-toan', [ThanhToanController::class, 'post']);
    Route::post('doi-mat-khau', [CustomerController::class, 'changePassword']);
    Route::get('dang-xuat', [CustomerController::class, 'logout']);
    Route::get('thong-tin-ca-nhan', [CustomerController::class, 'me']);
});

// Nhân viên
Route::get('chuyen-xe-cung-tuyen', [TripController::class, 'getChuyenXeWithTuyenXe']);
Route::get('employee/tra-cuu-ve/{id}', [VeXeController::class, 'getVeXeById']);
Route::post('/employee', [EmployeeController::class, 'store']);
Route::post('/employee/dang-ky', [EmployeeController::class, 'register']);
Route::post('/employee/xac-thuc-email', [EmployeeController::class, 'confirmEmail']);
Route::post('/employee/gui-lai-ma-xac-thuc-email', [EmployeeController::class, 'sendBackConfirmEmail']);
Route::post('/employee/dang-nhap', [EmployeeController::class, 'login']);
Route::get('/employee/thanh-toan', [ThanhToanController::class, 'get']);

Route::group([
    'middleware' => ["auth:employee_api"],
    // 'middleware' => ["cookie", "auth:khach_hang_api"],
    'prefix' => 'employee'
], function ($router) {
    Route::post('doi-mat-khau', [EmployeeController::class, 'changePassword']);
    Route::get('dang-xuat', [EmployeeController::class, 'logout']);
    Route::get('thong-tin-ca-nhan', [EmployeeController::class, 'me']);
    Route::put('doi-ve/{id}', [VeXeController::class, 'changeVeXe']);
    Route::delete('huy-ve/{id}', [VeXeController::class, 'destroy']);
});

Route::get('employee/{role}', [EmployeeController::class, 'getAllEmployeeByRole']);
