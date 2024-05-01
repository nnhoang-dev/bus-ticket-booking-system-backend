<?php

use App\Http\Controllers\ChuyenXeController;
use App\Http\Controllers\DatVeController;
use App\Http\Controllers\HoaDonController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\TuyenXeController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\NhaXeController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\VeTamController;
use App\Http\Controllers\VeXeController;
use App\Http\Middleware\KhachHangMiddleware;
use App\Http\Middleware\NhanVienMiddleware;
use App\Http\Middleware\QuanLyAndVanHangMiddleware;

Route::apiResource('nhan-vien', NhanVienController::class);
Route::apiResource('khach-hang', KhachHangController::class);
Route::apiResource('xe', XeController::class);
Route::apiResource('nha-xe', NhaXeController::class);
Route::apiResource('tuyen-xe', TuyenXeController::class);
Route::apiResource('chuyen-xe', ChuyenXeController::class);
Route::apiResource('khuyen-mai', KhuyenMaiController::class);
Route::apiResource('hoa-don', HoaDonController::class);
// Route::apiResource('ve-tam', VeTamController::class);

Route::post('thanh-toan', [ThanhToanController::class, 'post']);
Route::get('thanh-toan', [ThanhToanController::class, 'get']);

Route::get('tra-cuu-ve/{id}', [VeXeController::class, 'getVeXeById']);
Route::put('doi-ve/{id}', [VeXeController::class, 'changeVeXe']);
Route::put('huy-ve/{id}', [VeXeController::class, 'destroy']);
// Route::apiResource('nhan_vien', NhanVienController::class)
//     ->middleware(NhanVienMiddleware::class);

// Route::apiResource('khach_hang', KhachHangController::class)
//     ->middleware(KhachHangMiddleware::class);

// Route::apiResource('xe', XeController::class)
//     ->middleware(QuanLyAndVanHangMiddleware::class);

// Route::apiResource('nha_xe', NhaXeController::class)
//     ->middleware(QuanLyAndVanHangMiddleware::class);


// Route::apiResource('tuyen_xe', TuyenXeController::class)
//     ->middleware(QuanLyAndVanHangMiddleware::class);

// Route::apiResource('chuyen_xe', ChuyenXeController::class)
//     ->middleware(QuanLyAndVanHangMiddleware::class);

// Route::apiResource('khuyen_mai', KhuyenMaiController::class)
//     ->middleware(QuanLyAndVanHangMiddleware::class);
