<?php

use App\Http\Controllers\TripController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusStationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;

// [CUSTOMER]

// Forgot password
Route::post('/customer/forgot-password', [ForgotPasswordController::class, 'sendOTPForgotPassword']);
Route::put('/customer/forgot-password/change-password', [ForgotPasswordController::class, 'changePasswordForgotPassword']);

// Send OTP to confirm email address
Route::post('/customer/confirm-email', [CustomerController::class, 'confirmEmail']);
Route::post('/customer/resend-confirm-email', [CustomerController::class, 'resendComfirmEmail']);

// Look up tiket
Route::get('/customer/lookup-ticket', [TicketController::class, 'lookupTicket']);

// Register customer account
Route::post('/customer/register', [CustomerController::class, 'register']);

// Login customer account
Route::post('/customer/login', [CustomerController::class, 'login']);

// Payment for tickets
Route::get('/customer/payment', [PaymentController::class, 'get']);

Route::group([
    'middleware' => ["auth:customer_api"],
    'prefix' => 'customer'
], function ($router) {
    // Change avater
    Route::put('change-avatar', [CustomerController::class, 'changeAvatar']);

    // Update account information
    Route::put('update-my-account', [CustomerController::class, 'updateMyAccount']);

    // Payment for tickets
    Route::post('payment', [PaymentController::class, 'post']);

    // Change password
    Route::put('change-password', [CustomerController::class, 'changePassword']);

    // Logout customer account
    Route::get('logout', [CustomerController::class, 'logout']);

    // Get information of customer account
    Route::get('me', [CustomerController::class, 'me']);
});

// [EMPLOYEE]

// Get trip
Route::get('employee/trip', [TripController::class, 'index']);

// Get trip by id
Route::get('employee/trip/{id}', [TripController::class, 'show']);

// Get bus
Route::get('employee/bus', [BusController::class, 'index']);

// Get bus by id
Route::get('employee/bus/{id}', [BusController::class, 'show']);

// Get route
Route::get('employee/route', [RouteController::class, 'index']);

// Get route by id
Route::get('employee/route/{id}', [RouteController::class, 'show']);

// Get bus station
Route::get('employee/bus-station', [BusStationController::class, 'index']);

// Get bus station by id
Route::get('employee/bus-station/{id}', [BusStationController::class, 'show']);

// Get employee with role
Route::get('employee/get-employees-by-role/{role}', [EmployeeController::class, 'getAllEmployeesByRole']);

// Get trip same route
Route::get('employee/get-trip-same-route', [TripController::class, 'getTripSameRoute']);

// Login employee account
Route::post('employee/login', [EmployeeController::class, 'login']);

Route::group([
    'middleware' => ["auth:employee_api"],
    'prefix' => 'employee'
], function ($router) {
    // bus api
    Route::apiResource('bus', BusController::class, ['except' => ['index', 'show']]);

    // bus station api
    Route::apiResource('bus-station', BusStationController::class, ['except' => ['index', 'show']]);

    // route api
    Route::apiResource('route', RouteController::class, ['except' => ['index', 'show']]);

    // trip api
    Route::apiResource('trip', TripController::class, ['except' => ['index', 'show']]);

    // employee api
    Route::apiResource('employee', EmployeeController::class);

    // employee api
    Route::apiResource('customer', CustomerController::class);

    // change ticket
    Route::put('chance-ticket/{id}', [TicketController::class, 'changeTicket']);

    // cancel ticket
    Route::delete('cancel-ticket/{id}', [TicketController::class, 'destroy']);

    // change avatar
    Route::put('change-avatar', [EmployeeController::class, 'changeAvatar']);

    // update employee account information
    Route::put('update-my-account', [EmployeeController::class, 'updateMyAccount']);

    // change password
    Route::put('change-password', [EmployeeController::class, 'changePassword']);

    // logout employee account
    Route::get('logout', [EmployeeController::class, 'logout']);

    // get information of employee account
    Route::get('me', [EmployeeController::class, 'me']);
});