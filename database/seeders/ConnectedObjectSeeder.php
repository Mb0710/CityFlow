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
    public function run(): void
    {
        $zones = CityZone::all();

        foreach ($zones as $zone) {
            $numberOfObjects = rand(15, 20);
            $this->command->info("Zone '{$zone->name}' (type: {$zone->type}) → {$numberOfObjects} objets connectés générés.");

            for ($i = 0; $i < $numberOfObjects; $i++) {
                // On tire aléatoirement un type d'objet
                $objectType = fake()->randomElement([
                    'lampadaire',
                    'capteur_pollution',
                    'borne_bus',
                    'panneau_information',
                    'caméra'
                ]);

                // Génération des coordonnées
                do {
                    $lat = $zone->lat + fake()->randomFloat(6, -0.002, 0.002);
                    $lng = $zone->lng + fake()->randomFloat(6, -0.002, 0.002);
                } while (
                    $lat < 49.025 || $lat > 49.045 ||
                    $lng < 2.055 || $lng > 2.075
                );


                $commonData = [
                    'unique_id' => Str::uuid(),
                    'status' => fake()->randomElement(['actif', 'inactif', 'maintenance']),
                    'battery_level' => fake()->numberBetween(30, 100),
                    'lat' => $lat,
                    'lng' => $lng,
                    'last_interaction' => now()->subMinutes(rand(1, 1440)),

                ];

                $objectData = [];

                switch ($objectType) {
                    case 'lampadaire':
                        $objectData = [
                            'name' => 'Lampadaire intelligent',
                            'description' => 'Éclairage public adaptatif.',
                            'type' => 'lampadaire',
                            'attributes' => [
                                'intensite' => fake()->randomElement(['basse', 'moyenne', 'forte'])
                            ]
                        ];
                        break;

                    case 'capteur_pollution':
                        $objectData = [
                            'name' => 'Capteur de pollution',
                            'description' => 'Mesure la qualité de l’air.',
                            'type' => 'capteur_pollution',
                            'attributes' => [
                                'capteur' => fake()->randomElement(['CO2', 'NO2', 'PM2.5'])
                            ],
                        ];
                        break;

                    case 'borne_bus':
                        $objectData = [
                            'name' => 'Borne d’arrêt de bus',
                            'description' => 'Affiche les horaires et infos trafic.',
                            'type' => 'borne_bus',
                            'attributes' => [
                                'ligne' => fake()->randomElement(['12', '42', 'A', 'C'])
                            ],
                        ];
                        break;

                    case 'panneau_information':
                        $objectData = [
                            'name' => 'Panneau d’information',
                            'description' => 'Affiche des annonces locales.',
                            'type' => 'panneau_information',
                            'attributes' => [
                                'contenu' => fake()->sentence()
                            ],
                        ];
                        break;

                    case 'caméra':
                        $objectData = [
                            'name' => 'Caméra de surveillance',
                            'description' => 'Surveille la zone et détecte les mouvements.',
                            'type' => 'caméra',
                            'attributes' => [
                                'resolution' => fake()->randomElement(['720p', '1080p', '4K'])
                            ],
                        ];
                        break;
                }


                $object = new ConnectedObject(array_merge($commonData, $objectData));


                $object->setZoneFromCoordinates();


                $object->save();
            }
        }
    }

}