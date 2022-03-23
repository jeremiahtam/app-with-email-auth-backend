<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\ContactsPermissions;
use Illuminate\Http\Request;

class ContactsPermissionsController extends Controller
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
    public function create($contactsId, $data)
    {
        $newContactPermissions = ContactsPermissions::create([
            'contacts_id' => $contactsId,
            'last_seen' => $data['last_seen'],
            'live_location' => $data['live_location'],
            'location_history' => $data['location_history'],
            'removed' => 0,
        ]);

        if (!$newContactPermissions) {
            $response = [
                'success' => false,
                'message' => 'Contact permissions could not be created',
            ];
        }

        $response = [
            'success' => true,
            'message' => 'Contact permissions successfully created',
        ];
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $data)
    {
        $updateContactPermission = ContactsPermissions::where('contacts_id', $id)
            ->where('removed', 0)
            ->update([
                'last_seen' => $data['last_seen'],
                'live_location' => $data['live_location'],
                'location_history' => $data['location_history'],
            ]);

        if (!$updateContactPermission) {
            $response = [
                'success' => false,
                'message' => 'Contact permissions could not be updated',
            ];
        }

        $response = [
            'success' => true,
            'message' => 'Contact permissions successfully updated',
        ];
        return $response;
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
