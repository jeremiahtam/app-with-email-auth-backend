<?php

namespace App\Http\Controllers;

use App\Models\SecurityMessage;
use Illuminate\Http\Request;

class SecurityMessageController extends Controller
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
        $createSecurityMessage = SecurityMessage::create([
            'user_id' => $userId,
            'message' => 'Default message',
            'removed' => 0,
        ]);

        if ($createSecurityMessage) {
            return [
                'userId' => $userId,
                'success' => true,
                'message' => 'Security message successfully initialized',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Security message could not be established',
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
        $securityMessage = SecurityMessage::where('id', $id)
            ->where('removed', 0)
            ->first();
        return response($securityMessage);
    }

    /**
     * Display user security message info by userId.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function showUserSecurityMessage($userId)
    {
        $securityMessage = SecurityMessage::where('user_id', $userId)
            ->where('removed', 0)
            ->first();
        return [
            'success' => true,
            'message' => 'User exists',
            'data' =>  $securityMessage,
        ];
    }

    /**
     * Display the specified resource rows.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserNumRows($userId)
    {
        $securityMessage = SecurityMessage::where('user_id', $userId)
            ->where('removed', 0)
            ->get()->count();
        return $securityMessage;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            'message' => 'required|string',
        ]);


        $updateNotificationSettings = SecurityMessage::where('id', $id)
            ->where('removed', 0)
            ->update([
                'message' => $fields['message'],
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
