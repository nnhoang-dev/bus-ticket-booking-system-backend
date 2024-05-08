<?php

use App\Http\Controllers\ChuyenXeController;
use App\Http\Controllers\DatVeController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KhachHangAuthController;
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

// Route::apiResource('nhan-vien', NhanVienController::class);
// Route::apiResource('khach-hang', KhachHangController::class);
Route::apiResource('xe', XeController::class);
Route::apiResource('nha-xe', NhaXeController::class);
Route::apiResource('tuyen-xe', TuyenXeController::class);
Route::apiResource('chuyen-xe', ChuyenXeController::class);
Route::apiResource('khuyen-mai', KhuyenMaiController::class);
// Route::apiResource('hoa-don', HoaDonController::class);
// Route::apiResource('ve-tam', VeTamController::class);

Route::get('khach-hang/tra-cuu-ve', [VeXeController::class, 'getVeXeById']);
Route::put('doi-ve/{id}', [VeXeController::class, 'changeVeXe']);
Route::put('huy-ve/{id}', [VeXeController::class, 'destroy']);


// Route::get('check-result-ticket', [VeXeController::class, 'checkTicket']);

// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'employ
// ], function ($router) {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/change-password', [AuthController::class, 'changePassword']);
//     // ... other routes for employees
// });

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

Route::post('/get-cookie', function () {
    $minutes = 60;
    $path = "/";
    $domain = "127.0.0.1";
    return response()->json(["hehe" => "hehe"])->cookie('my_cookie', 'heh', 1, $path, $domain, false, false);
});


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