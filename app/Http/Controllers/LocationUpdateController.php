<?php

namespace App\Http\Controllers;

use App\Models\LocationUpdate;
use Illuminate\Http\Request;

class LocationUpdateController extends Controller
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
    public function create($data)
    {
        $createLocationUpdate = LocationUpdate::create([
            'user_id' => $data['user_id'],
            'location_history_id' => $data['location_history_id'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'location_name' => $data['location_name'],
            'removed' => 0,
        ]);
        if (!$createLocationUpdate) {
            return [
                'success' => false,
                'message' => 'Could not create a live location update',
            ];
        }

        return [
            'success' => true,
            'message' => 'Live location update created',
        ];
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
        $locationUpdates = LocationUpdate::where('location_history_id', $id)
            ->where('removed', 0)->get();

        if (!$locationUpdates) {
            return [
                'success' => false,
                'message' => 'Could not get user location updates',
                'data' => $locationUpdates,
            ];
        }
        return [
            'success' => true,
            'message' => 'User location updates successfully retrieved',
            'data' => $locationUpdates,
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
