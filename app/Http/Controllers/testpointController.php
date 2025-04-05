<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestPoint;

class TestPointController extends Controller
{
    public function searchTestPoint(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        
        $testpoints = TestPoint::whereBetween('lat', [$lat-0.1, $lat+0.1])
                         ->whereBetween('lng', [$lng-0.1, $lng+0.1])
                         ->get();
        
        return response()->json($testpoints);
    }
    
    public function storeTestPoint(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        
        $testpoint = TestPoint::create($validated);
        
        return response()->json($testpoint);
    }
}