<?php

namespace App\Http\Controllers;

use App\Models\ObjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ObjectTypeController extends Controller
{
    /**
     * Afficher tous les types d'objets
     */
    public function index()
    {
        $types = ObjectType::all();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Ajouter un nouveau type d'objet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:object_types,name|max:255',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*.nom' => 'required|string',
            'attributes.*.label' => 'required|string',
            'attributes.*.type' => 'required|string|in:text,select,number',
            'attributes.*.options' => 'required_if:attributes.*.type,select|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $type = ObjectType::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Type d\'objet ajouté avec succès',
            'data' => $type
        ], 201);
    }

    /**
     * Supprimer un type d'objet
     */
    public function destroy($id)
    {
        $type = ObjectType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'objet non trouvé'
            ], 404);
        }

        // Vérifier si des objets utilisent ce type
        $usageCount = $type->connectedObjects()->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type est utilisé par ' . $usageCount . ' objets connectés et ne peut pas être supprimé'
            ], 422);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type d\'objet supprimé avec succès'
        ]);
    }
}