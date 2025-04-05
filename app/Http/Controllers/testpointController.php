<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestPoint;
use Exception;

class TestPointController extends Controller
{
    // Recherche des points de test en fonction de la latitude et de la longitude
    public function searchTestPoint(Request $request)
    {
        try {
            $request->validate([
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
            ]);

            $lat = $request->lat;
            $lng = $request->lng;
            
            // Recherche des points de test dans une plage autour des coordonnées données
            $testpoints = TestPoint::whereBetween('lat', [$lat-0.1, $lat+0.1])
                             ->whereBetween('lng', [$lng-0.1, $lng+0.1])
                             ->get();
            
            return response()->json($testpoints);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Sauvegarde d'un point de test
    public function store(Request $request)
    {
        \Log::info('Données reçues dans store:', $request->all());

        try {
            // Validation des données envoyées par la requête
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
            ]);
            
            // Création du point de test avec les données validées
            $testpoint = TestPoint::create([
                'name' => $validated['name'],
                'lat' => $validated['lat'],
                'lng' => $validated['lng']
            ]);
            
            // Retourne une réponse JSON contenant les données du point de test créé
            return response()->json(['success' => true, 'point' => $testpoint], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}