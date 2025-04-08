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
        // 🔁 On récupère toutes les zones pour leur associer des objets connectés
        $zones = CityZone::all();

        foreach ($zones as $zone) {
            // 🎲 Détermine un nombre d’objets aléatoire à générer pour chaque zone
            $numberOfObjects = rand(15, 20);
            $this->command->info("Zone '{$zone->name}' (type: {$zone->type}) → {$numberOfObjects} objets connectés générés.");

            for ($i = 0; $i < $numberOfObjects; $i++) {
                //  On tire aléatoirement un type d’objet
                $objectType = fake()->randomElement([
                    'lampadaire',
                    'capteur_pollution',
                    'borne_bus',
                    'panneau_information',
                    'caméra'
                ]);

                //  Coordonnées GPS générées dans les limites précises de Cergy
                do {
                    $lat = $zone->lat + fake()->randomFloat(6, -0.002, 0.002);
                    $lng = $zone->lng + fake()->randomFloat(6, -0.002, 0.002);
                } while (
                    $lat < 49.025 || $lat > 49.045 ||
                    $lng < 2.055 || $lng > 2.075
                );
                //  Données communes à tous les types d’objets
                $commonData = [
                    'unique_id' => Str::uuid(),
                    'status' => fake()->randomElement(['actif', 'inactif', 'maintenance']),
                    'battery_level' => fake()->numberBetween(30, 100),
                    'lat' => $lat,
                    'lng' => $lng,
                    'zone_id' => $zone->id,
                    'last_interaction' => now()->subMinutes(rand(1, 1440)), // entre 1 min et 24h
                ];

                //  On adapte les champs spécifiques à chaque type d'objet
                switch ($objectType) {
                    case 'lampadaire':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Lampadaire intelligent',
                            'description' => 'Éclairage public adaptatif.',
                            'type' => 'lampadaire',
                            'attributes' => json_encode([
                                'intensité' => fake()->randomElement(['basse', 'moyenne', 'forte'])
                            ], JSON_UNESCAPED_UNICODE),
                        ]));
                        break;

                    case 'capteur_pollution':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Capteur de pollution',
                            'description' => 'Mesure la qualité de l’air.',
                            'type' => 'capteur_pollution',
                            'attributes' => json_encode([
                                'capteur' => fake()->randomElement(['CO2', 'NO2', 'PM2.5'])
                            ]),
                        ]));
                        break;

                    case 'borne_bus':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Borne d’arrêt de bus',
                            'description' => 'Affiche les horaires et infos trafic.',
                            'type' => 'borne_bus',
                            'attributes' => json_encode([
                                'ligne' => fake()->randomElement(['12', '42', 'A', 'C'])
                            ]),
                        ]));
                        break;

                    case 'panneau_information':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Panneau d’information',
                            'description' => 'Affiche des annonces locales.',
                            'type' => 'panneau_information',
                            'attributes' => json_encode([
                                'contenu' => fake()->sentence()
                            ]),
                        ]));
                        break;

                    case 'caméra':
                        ConnectedObject::create(array_merge($commonData, [
                            'name' => 'Caméra de surveillance',
                            'description' => 'Surveille la zone et détecte les mouvements.',
                            'type' => 'caméra', // ✅ correction ici
                            'attributes' => json_encode([
                                'résolution' => fake()->randomElement(['720p', '1080p', '4K'])
                            ], JSON_UNESCAPED_UNICODE),
                        ]));
                        break;
                }
            }
        }
    }
}