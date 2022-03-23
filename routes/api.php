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

    Route::get('/notification-settings/{id}', [NotificationSettingsController::class, 'show']);
    Route::put('/notification-settings/update/{id}', [NotificationSettingsController::class, 'update']);

    Route::get('/security-message/{id}', [SecurityMessageController::class, 'show']);
    Route::put('/security-message/update/{id}', [SecurityMessageController::class, 'update']);

    Route::post('/alert-message/store', [AlertMessageController::class, 'store']);
    Route::get('/alert-message/{id}', [AlertMessageController::class, 'show']);
    Route::get('/alert-message/sent-messages/{userId}', [AlertMessageController::class, 'showSentAlertMessages']);
    Route::get('/alert-message/received-messages/{userId}', [AlertMessageController::class, 'showReceivedAlertMessages']);

    Route::post('/contacts', [ContactsController::class, 'index']);
    Route::get('/contacts/{id}', [ContactsController::class, 'show']);
    Route::post('/contacts/store', [ContactsController::class, 'store']);
    Route::put('/contacts/update/{id}', [ContactsController::class, 'update']);
    Route::put('/contacts/delete-contact/{id}', [ContactsController::class, 'destroy']);

    Route::post('/location-history', [LocationHistoryController::class, 'index']);
    Route::post('/location-history/create', [LocationHistoryController::class, 'create']);
    Route::post('/location-history/live-status', [LocationHistoryController::class, 'liveStatus']);
    Route::post('/location-history/live-location-switch', [LocationHistoryController::class, 'liveLocationSwitch']);

    Route::get('/location-history/{id}', [LocationHistoryController::class, 'show']);
});
