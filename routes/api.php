<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarControlController;
use App\Http\Controllers\DriverMonitoringController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class,'logout'])->middleware('auth:api');// look for auth.php and make api guard
Route::post('generate-otp', [AuthController::class, 'generateOtp']);
Route::post('validate-otp', [AuthController::class, 'validateOtp']);
Route::post('/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

// Add this named route for password reset
Route::get('/password/reset', function ($token) {
    // This can redirect to a frontend reset page if needed.
})->name('password.reset');
// LogController
Route::get('/logs', [LogController::class, 'fetchLogs']);
Route::post('/logs', [LogController::class, 'addLog']);

// CarControlController
Route::post('/speed-control', [CarControlController::class, 'issueWarning']);

// ViolationController
Route::get('/violations', [ViolationController::class, 'list']);
Route::post('/violations/evidence', [ViolationController::class, 'uploadEvidence']);

// EmergencyController
Route::post('/emergency', [EmergencyController::class, 'requestOverride']);
Route::post('/emergency/evidence', [EmergencyController::class, 'uploadEvidence']);

// DriverMonitoringController
Route::get('/monitor', [DriverMonitoringController::class, 'getMonitoringData']);

// NotificationController
Route::get('/notifications', [NotificationController::class, 'list']);
Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);

// ProfileController
Route::get('/profile', [ProfileController::class, 'show']);
Route::put('/profile', [ProfileController::class, 'update']);

