<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('object_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->json('attributes')->nullable();  // Pour stocker les attributs et options
            $table->timestamps();
        });

        // Ajout des types par défaut avec attributs
        DB::table('object_types')->insert([
            [
                'name' => 'lampadaire',
                'description' => 'Éclairage public adaptatif.',
                'attributes' => json_encode([
                    [
                        'nom' => 'luminosite',
                        'label' => 'intensite',
                        'type' => 'select',
                        'options' => ['faible', 'moyenne', 'forte']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'capteur_pollution',
                'description' => 'Mesure la qualité de l\'air.',
                'attributes' => json_encode([
                    [
                        'nom' => 'type_capteur',
                        'label' => 'Type de capteur',
                        'type' => 'select',
                        'options' => ['co2', 'particules', 'nox']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'borne_bus',
                'description' => 'Affiche les horaires et infos trafic.',
                'attributes' => json_encode([
                    [
                        'nom' => 'ligne',
                        'label' => 'Ligne de bus',
                        'type' => 'text'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'panneau_information',
                'description' => 'Affiche des annonces locales.',
                'attributes' => json_encode([
                    [
                        'nom' => 'type_affichage',
                        'label' => 'Type d\'affichage',
                        'type' => 'text'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'caméra',
                'description' => 'Surveille la zone et détecte les mouvements.',
                'attributes' => json_encode([
                    [
                        'nom' => 'resolution',
                        'label' => 'Résolution',
                        'type' => 'select',
                        'options' => ['720p', '1080p', '4K']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('object_types');
    }
};