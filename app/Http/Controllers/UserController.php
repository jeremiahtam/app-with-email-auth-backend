<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\NotificationSettingsController;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $fields = $request->validate([
            'first_name' => 'required|string|max:60|min:1',
            'last_name' => 'required|string|max:60',
            'email' => 'required|string|email|unique:users,email|max:100',
            'phone_number' => 'required|string|unique:users|max:15',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password|min:8',
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'phone_number' => $fields['phone_number'],
            'password' => bcrypt($fields['password']),
            'removed' => 0,
        ]);

        if (!$user) {
            $response = [
                'success' => false,
                'message' => 'Registration could not be completed',
            ];
        }

        $response = [
            'success' => true,
            'message' => 'Your registration was successful.',
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|email|string|max:100',
            'password' => 'required|string|min:8',
        ]);

        //check email
        $user = User::where('email', $fields['email'])->first();

        //check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'success' => false,
                "message" => "Your username or password is incorrect.",
                "errors" => [
                    "email" => "Your username or password is incorrect.",
                    "password" => "Your username or password is incorrect.",
                ],
            ], 401);
        }

        $token = $user->createToken('notnormal')->plainTextToken;
        //--------------------------------------------
        //  Get userInfo and set notification settings 
        //--------------------------------------------
        $userInfo = $this->getUserInfoByEmail(new Request(['email' => $fields['email']]));
        $userId = $userInfo['data']->id;

        //-----------------------------------------
        //  create notification settings
        //-----------------------------------------
        $notificationSettings = new NotificationSettingsController;
        //Check if user notification exists
        $notificationSettingsNumRows = $notificationSettings->showUserNumRows($userId);

        if ($notificationSettingsNumRows == 0) {
            $userNotificationData = $notificationSettings->create($userId);
            if ($userNotificationData['success'] == false) {
                return [
                    'success' => false,
                    'message' => 'Could not create notification setings for user',
                ];
            }
        }
        //Get live status of user
        $locationHistoryController = new LocationHistoryController;
        $liveLocationStatusRequest = $locationHistoryController->liveStatus(new Request(['user_id' => $userId]));


        if ($liveLocationStatusRequest['success'] == true) {
            $liveLocationStatus = $liveLocationStatusRequest['data']['live'];
        } else {
            $liveLocationStatus = null;
        }

        //-----------------------------------------
        //  create security message for user
        //-----------------------------------------
        $securityMessage = new SecurityMessageController;
        //Check if user security message exists
        $securityMessageNumRows = $securityMessage->showUserNumRows($userId);

        if ($securityMessageNumRows == 0) {
            $userSecurityMessageData = $securityMessage->create($userId);
            if ($userSecurityMessageData['success'] == false) {
                return [
                    'success' => false,
                    'message' => 'Could not create default security message for user',
                ];
            }
        }

        $response = [
            'success' => true,
            'message' => 'Successfully logged in',
            'notification_settings' => isset($userNotificationData) ? $userNotificationData : 'already exists',
            'security_message' => isset($userSecurityMessageData) ? $userSecurityMessageData : 'already exists',
            'data' => [
                'token' => $token,
                'email' => $fields['email'],
                'userId' => $userId,
                'liveLocationStatus' => $liveLocationStatus,
            ]
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $deleteTokens = $request->user()->tokens()->delete();

        if (!$deleteTokens) {
            return response([
                "success" => false,
                "message" => "Unnable to logout",
                "errors" => [
                    "token" => $deleteTokens
                ]
            ]);
        }

        return response([
            "success" => true,
            "message" => "Successfully logged out"
        ], 201);
    }

    public function getUserInfoByEmail(Request $request)
    {
        $email = $request->email;

        $userInfo = User::where('email', $email)
            ->where('removed', 0)->first();
        if (!$userInfo) {
            return [
                'success' => false,
                'message' =>  'User not found',
            ];
        }
        return [
            'success' => true,
            'message' => 'User exists',
            'data' =>  $userInfo,
        ];
    }

    public function getUserInfoByUserId($id)
    {
        $userInfo = User::where('id', $id)
            ->where('removed', 0)->first();
        if (!$userInfo) {
            return [
                'success' => false,
                'message' =>  'User not found',
            ];
        }
        return [
            'success' => true,
            'message' => 'User exists',
            'data' =>  $userInfo,
        ];
    }
}
