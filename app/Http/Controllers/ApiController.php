<?php

namespace App\Http\Controllers;

use App\Location;
use App\Notification;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Show the forms to manage oAuth2 clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function clientManagement()
    {
        return view('api.client-management');
    }
    
    /**
     * Create the basic Notification info from api call.
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:50',
            'plant_code' => 'required|integer',
            'rcsMPN' => 'required|string|max:32',
            'rcsSER' => 'required|string|max:15',
            'rcsMRD' => 'required|date_format:Y-m-d',
            'hdrROC' => 'required|string|min:3|max:5',
            'hdrRON' => 'nullable|string|max:55'
        ]);
        
        Notification::withTrashed()->updateOrCreate(
            ['id' => $request->id],
            [
                'id' => $request->id,
                'plant_code' => $request->plant_code,
                'rcsSFI' => $request->id,
                'rcsMPN' => $request->rcsMPN,
                'rcsSER' => $request->rcsSER,
                'rcsMRD' => $request->rcsMRD,
                'hdrROC' => Location::getFirstCageCode($request->plant_code),
                'hdrRON' => Location::getReportingOrganisationName($request->plant_code)
            ]
        );
        
        /*
        // Create the Notification.
        $notification = new Notification;
        $notification->id = $request->id;
        $notification->plant_code = $request->plant_code;
        $notification->rcsSFI = $request->id;
        $notification->rcsMPN = $request->rcsMPN;
        $notification->rcsSER = $request->rcsSER;
        $notification->rcsMRD = $request->rcsMRD;
        $notification->hdrROC = $request->hdrROC;
        $notification->hdrRON = $request->hdrRON;
        $notification->save();
        */
        
        return response()->json(['success' => true], 201);
    }
}
