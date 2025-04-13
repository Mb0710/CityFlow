<?php

namespace App\Http\Controllers;

use App\Models\ConnectedObject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $this->updateBatteryLevels();

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




    private function updateBatteryLevels()
    {
        $lastUpdate = Cache::get('last_battery_update_sim', now()->subMinutes(10));
        $now = now();

        $minutesElapsed = abs($now->diffInMinutes($lastUpdate));
        if ($minutesElapsed >= 1) {
            $baseDischargePerMinute = 1; // 1% par minute
            $totalDischarge = $baseDischargePerMinute * $minutesElapsed;

            $activeObjects = ConnectedObject::where('status', 'actif')->get();

            foreach ($activeObjects as $object) {
                $newLevel = max(0, $object->battery_level - $totalDischarge);
                $object->battery_level = round($newLevel, 1);

                if ($object->battery_level <= 0) {
                    $object->status = 'inactif';
                }

                $object->save();
            }

            Cache::put('last_battery_update_sim', $now, 60 * 24);
        }
    }


    private function awardPointsAndLogAction($userId, $actionType, $objectId, $points, $description = null)
    {
        // Récupérer le niveau avant l'attribution des points
        $user = User::findOrFail($userId);
        $oldLevel = $user->level;

        // Ajout des points à l'utilisateur
        $user->points += $points;
        $user->save();

        $newLevel = $user->updateLevelBasedOnPoints();
        // Enregistrement de l'action
        UserAction::create([
            'user_id' => $userId,
            'action_type' => $actionType,
            'object_id' => $objectId,
            'description' => $description,
            'points' => $points
        ]);

        // Récupérer le niveau après l'attribution des points
        $levelInfo = $user->getUserLevel();

        return [
            'total_points' => $user->points,
            'level_changed' => ($oldLevel !== $newLevel),
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'level_info' => $levelInfo
        ];
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
                'lat' => 'required|numeric|min:49.015|max:49.055',
                'lng' => 'required|numeric|min:2.02|max:2.11',
                'attributes' => 'nullable|json',
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

            if ($request->has('attributes')) {

                if (is_string($request->input('attributes'))) {

                    json_decode($request->input('attributes'));
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $object->attributes = $request->input('attributes');
                    } else {
                        $object->attributes = json_encode($request->input('attributes'));
                    }
                } else {
                    $object->attributes = json_encode($request->input('attributes'));
                }
            }


            if (!isset($object->description) || empty($object->description)) {
                // Utiliser les mêmes descriptions que dans le seeder
                switch ($object->type) {
                    case 'lampadaire':
                        $object->description = 'Éclairage public adaptatif.';
                        break;
                    case 'capteur_pollution':
                        $object->description = 'Mesure la qualité de l’air.';
                        break;
                    case 'borne_bus':
                        $object->description = 'Affiche les horaires et infos trafic.';
                        break;
                    case 'panneau_information':
                        $object->description = 'Affiche des annonces locales.';
                        break;
                    case 'caméra':
                        $object->description = 'Surveille la zone et détecte les mouvements.';
                        break;
                    default:
                        $object->description = $object->name . ' description';
                        break;
                }
            }


            $object->last_interaction = now();


            $object->setZoneFromCoordinates();


            $object->save();

            $pointsResult = $this->awardPointsAndLogAction(
                Auth::id(),
                'ajout',
                $object->id,
                10,
                "Ajout d'un nouvel objet: " . $object->name
            );

            return response()->json([
                'success' => true,
                'data' => $object,
                'points_awarded' => 10,
                'total_points' => $pointsResult['total_points'],
                'level_changed' => $pointsResult['level_changed'],
                'new_level' => $pointsResult['level_changed'] ? $pointsResult['new_level'] : null,
                'old_level' => $pointsResult['level_changed'] ? $pointsResult['old_level'] : null,
                'level_info' => $pointsResult['level_info']
            ]);
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

    public function report($id)
    {
        $connectedObject = ConnectedObject::find($id);

        if (!$connectedObject) {
            return response()->json([
                'success' => false,
                'message' => 'Objet connecté non trouvé'
            ], 404);
        }

        $connectedObject->reported = true;
        $connectedObject->save();

        $pointsResult = $this->awardPointsAndLogAction(
            Auth::id(),
            'signalement',
            $connectedObject->id,
            10,
            "signalement d'un nouvel objet: " . $connectedObject->name
        );

        return response()->json([
            'success' => true,
            'data' => $connectedObject,
            'points_awarded' => 10,
            'total_points' => $pointsResult['total_points'],
            'level_changed' => $pointsResult['level_changed'],
            'new_level' => $pointsResult['level_changed'] ? $pointsResult['new_level'] : null,
            'old_level' => $pointsResult['level_changed'] ? $pointsResult['old_level'] : null,
            'level_info' => $pointsResult['level_info']
        ]);
    }


    public function getReportedObjects()
    {
        $reportedObjects = ConnectedObject::where('reported', true)->get();

        return response()->json([
            'success' => true,
            'data' => $reportedObjects
        ]);
    }

    /**
     * Supprimer un objet connecté
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $object = ConnectedObject::findOrFail($id);

            // Supprimer explicitement toutes les actions liées à cet objet
            UserAction::where('object_id', $id)->delete();

            // Puis supprimer l'objet
            $object->delete();

            return response()->json([
                'success' => true,
                'message' => 'Objet connecté supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cet objet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annuler le signalement d'un objet
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelReport($id)
    {
        $connectedObject = ConnectedObject::find($id);

        if (!$connectedObject) {
            return response()->json([
                'success' => false,
                'message' => 'Objet connecté non trouvé'
            ], 404);
        }

        $connectedObject->reported = false;
        $connectedObject->save();

        return response()->json([
            'success' => true,
            'message' => 'Signalement annulé avec succès',
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
        $originalBatteryLevel = $connectedObject->battery_level;

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
            'type' => 'exists:object_types,name',
            'status' => 'in:actif,inactif,maintenance',
            'attributes' => 'nullable|json',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'lat' => 'nullable|numeric|min:49.015|max:49.055',
            'lng' => 'nullable|numeric|min:2.02|max:2.11',
            'zone_id' => 'exists:city_zones,id',
            'action_type' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $actionType = $request->input('action_type');

        $connectedObject->update($request->except('action_type'));

        if ($originalBatteryLevel < 100 && $connectedObject->battery_level == 100) {
            $pointsResult = $this->awardPointsAndLogAction(
                Auth::id(),
                'recharge',
                $id,
                5,
                "Recharge de l'objet: " . $connectedObject->name
            );

            return response()->json([
                'success' => true,
                'data' => $connectedObject,
                'points_awarded' => 5,
                'total_points' => $pointsResult['total_points'],
                'level_changed' => $pointsResult['level_changed'],
                'new_level' => $pointsResult['new_level'],
                'old_level' => $pointsResult['old_level'],
                'level_info' => $pointsResult['level_info']
            ]);
        } elseif ($actionType === 'modification') {
            $pointsResult = $this->awardPointsAndLogAction(
                Auth::id(),
                'modification',
                $id,
                7,
                "Modification de l'objet: " . $connectedObject->name
            );

            return response()->json([
                'success' => true,
                'data' => $connectedObject,
                'points_awarded' => 7,
                'total_points' => $pointsResult['total_points'],
                'level_changed' => $pointsResult['level_changed'],
                'new_level' => $pointsResult['new_level'],
                'old_level' => $pointsResult['old_level'],
                'level_info' => $pointsResult['level_info']
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Objet connecté mis à jour avec succès',
            'data' => $connectedObject
        ]);
    }
}