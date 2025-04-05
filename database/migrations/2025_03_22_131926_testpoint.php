<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class testpoint extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        Object service table that will allows us to connect an object to a service
        */
        Schema::create('pointtest', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('lat');
            $table->float('lng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointtest');
    }
};
