<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\NotificationSettings;
use App\Http\Controllers\UserController;

class NotificationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($userId)
    {
        $createNotification = NotificationSettings::create([
            'user_id' => $userId,
            'push_notification' => 0,
            'lock_screen_interface' => 0,
            'self_tracking' => 0,
            'removed' => 0,
        ]);

        if ($createNotification) {
            return [
                'userId' => $userId,
                'success' => true,
                'message' => 'Notification settings successflly initialised',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Notification settings could not be established',
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userNotification = NotificationSettings::where('id', $id)
            ->where('removed', 0)
            ->first();
        return response($userNotification);
    }

    public function showViaUserId($userId)
    {
        $userNotification = NotificationSettings::where('user_id', $userId)
            ->where('removed', 0)
            ->first();
        return response($userNotification);
    }

    /**
     * Display the specified resource rows.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserNumRows($userId)
    {
        $userNotification = NotificationSettings::where('user_id', $userId)
            ->where('removed', 0)
            ->get()->count();
        return $userNotification;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $fields = $request->validate([
            'push_notification' => 'required|boolean',
            'lock_screen_interface' => 'required|boolean',
            'self_tracking' => 'required|boolean',
        ]);


        $updateNotificationSettings = NotificationSettings::where('id', $id)
            ->where('removed', 0)
            ->update([
                'push_notification' => $fields['push_notification'],
                'lock_screen_interface' => $fields['lock_screen_interface'],
                'self_tracking' => $fields['self_tracking'],
            ]);

        if ($updateNotificationSettings) {
            return [
                'success' => true,
                'message' => 'Successflly updated',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Could not be updated',
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
