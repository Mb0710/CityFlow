<?php

namespace App\Http\Controllers;

use App\Models\ConnectedObject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ConnectedObjectsController extends Controller
{
    /**
     * Afficher une liste de tous les objets connectés
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $connectedObjects = ConnectedObject::all();

        return response()->json([
            'success' => true,
            'data' => $connectedObjects
        ]);
    }

    /**
     * Afficher les objets connectés par type
     * 
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function getByType($type)
    {
        $connectedObjects = ConnectedObject::where('type', $type)->get();

        return response()->json([
            'success' => true,
            'data' => $connectedObjects
        ]);
    }

    /**
     * Afficher les objets connectés par statut
     * 
     * @param string $status
     * @return \Illuminate\Http\Response
     */
    public function getByStatus($status)
    {
        $connectedObjects = ConnectedObject::where('status', $status)->get();

        return response()->json([
            'success' => true,
            'data' => $connectedObjects
        ]);
    }

    /**
     * Afficher les objets connectés par zone
     * 
     * @param int $zoneId
     * @return \Illuminate\Http\Response
     */
    public function getByZone($zoneId)
    {
        $connectedObjects = ConnectedObject::where('zone_id', $zoneId)->get();

        return response()->json([
            'success' => true,
            'data' => $connectedObjects
        ]);
    }

    /**
     * Stocker un nouvel objet connecté
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'unique_id' => 'required|string|unique:connected_objects',
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'description' => 'nullable|string',
                'status' => 'required|string',
                'battery_level' => 'integer|min:0|max:100',
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
            ]);



            $existingObject = ConnectedObject::where('lat', $request->lat)
                ->where('lng', $request->lng)
                ->first();

            if ($existingObject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un objet existe déjà à ces coordonnées',
                    'errors' => [
                        'coordinates' => ['Un objet connecté existe déjà à l\'emplacement (lat: ' . $request->lat . ', lng: ' . $request->lng . ')']
                    ]
                ], 422);
            }


            $object = new ConnectedObject($validated);


            if (!isset($object->description)) {
                $object->description = $object->name . ' description';
            }


            $object->last_interaction = now();


            $object->setZoneFromCoordinates();


            $object->save();

            return response()->json([
                'success' => true,
                'message' => 'Connected object created successfully',
                'data' => $object
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create connected object',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un objet connecté spécifique
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $connectedObject = ConnectedObject::find($id);

        if (!$connectedObject) {
            return response()->json([
                'success' => false,
                'message' => 'Objet connecté non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $connectedObject
        ]);
    }

    /**
     * Obtenir un objet connecté par son unique_id
     *
     * @param  string  $uniqueId
     * @return \Illuminate\Http\Response
     */
    public function getByUniqueId($uniqueId)
    {
        $connectedObject = ConnectedObject::where('unique_id', $uniqueId)->first();

        if (!$connectedObject) {
            return response()->json([
                'success' => false,
                'message' => 'Objet connecté non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $connectedObject
        ]);
    }

    /**
     * Mettre à jour un objet connecté
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $connectedObject = ConnectedObject::find($id);

        if (!$connectedObject) {
            return response()->json([
                'success' => false,
                'message' => 'Objet connecté non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'unique_id' => 'string|unique:connected_objects,unique_id,' . $id,
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'type' => 'in:lampadaire,capteur_pollution,borne_bus,panneau_information,caméra',
            'status' => 'in:actif,inactif,maintenance',
            'attributes' => 'nullable|json',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'zone_id' => 'exists:city_zones,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $connectedObject->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Objet connecté mis à jour avec succès',
            'data' => $connectedObject
        ]);
    }
}