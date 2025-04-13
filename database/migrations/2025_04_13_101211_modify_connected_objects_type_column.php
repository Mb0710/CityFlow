<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // D'abord, sauvegardez les données existantes
        $objects = DB::table('connected_objects')->get();

        // Modifiez la colonne type pour être une string normale au lieu d'un ENUM
        Schema::table('connected_objects', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });

        // Vérifier si la table de référence object_types existe
        if (Schema::hasTable('object_types')) {
            // Ajouter une contrainte de clé étrangère
            Schema::table('connected_objects', function (Blueprint $table) {
                $table->foreign('type')
                    ->references('name')
                    ->on('object_types')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            });
        }
    }

    public function down(): void
    {
        // Supprimer la contrainte de clé étrangère
        Schema::table('connected_objects', function (Blueprint $table) {
            $table->dropForeign(['type']);
        });

        // Remettre la colonne comme ENUM
        Schema::table('connected_objects', function (Blueprint $table) {
            $table->enum('type', ['lampadaire', 'capteur_pollution', 'borne_bus', 'panneau_information', 'caméra'])->change();
        });
    }
};