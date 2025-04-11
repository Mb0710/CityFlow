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
            ['name' => 'Cergy Préfecture', 'type' => 'administratif', 'lat' => 49.0412, 'lng' => 2.0589],
            ['name' => 'Port-Cergy', 'type' => 'loisirs', 'lat' => 49.0349, 'lng' => 2.0701],
            ['name' => 'Cergy Saint-Christophe', 'type' => 'commercial', 'lat' => 49.0377, 'lng' => 2.0684],
            ['name' => 'Les Linandes', 'type' => 'industriel', 'lat' => 49.0295, 'lng' => 2.0653],
            ['name' => 'Axe Majeur', 'type' => 'loisirs', 'lat' => 49.0278, 'lng' => 2.0721],
            ['name' => 'Cergy Le Haut', 'type' => 'résidentiel', 'lat' => 49.0385, 'lng' => 2.0595],
            ['name' => 'Parc François Mitterrand', 'type' => 'loisirs', 'lat' => 49.0332, 'lng' => 2.0749],
            ['name' => 'Université de Cergy', 'type' => 'administratif', 'lat' => 49.0304, 'lng' => 2.0667],
            ['name' => 'Quartier d’affaires', 'type' => 'commercial', 'lat' => 49.0408, 'lng' => 2.0642],
        ];

        foreach ($zones as $zone) {

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