<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacts;
use App\Models\ContactsPermissions;

class ContactsController extends Controller
{
    /**
     * List out all the contacts belonging to a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fields = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $contactInfo = Contacts::where('user_id', $fields['user_id'])
            ->where('removed', 0)
            ->with('contactsPermissions')->get();

        if (!$contactInfo) {
            return [
                'success' => false,
                'message' => 'Could not find contact',
                'data' => $contactInfo,
            ];
        }
        return [
            'success' => true,
            'message' => 'Contact successfully retrieved',
            'data' => $contactInfo,
        ];
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'user_id' => 'required|integer',
            'first_name' => 'required|string|max:60|min:1',
            'last_name' => 'string|max:60',
            'phone_number' => 'required|string|unique:contacts,phone_number|max:60',
            'email' => 'required|string|email|unique:contacts,email|max:100',
            'last_seen' => 'required|boolean',
            'live_location' => 'required|boolean',
            'location_history' => 'required|boolean',
        ]);

        //check number of contacts a user has
        $contactInfo = Contacts::where('user_id', $fields['user_id'])
            ->where('removed', 0)->get();
        if ($contactInfo->count() == 5) {
            $response = [
                'success' => false,
                'message' => 'You cannot have more than five (5) contacts',
            ];
        }

        //create new contact
        $newContact = Contacts::create([
            'user_id' => $fields['user_id'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'phone_number' => $fields['phone_number'],
            'removed' => 0,
        ]);

        //Get contact info 
        $contactInfo = $this->getContactInfoByPhoneNumber($fields['user_id'], $fields['phone_number']);
        $contactId = $contactInfo['data']->id;

        //Create new contact permissions
        $contactsPermission = new ContactsPermissionsController;
        $createContactPermissions = $contactsPermission->create($contactId, [
            'last_seen' => $fields['last_seen'],
            'live_location' => $fields['live_location'],
            'location_history' => $fields['location_history'],
        ]);

        if (!$newContact) {
            $response = [
                'success' => false,
                'permissions' => $createContactPermissions,
                'message' => 'Contact could not be created',
            ];
        }

        $response = [
            'success' => true,
            'permissions' => $createContactPermissions,
            'message' => 'Contact successfully created',
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
        $contactInfo = Contacts::where('removed', 0)
            ->with('contactsPermissions')->find($id);

        if (!$contactInfo) {
            return [
                'success' => false,
                'message' => 'Could not find contact',
                'data' => $contactInfo,
            ];
        }
        return [
            'success' => true,
            'message' => 'Contact successfully retrieved',
            'data' => $contactInfo,
        ];
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
            'first_name' => 'required|string|max:60|min:1',
            'last_name' => 'string|max:60',
            'phone_number' => 'required|string|unique:contacts,phone_number|max:60',
            'email' => 'required|string|email|unique:contacts,email|max:100',
            'last_seen' => 'required|boolean',
            'live_location' => 'required|boolean',
            'location_history' => 'required|boolean',
        ]);

        $updateContact = Contacts::where('id', $id)
            ->where('removed', 0)
            ->update([
                'first_name' => $fields['first_name'],
                'last_name' => $fields['last_name'],
                'email' => $fields['email'],
                'phone_number' => $fields['phone_number'],
            ]);

        //Edit contact permissions
        $contactsPermission = new ContactsPermissionsController;
        $updateContactPermissions = $contactsPermission->edit($id, [
            'last_seen' => $fields['last_seen'],
            'live_location' => $fields['live_location'],
            'location_history' => $fields['location_history'],
        ]);

        if (!$updateContact) {
            $response = [
                'success' => false,
                'permissions' => $updateContactPermissions,
                'message' => 'Contact could not be updated',
            ];
        }

        $response = [
            'success' => true,
            'permissions' => $updateContactPermissions,
            'message' => 'Contact successfully updated',
        ];
        return response($response);
    }

    public function getContactInfoByPhoneNumber($userId, $phoneNumber)
    {
        $contactInfo = Contacts::where('phone_number', $phoneNumber)
            ->where('user_id', $userId)
            ->where('removed', 0)->first();
        if (!$contactInfo) {
            return response([
                'success' => false,
                'message' =>  'Contact not found',
            ], 401);
        }
        return [
            'success' => true,
            'message' => 'Contact found',
            'data' =>  $contactInfo,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $deleteContact = Contacts::where('id', $id)
            ->where('removed', 0)
            ->update([
                'removed' => 1,
            ]);

        $deleteContactPermissions = ContactsPermissions::where('contacts_id', $id)
            ->where('removed', 0)
            ->update([
                'removed' => 1,
            ]);

        $response = [
            'success' => true,
            'message' => 'Contact successfully deleted',
        ];

        return response($response);
    }
}
