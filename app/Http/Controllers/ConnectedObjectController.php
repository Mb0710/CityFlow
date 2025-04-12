<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConnectedObject; // Ajoutez cette ligne pour importer le modèle
use Exception;

class ConnectedObjectController extends Controller
{
    // Cette méthode doit s'appeler searchConnectedObject pour correspondre à la route dans web.php
    public function searchConnectedObject(Request $request)
    {
        try {
            $request->validate([
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
            ]);

            $lat = $request->lat;
            $lng = $request->lng;

            // Recherche des objets connectés dans une plage autour des coordonnées données
            $connectedObjects = ConnectedObject::whereBetween('lat', [$lat-0.1, $lat+0.1])
                ->whereBetween('lng', [$lng-0.1, $lng+0.1])
                ->get();

            return response()->json($connectedObjects);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}