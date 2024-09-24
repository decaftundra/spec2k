<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect(route('notifications.index'));
    }
    
    /**
     * Set the show all fields setting.
     *
     * @param (boolean) $setting
     * @return \Illuminate\Http\Response
     */
    public function showFields($setting)
    {
        if ($setting) {
            session(['show_all_fields' => 'show']);
        } else {
            session()->forget('show_all_fields');
        }
        
        return response()->json(['success' => 'true'], 200);
    }
    
    /**
     * Set the show all segments setting.
     *
     * @param (boolean) $setting
     * @return \Illuminate\Http\Response
     */
    public function showSegments($setting)
    {
        if ($setting) {
            session(['show_all_segments' => 'show']);
        } else {
            session()->forget('show_all_segments');
        }
        
        return response()->json(['success' => 'true'], 200);
    }
}
