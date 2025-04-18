<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectedObject extends Model
{
    protected $fillable = [
        'unique_id',
        'name',
        'type',
        'description',
        'status',
        'attributes',
        'battery_level',
        'lat',
        'lng',
        'zone_id',
        'last_interaction',


    ];

    protected $casts = [
        'attributes' => 'array',
        'battery_level' => 'integer',
        'lat' => 'float',
        'lng' => 'float',
    ];


    public function objectType()
    {
        return $this->belongsTo(ObjectType::class, 'type', 'name');
    }
    public function zone()
    {
        return $this->belongsTo(CityZone::class, 'zone_id');
    }


    public function userActions()
    {
        return $this->hasMany(UserAction::class, 'object_id', 'id');
    }

    public function findNearestZone()
    {
        if (!$this->lat || !$this->lng) {
            return null;
        }

        // Récupère toutes les zones
        $zones = \App\Models\CityZone::all();

        if ($zones->isEmpty()) {
            return null;
        }

        $nearestZone = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($zones as $zone) {

            $distance = $this->calculateDistance($this->lat, $this->lng, $zone->lat, $zone->lng);

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestZone = $zone;
            }
        }

        return $nearestZone;
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);


        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));


        $r = 6371;


        return $r * $c;
    }


    public function setZoneFromCoordinates()
    {
        $zone = $this->findNearestZone();

        if ($zone) {
            $this->zone_id = $zone->id;
            return true;
        }

        return false;
    }

    public static function generateReport()
    {
        $totalObjects = self::count();

        $objectsByType = self::select('type', \DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        $activeObjects = self::where('status', 'actif')->count();
        $inactiveObjects = self::where('status', '!=', 'actif')->count();

        $averageBatteryLevel = self::avg('battery_level');

        // Objets avec batterie faible (moins de 20%)
        $lowBatteryObjects = self::where('battery_level', '<', 20)->count();

        // Dernière interaction
        $lastInteractionDate = self::max('last_interaction');

        // Distribution par zone
        $objectsByZone = self::select('zone_id', \DB::raw('count(*) as count'))
            ->groupBy('zone_id')
            ->with('zone') // Pour charger le nom de la zone
            ->get();

        return [
            'total_objects' => $totalObjects,
            'objects_by_type' => $objectsByType,
            'active_objects' => $activeObjects,
            'inactive_objects' => $inactiveObjects,
            'average_battery_level' => $averageBatteryLevel,
            'low_battery_objects' => $lowBatteryObjects,
            'last_interaction_date' => $lastInteractionDate,
            'objects_by_zone' => $objectsByZone,
        ];
    }
}