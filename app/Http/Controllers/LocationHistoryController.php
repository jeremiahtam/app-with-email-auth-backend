<?php

namespace App\Http\Controllers;

use App\Models\LocationHistory;
use Illuminate\Http\Request;

class LocationHistoryController extends Controller
{
    public function liveStatus(Request $request)
    {
        $fields = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $liveStatusInfo = LocationHistory::where('user_id', $fields['user_id'])
            ->where('removed', 0)->orderBy('created_at', 'desc')->first();

        if (!$liveStatusInfo) {
            return [
                'success' => false,
                'message' => 'Cannot retrieve live status',
            ];
        }

        return [
            'success' => true,
            'message' => 'Live status successfully retrieved',
            'data' => $liveStatusInfo,
        ];
    }

    public function liveLocationSwitch(Request $request)
    {
        $fields = $request->validate([
            'user_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string',
            'live' => 'required|boolean',
        ]);

        if ($fields['live'] == 1) {

            $data = new Request([
                'switch_on' => 1,
                'user_id' => $fields['user_id'],
                'latitude' => $fields['latitude'],
                'longitude' => $fields['longitude'],
                'location_name' => $fields['location_name'],
                'live' => $fields['live'],
            ]);
            return $this->create($data);
        } else {

            $getLiveStatus = LocationHistory::where('user_id', $fields['user_id'])
                ->where('live', 1)->where('removed', 0)
                ->orderBy('created_at', 'desc')->first();

            if ($getLiveStatus) {

                $updateLiveStatus = $getLiveStatus->update([
                    'live' => 0
                ]);

                if (!$updateLiveStatus) {
                    return [
                        'success' => false,
                        'message' => 'Could not turn off live location',
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Could not retrieve location history to get live status',
                ];
            }

            return [
                'success' => true,
                'message' => 'Live location successfully turned off',
            ];
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fields = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $locationHistoryList = LocationHistory::where('user_id', $fields['user_id'])
            ->where('removed', 0)->get();

        if (!$locationHistoryList) {
            return [
                'success' => false,
                'message' => 'Could not get user location history',
                'data' => $locationHistoryList,
            ];
        }
        return [
            'success' => true,
            'message' => 'User location history successfully retrieved',
            'data' => $locationHistoryList,
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fields = $request->validate([
            'user_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string',
            'live' => 'required|boolean',
        ]);

        $userLiveLocaion = LocationHistory::where('user_id', $fields['user_id'])
            ->where('live', 1)->where('removed', 0)
            ->orderBy('created_at', 'desc')->first();

        //check if a live location does not exist for the user
        if ($userLiveLocaion == null) {

            if ($request->has('switch_on')) {
                //If it does not exist, create a new location history ONLY if 
                //the switch is tuned ON
                $createLocationHistory = LocationHistory::create([
                    'user_id' => $fields['user_id'],
                    'latitude' => $fields['latitude'],
                    'longitude' => $fields['longitude'],
                    'location_name' => $fields['location_name'],
                    'live' => 1,
                    'removed' => 0,
                ]);

                if (!$createLocationHistory) {
                    return [
                        'success' => false,
                        'message' => 'Could not create a live location',
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'New live location history created',
                ];
            }
            return [
                'success' => false,
                'message' => 'Could not create a live location. You have no live locations',
            ];
        } else {
            //If it exists, get the location history id and add to the data
            //make a new location update for that live location 
            $fields[] = ['location_history_id' => $userLiveLocaion->id];


            $locationUpdate = new LocationUpdateController;
            return $locationUpdate->create([
                'user_id' => $fields['user_id'],
                'location_history_id' => $userLiveLocaion->id,
                'latitude' => $fields['latitude'],
                'longitude' => $fields['longitude'],
                'location_name' => $fields['location_name'],
                'live' => 1,
                'removed' => 0,
            ]);
        }

        return [
            'success' => true,
            'message' => 'Live location created',
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
        $locationUpdateController = new LocationUpdateController;
        return $locationUpdateController->show($id);
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
