<?php

use App\Http\Controllers\AlertMessageController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\LocationHistoryController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\SecurityMessageController;
use App\Http\Controllers\UserController;
use App\Models\LocationHistory;
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
//public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
/*Reset passord */
Route::post('/password/forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/password/confirm-password-reset-token', [NewPasswordController::class, 'confirmPasswordResetToken']);
Route::post('/password/reset-password', [NewPasswordController::class, 'resetPassword']);

// Route::post('/user/reset-password-token', [UserController::class,'resetPasswordToken']);

//secured routes
Route::group(["middleware" => ['auth:sanctum']], function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/check-user-info', [UserController::class, 'getUserInfoByEmail']);
});
