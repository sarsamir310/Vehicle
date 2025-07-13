<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CarControlController;
use App\Http\Controllers\DriverMonitoringController;
use App\Http\Controllers\DrivingLicenseController;
use App\Http\Controllers\DrivingModeController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ViolationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// AuthController
Route::post('/register-step1', [AuthController::class, 'registerStep1']);
Route::post('/register-step2', [AuthController::class, 'registerStep2']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/generate-otp', [AuthController::class, 'generateOtp']);
Route::post('/validate-otp', [AuthController::class, 'validateOtp']);

// Protect routes with auth:sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('/send-otp', [PasswordResetController::class, 'sendOtp']);
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::get('/logs', [LogController::class, 'fetchLogs']);
Route::post('/logs', [LogController::class, 'addLog']);

// CarControlController
Route::post('/speed-control', [CarControlController::class, 'issueWarning']);

//Violation
//Route::get('/check-violations', [EmergencyController::class, 'checkAndLogViolations']);


// EmergencyController
Route::post('/emergency-request', [EmergencyController::class, 'createEmergencyRequest']);
Route::post('/upload-evidence', [EmergencyController::class, 'uploadEvidence']);
Route::get('/check-violations', [EmergencyController::class, 'checkForViolations']);

// DriverMonitoringController
Route::get('/monitor', [DriverMonitoringController::class, 'getMonitoringData']);

// NotificationController
/*Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});*/


// ProfileController
//Route::middleware('auth:api')->group(function () {
//Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/driving-mode/toggle', [DrivingModeController::class, 'toggleDrivingMode']);
    Route::post('/driving-mode/apps', [DrivingModeController::class, 'updateDistractingApps']);
    Route::get('/driving-mode', [DrivingModeController::class, 'getDrivingMode']);
});
//Route::post('/save-device-token', [NotificationController::class, 'saveToken']);
//Route::post('/send-notification', [NotificationController::class, 'sendPushNotification']);




