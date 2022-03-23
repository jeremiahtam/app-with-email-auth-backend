<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\ContactsPermissions;
use App\Models\AlertMessages;
use Illuminate\Http\Request;

class AlertMessageController extends Controller
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
    public function create()
    {
        //
    }

    //send email
    public function sendEmailAlert($email)
    {
        //get contact permission to receive email alerts
        $value = 1;
        return $value;
    }

    //send text message
    public function sendTextMessageAlert($phoneNumber)
    {
        $value = 1;
        return $value;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'sender' => 'required|integer',
            'location_name' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        //get sender message
        $securityMessageController = new SecurityMessageController;
        $securityMessageInfo = $securityMessageController->showUserSecurityMessage($request->sender);
        $message = $securityMessageInfo['data']->message;

        //get all contacts of the sender
        $contactsController = new ContactsController;

        $content = new Request([
            'user_id' => $fields['sender']
        ]);
        $contacts = $contactsController->index($content);

        $contacts = $contacts['data'];

        $data = [];

        foreach ($contacts as $contact) {
            $phoneNumber = $contact['phone_number'];
            $email = $contact['email'];
            //send text
            $textSentStatus = $this->sendTextMessageAlert($phoneNumber);
            $emailSentStatus = $this->sendEmailAlert($email);

            //check if the receiver is reistered to the platform
            $userController = new UserController();

            $userInfo = $userController->getUserInfoByEmail(new Request([
                'email' => $email
            ]));

            $receiverId = null;
            if (isset($userInfo['data'])) {
                $receiverId = $userInfo['data']['id'];
            }

            $alertMessage = AlertMessages::create([
                'receiver' => $receiverId,
                'sender' => $fields['sender'],
                'message' => $message,
                'location_name' => $fields['location_name'],
                'latitude' => $fields['latitude'],
                'longitude' => $fields['longitude'],
                'email_sent' => $emailSentStatus,
                'text_message_sent' => $textSentStatus,
                'removed' => 0,
            ]);

            if (!$alertMessage) {
                $data[] = [
                    'message' => "Could not send alert message to $contact->last_name $contact->first_name",
                ];
            }

            $data[] = [
                'message' => "Alert message sent successfully to $contact->last_name $contact->first_name",
            ];
        }

        $response = [
            'success' => true,
            'message' => 'Successfully executed',
            'data' => $data
        ];

        return response($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userAlertMessage = AlertMessages::where('id', $id)
            ->where('removed', 0)
            ->first();
        return response($userAlertMessage);
    }
    /**
     * Display sent alert messages.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */

    public function showSentAlertMessages($userId)
    {
        $userSentAlertMessages = AlertMessages::where('sender', $userId)
            ->where('removed', 0)
            ->get();

        $userController = new UserController;
        $sentAlertMessages = [];

        foreach ($userSentAlertMessages as $message) {

            $user = $userController->getUserInfoByUserId($message['receiver']);
            $firstName = $user['data']->first_name;
            $lastName = $user['data']->last_name;

            $sentAlertMessages[] = [
                'receiver' => $message['receiver'],
                'receiver_name' => "$lastName $firstName",
                'message' => $message['message'],
                'location_name' => $message['location_name'],
                'latitude' => $message['latitude'],
                'longitude' => $message['longitude'],
            ];
        }

        $response = [
            'success' => false,
            'message' => 'Sent alert messages successfully retrieved',
            'data' => $sentAlertMessages,
        ];

        return response($response);
    }


    /**
     * Display received alert messages.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */

    public function showReceivedAlertMessages($userId)
    {
        $userReceivedAlertMessages = AlertMessages::where('receiver', $userId)
            ->where('removed', 0)
            ->get();

        $userController = new UserController;
        $receivedAlertMessages = [];

        foreach ($userReceivedAlertMessages as $message) {

            $user = $userController->getUserInfoByUserId($message['sender']);
            $firstName = $user['data']->first_name;
            $lastName = $user['data']->last_name;

            $receivedAlertMessages[] = [
                'sender' => $message['sender'],
                'sender_name' => "$lastName $firstName",
                'message' => $message['message'],
                'location_name' => $message['location_name'],
                'latitude' => $message['latitude'],
                'longitude' => $message['longitude'],
            ];
        }

        $response = [
            'success' => false,
            'message' => 'Received alert messages successfully retrieved',
            'data' => $receivedAlertMessages,
        ];

        return response($response);
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
        //
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
