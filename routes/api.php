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
use App\Http\Controllers\TicketController;
use App\Models\Employee;

// Customer
Route::post('/customer/forgot-password', [ForgotPasswordController::class, 'sendOTPForgotPassword']);
Route::put('/customer/forgot-password/change-password', [ForgotPasswordController::class, 'changePasswordForgotPassword']);

Route::post('/customer/confirm-email', [CustomerController::class, 'confirmEmail']);
Route::post('/customer/resend-confirm-email', [CustomerController::class, 'resendComfirmEmail']);

Route::get('/customer/lookup-ticket', [TicketController::class, 'lookupTicket']);
Route::post('/customer/register', [CustomerController::class, 'register']);
Route::post('/customer/login', [CustomerController::class, 'login']);
Route::get('/customer/payment', [ThanhToanController::class, 'get']);

Route::group([
    'middleware' => ["auth:customer_api"],
    'prefix' => 'customer'
], function ($router) {
    Route::put('change-avatar', [CustomerController::class, 'changeAvatar']);
    Route::put('update-my-account', [CustomerController::class, 'updateMyAccount']);
    Route::post('payment', [ThanhToanController::class, 'post']);
    Route::put('change-password', [CustomerController::class, 'changePassword']);
    Route::get('logout', [CustomerController::class, 'logout']);
    Route::get('me', [CustomerController::class, 'me']);
});

// Employee
Route::get('employee/trip', [TripController::class, 'index']);
Route::get('employee/bus', [BusController::class, 'index']);
Route::get('employee/bus-station', [BusStationController::class, 'index']);
Route::get('employee/route', [RouteController::class, 'index']);
Route::get('employee/trip/{id}', [TripController::class, 'show']);
Route::get('employee/bus/{id}', [BusController::class, 'show']);
Route::get('employee/bus-station/{id}', [BusStationController::class, 'show']);
Route::get('employee/route/{id}', [RouteController::class, 'show']);
Route::get('employee/get-employees-by-role/{role}', [EmployeeController::class, 'getAllEmployeesByRole']);

Route::get('employee/get-trip-same-route', [TripController::class, 'getTripSameRoute']);

Route::post('employee/login', [EmployeeController::class, 'login']);

Route::group([
    'middleware' => ["auth:employee_api"],
    'prefix' => 'employee'
], function ($router) {
    Route::apiResource('bus', BusController::class, ['except' => ['index', 'show']]);
    Route::apiResource('bus-station', BusStationController::class, ['except' => ['index', 'show']]);
    Route::apiResource('route', RouteController::class, ['except' => ['index', 'show']]);
    Route::apiResource('trip', TripController::class, ['except' => ['index', 'show']]);
    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('customer', CustomerController::class);

    Route::put('chance-ticket/{id}', [TicketController::class, 'changeTicket']);
    Route::delete('cancel-ticket/{id}', [TicketController::class, 'destroy']);

    Route::put('change-avatar', [EmployeeController::class, 'changeAvatar']);
    Route::put('update-my-account', [EmployeeController::class, 'updateMyAccount']);
    Route::put('change-password', [EmployeeController::class, 'changePassword']);
    Route::get('logout', [EmployeeController::class, 'logout']);
    Route::get('me', [EmployeeController::class, 'me']);
});
