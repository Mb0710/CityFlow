<?php

namespace Database\Seeders;

use App\Models\CityZone;
use Illuminate\Database\Seeder;
use App\Models\ConnectedObject;
use Illuminate\Support\Str;

class ConnectedObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        $zones = CityZone::all();

        // 🔁 pour chaque zone
        foreach ($zones as $zone) {
            $numberOfObjects = rand(15, 20);
            $this->command->info("Zone '{$zone->name}' (type: {$zone->type}) → {$numberOfObjects} objets connectés générés.");

            for ($i = 0; $i < $numberOfObjects; $i++) {
                $objectType = fake()->randomElement([
                    'lampadaire',
                    'capteur_pollution',
                    'borne_bus',
                    'panneau_information',
                    'caméra'
                ]);

                $coords = [
                    'lat' => fake()->latitude(48.80, 48.90),
                    'lng' => fake()->longitude(2.30, 2.40),
                ];

                // 🔁 On adapte dynamiquement le contenu de l’objet selon le type tiré
                $commonData = [
                    'unique_id' => Str::uuid(),
                    'status' => fake()->randomElement(['actif', 'inactif', 'maintenance']),
                    'battery_level' => fake()->numberBetween(30, 100),
                    'coordinates' => json_encode($coords),
                    'zone_id' => $zone->id,
                    'last_interaction' => now()->subMinutes(rand(1, 1440)),
                ];

                switch ($objectType) {
                    case 'lampadaire':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Lampadaire intelligent',
                            'description' => 'Éclairage public adaptatif.',
                            'type' => 'lampadaire',
                            'attributes' => json_encode([ 'intensité' => fake()->randomElement(['basse', 'moyenne', 'forte'])], JSON_UNESCAPED_UNICODE),
                        ]));
                        break;

                    case 'capteur_pollution':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Capteur de pollution',
                            'description' => 'Mesure la qualité de l’air.',
                            'type' => 'capteur_pollution',
                            'attributes' => json_encode(fake()->randomElement(['CO2', 'NO2', 'PM2.5'])),
                        ]));
                        break;

                    case 'borne_bus':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Borne d’arrêt de bus',
                            'description' => 'Affiche les horaires et infos trafic.',
                            'type' => 'borne_bus',
                            'attributes' => json_encode(['ligne' => fake()->randomElement(['12', '42', 'A', 'C'])]),
                        ]));
                        break;

                    case 'panneau_information':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Panneau d’information',
                            'description' => 'Affiche des annonces locales.',
                            'type' => 'panneau_information',
                            'attributes' => json_encode(['contenu' => fake()->sentence()]),
                        ]));
                        break;

                    case 'caméra':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Caméra de surveillance',
                            'description' => 'Surveille la zone et détecte les mouvements.',
                            'type' => 'panneau_information', // ou change selon tes migrations
                            'attributes' => json_encode(['résolution' => fake()->randomElement(['720p', '1080p', '4K'])],JSON_UNESCAPED_UNICODE),
                        ]));
                        break;
                }
            }
        }


        // Laravel génère :
// INSERT INTO connected_objects (name, type, description)
// VALUES ('Caméra de surveillance', 'Sécurité', 'Détecte les mouvements suspects.')
    }
}
