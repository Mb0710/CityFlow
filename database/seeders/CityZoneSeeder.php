<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CityZone;

class CityZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            ['name' => 'Grand Centre', 'type' => 'résidentiel', 'lat' => 49.0362, 'lng' => 2.0634],
            ['name' => 'Cergy Préfecture', 'type' => 'administratif', 'lat' => 49.0373, 'lng' => 2.0767],
            ['name' => 'Port-Cergy', 'type' => 'loisirs', 'lat' => 49.0415, 'lng' => 2.0599],
            ['name' => 'Cergy Saint-Christophe', 'type' => 'commercial', 'lat' => 49.0435, 'lng' => 2.0381],
            ['name' => 'Les Linandes', 'type' => 'industriel', 'lat' => 49.0391, 'lng' => 2.0523],
            ['name' => 'Axe Majeur', 'type' => 'loisirs', 'lat' => 49.0452, 'lng' => 2.0531],
            ['name' => 'Cergy Le Haut', 'type' => 'résidentiel', 'lat' => 49.0501, 'lng' => 2.0234],
            ['name' => 'Parc François Mitterrand', 'type' => 'loisirs', 'lat' => 49.0365, 'lng' => 2.0709],
            ['name' => 'Université de Cergy', 'type' => 'administratif', 'lat' => 49.0348, 'lng' => 2.0792],
            ['name' => 'Quartier d’affaires', 'type' => 'commercial', 'lat' => 49.0377, 'lng' => 2.0684],
        ];

        foreach ($zones as $zone) {
            // 🔒 Clamp des coordonnées dans les bornes de la map
            $lat = min(max($zone['lat'], 49.025), 49.045);
            $lng = min(max($zone['lng'], 2.055), 2.075);

            $created = CityZone::create([
                'name' => $zone['name'],
                'description' => "Zone générée automatiquement pour tests à Cergy.",
                'type' => $zone['type'],
                'lat' => $lat,
                'lng' => $lng,

            ]);
            $this->command->info(" Zone insérée : {$created->name}");
        }

        $this->command->info(" 10 zones de Cergy créées avec succès !");
    }
}
