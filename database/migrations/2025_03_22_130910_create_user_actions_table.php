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
         * User actions table
         */
        Schema::create('user_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('action_type', ['consultation', 'modification', 'ajout', 'suppression']);
            $table->string('action_details');
            $table->enum('related_to', ['object', 'service', 'profile']);
            $table->unsignedBigInteger('related_id')->nullable();
            $table->float('points_earned')->default(0.5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_actions');
    }
};
