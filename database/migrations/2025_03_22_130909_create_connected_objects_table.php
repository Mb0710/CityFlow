<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Connected objects table
         */
        Schema::create('connected_objects', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
<<<<<<< HEAD
            $table->enum('type', ['lampadaire', 'capteur_pollution', 'borne_bus', 'panneau_information','caméra']); //Velo,panneau solaire,
=======
            $table->enum('type', ['lampadaire', 'capteur_pollution', 'borne_bus', 'panneau_information', 'caméra']); //Velo,panneau solaire,
>>>>>>> origin/master
            $table->enum('status', ['actif', 'inactif', 'maintenance'])->default('actif');
            $table->json('attributes')->nullable();
            $table->integer('battery_level')->nullable();
            //Nouvelle table.
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->foreignId('zone_id')->constrained('city_zones')->onDelete('cascade');
            $table->timestamp('last_interaction')->nullable();
            $table->boolean('reported')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_objects');
    }
};
