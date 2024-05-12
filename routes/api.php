<?php

use App\Http\Controllers\ChuyenXeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\KhachHangAuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\NhanVienAuthController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\TuyenXeController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\NhaXeController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\VeXeController;



Route::apiResource('xe', XeController::class);
Route::apiResource('nha-xe', NhaXeController::class);
Route::apiResource('tuyen-xe', TuyenXeController::class);
Route::apiResource('chuyen-xe', ChuyenXeController::class);
Route::apiResource('khuyen-mai', KhuyenMaiController::class);


Route::post('/khach-hang/quen-mat-khau', [ForgotPasswordController::class, 'sendOTPForgotPassword']);
Route::post('/khach-hang/quen-mat-khau/doi-mat-khau', [ForgotPasswordController::class, 'changePasswordForgotPassword']);
Route::get('khach-hang/tra-cuu-ve', [VeXeController::class, 'getVeXe']);
Route::post('/khach-hang/dang-ky', [KhachHangAuthController::class, 'register']);
Route::post('/khach-hang/xac-thuc-email', [KhachHangAuthController::class, 'confirmEmail']);
Route::post('/khach-hang/gui-lai-ma-xac-thuc-email', [KhachHangAuthController::class, 'sendBackConfirmEmail']);
Route::post('/khach-hang/dang-nhap', [KhachHangAuthController::class, 'login']);
Route::get('/khach-hang/thanh-toan', [ThanhToanController::class, 'get']);

Route::group([
    'middleware' => ["auth:khach_hang_api"],
    // 'middleware' => ["cookie", "auth:khach_hang_api"],
    'prefix' => 'khach-hang'
], function ($router) {
    Route::post('thanh-toan', [ThanhToanController::class, 'post']);
    Route::post('doi-mat-khau', [KhachHangAuthController::class, 'changePassword']);
    Route::get('dang-xuat', [KhachHangAuthController::class, 'logout']);
    Route::get('thong-tin-ca-nhan', [KhachHangAuthController::class, 'me']);
});

// Nhân viên
Route::get('chuyen-xe-cung-tuyen', [ChuyenXeController::class, 'getChuyenXeWithTuyenXe']);
Route::get('nhan-vien/tra-cuu-ve/{id}', [VeXeController::class, 'getVeXeById']);
Route::post('/nhan-vien', [NhanVienController::class, 'store']);
Route::post('/nhan-vien/dang-ky', [NhanVienAuthController::class, 'register']);
Route::post('/nhan-vien/xac-thuc-email', [NhanVienAuthController::class, 'confirmEmail']);
Route::post('/nhan-vien/gui-lai-ma-xac-thuc-email', [NhanVienAuthController::class, 'sendBackConfirmEmail']);
Route::post('/nhan-vien/dang-nhap', [NhanVienAuthController::class, 'login']);
Route::get('/nhan-vien/thanh-toan', [ThanhToanController::class, 'get']);

Route::group([
    'middleware' => ["auth:nhan_vien_api"],
    // 'middleware' => ["cookie", "auth:khach_hang_api"],
    'prefix' => 'nhan-vien'
], function ($router) {
    Route::post('doi-mat-khau', [NhanVienAuthController::class, 'changePassword']);
    Route::get('dang-xuat', [NhanVienAuthController::class, 'logout']);
    Route::get('thong-tin-ca-nhan', [NhanVienAuthController::class, 'me']);
    Route::put('doi-ve/{id}', [VeXeController::class, 'changeVeXe']);
    Route::delete('huy-ve/{id}', [VeXeController::class, 'destroy']);
});

Route::get('nhan-vien/{role}', [NhanVienController::class, 'getAllNhanVienByRole']);