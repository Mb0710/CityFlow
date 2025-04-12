<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConnectedObject;

class ChartController extends Controller
{
    public function stats()
    {
        //  Nombre d'objets par zone
        $byZone = ConnectedObject::selectRaw('zone_id, COUNT(*) as total')
            ->groupBy('zone_id')
            ->with('zone') // pour récupérer le nom de la zone
            ->get();

        //  Nombre d'objets par type
        $byType = ConnectedObject::selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->get();

        //  Moyenne batterie par type
        $batteryAvg = ConnectedObject::selectRaw('type, AVG(battery_level) as average')
            ->groupBy('type')
            ->get();

        //  Nombre d'objets par statut
        $byStatus = ConnectedObject::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        //  On envoie toutes les données à la vue
        return view('stats', compact('byZone', 'byType', 'batteryAvg', 'byStatus'));
    }
}
