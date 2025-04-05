<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestPoint;

class TestPointController extends Controller
{
    // Recherche des points de test en fonction de la latitude et de la longitude
    public function searchTestPoint(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        
        // Recherche des points de test dans une plage autour des coordonnées données
        $testpoints = TestPoint::whereBetween('lat', [$lat-0.1, $lat+0.1])
                         ->whereBetween('lng', [$lng-0.1, $lng+0.1])
                         ->get();
        
        return response()->json($testpoints);
    }
    
    // Sauvegarde d'un point de test
    public function storeTestPoint(Request $request)
    {
        // Validation des données envoyées par la requête
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        
        // Création du point de test avec les données validées
        $testpoint = TestPoint::create($validated);
        
        // Retourne une réponse JSON contenant les données du point de test créé
        return response()->json($testpoint);
    }
}

?>