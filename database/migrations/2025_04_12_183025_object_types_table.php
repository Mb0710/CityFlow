<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('object_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('default_unit')->nullable();
            $table->text('description')->nullable();
            $table->json('default_attributes')->nullable();
            $table->timestamps();
        });

        // Modification de la table connected_objects
        Schema::table('connected_objects', function (Blueprint $table) {

            $table->dropColumn('type');
        });

        Schema::table('connected_objects', function (Blueprint $table) {

            $table->foreignId('object_type_id')->after('description')->constrained('object_types');
        });
    }

    public function down(): void
    {
        // Restaurer l'ancienne structure en cas de rollback
        Schema::table('connected_objects', function (Blueprint $table) {
            $table->dropForeign(['object_type_id']);
            $table->dropColumn('object_type_id');
        });

        Schema::table('connected_objects', function (Blueprint $table) {
            $table->enum('type', ['lampadaire', 'capteur_pollution', 'borne_bus', 'panneau_information', 'caméra'])->after('description');
        });

        Schema::dropIfExists('object_types');
    }
};