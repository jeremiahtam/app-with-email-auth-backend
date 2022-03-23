<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordToken;
use App\Models\PasswordReset;
use App\Exceptions\Handler;
use App\Listeners\LogSendingMessage;

class NewPasswordController extends Controller
{

    public function forgotPassword(Request $request)
    {
        $fields = $request->validate([
            'email' => 'string|required|email|max:100',
        ]);

        //check email
        $user = User::where('email', $fields['email'])->first();

        //check password        
        if (!$user) {
            return response(
                [
                    'success' => false,
                    'message' => "An error occured",
                    'errors' => [
                        'email' => 'That email cannot be found.'
                    ]
                ],
                401
            );
        }

        $checkExistingResetRequest =  PasswordReset::where([
            ['email', $fields['email']]
        ]);

        //delete if token already exists
        if ($checkExistingResetRequest->exists()) {
            $checkExistingResetRequest->delete();
        }

        $token = rand(1000000, 9999999);

        Mail::to($fields['email'])
            ->send(new ResetPasswordToken($token));

        if (count(Mail::failures()) > 0) {
            return response(
                [
                    'success' => false,
                    'message' => "Email could not be sent to this address. Try again later.",
                ]
            );
        }

        $checkExistingResetRequest =  PasswordReset::insert(
            [
                'email' => $fields['email'],
                'token' => $token
            ]
        );
        if (!$checkExistingResetRequest) {
            return response(
                [
                    'success' => false,
                    'message' => "Network error",
                ]
            );
        }

        return response(
            [
                'success' => true,
                'message' => "A reset token has been sent to your email address",
            ]
        );
    }

    public function confirmPasswordResetToken(Request $request)
    {
        $fields = $request->validate([
            'email' => 'string|required|email|max:100',
            'token' => 'digits:7|required',
        ]);


        $checkForToken =  PasswordReset::where([
            ['email', $fields['email']],
            ['token', $fields['token']],
        ]);

        if (!$checkForToken) {
            return [
                'success' => false,
                'message' => "Network error. Try again later",
            ];
        }

        //delete if token already exists
        if ($checkForToken->exists()) {
            return [
                'success' => true,
                'message' => "Token exists",
            ];
        } else {
            return [
                'success' => false,
                'message' => "Token does not exist",
                'errors' => [
                    'token' => 'Token does not exist'
                ]
            ];
        }
    }
    public function resetPassword(Request $request)
    {
        $fields = $request->validate([
            'email' => 'string|required|email|max:100',
            'token' => 'digits:7|required',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password|min:8',
        ]);

        $confirmToken = $this->confirmPasswordResetToken(new Request([
            'email' => $fields['email'],
            'token' => $fields['token'],
        ]));

        if ($confirmToken['success'] == false) {
            return $confirmToken;
        }
        //update user password
        $resetPassword = User::where([
            ['email', $fields['email']],
        ])->update([
            'password' => Hash::make($fields['password'])
        ]);

        if (!$resetPassword) {
            response([
                'success' => false,
                'message' => 'Could not reset password'
            ]);
        }

        $deleteToken =  PasswordReset::where([
            'email'=> $fields['email'],
            'token'=> $fields['token'],
        ])->delete();

        if (!$deleteToken) {
            response([
                'success' => false,
                'message' => 'Network error try again later'
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Password Changed'
        ]);
    }
}
