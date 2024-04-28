<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\TuyenXeController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\NhaXeController;

use App\Http\Middleware\KhachHangMiddleware;
use App\Http\Middleware\NhanVienMiddleware;
use App\Http\Middleware\NhaXeMiddleware;
use App\Http\Middleware\XeMiddlerware;

// Route::apiResource('nhan_vien', NhanVienController::class);
// Route::apiResource('khach_hang', KhachHangController::class);
// Route::apiResource('xe', XeController::class);
Route::apiResource('nhan_vien', NhanVienController::class)
    ->middleware(NhanVienMiddleware::class);

Route::apiResource('khach_hang', KhachHangController::class)
    ->middleware(KhachHangMiddleware::class);

Route::apiResource('xe', XeController::class)
    ->middleware(XeMiddlerware::class);

Route::apiResource('nha_xe', NhaXeController::class)
    ->middleware(NhaXeMiddleware::class);

Route::apiResource('tuyen_xe', TuyenXeController::class);
    // ->middleware(XeMiddlerware::class);
