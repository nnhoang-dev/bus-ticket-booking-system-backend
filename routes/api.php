<?php

use App\Http\Controllers\ChuyenXeController;
use App\Http\Controllers\HoaDonController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\TuyenXeController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\NhaXeController;


use App\Http\Middleware\KhachHangMiddleware;
use App\Http\Middleware\NhanVienMiddleware;
use App\Http\Middleware\QuanLyAndVanHangMiddleware;

// Route::apiResource('nhan_vien', NhanVienController::class);
// Route::apiResource('khach_hang', KhachHangController::class);
// Route::apiResource('xe', XeController::class);
Route::apiResource('nhan_vien', NhanVienController::class)
    ->middleware(NhanVienMiddleware::class);

Route::apiResource('khach_hang', KhachHangController::class)
    ->middleware(KhachHangMiddleware::class);

Route::apiResource('xe', XeController::class)
    ->middleware(QuanLyAndVanHangMiddleware::class);

Route::apiResource('nha_xe', NhaXeController::class)
    ->middleware(QuanLyAndVanHangMiddleware::class);


Route::apiResource('tuyen_xe', TuyenXeController::class)
    ->middleware(QuanLyAndVanHangMiddleware::class);

Route::apiResource('chuyen_xe', ChuyenXeController::class)
    ->middleware(QuanLyAndVanHangMiddleware::class);

Route::apiResource('khuyen_mai', KhuyenMaiController::class)
    ->middleware(QuanLyAndVanHangMiddleware::class);

Route::apiResource('hoa_don', HoaDonController::class);