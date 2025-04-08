<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        if (!User::where('login', 'testuser123')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'login' => 'testuser123',
            ]);
        }

        //Seeder pour insérer les objets connectés
        $this->call([
            CityZoneSeeder::class,
            ConnectedObjectSeeder::class,
            // UserSeeder::class,
        ]);
    }
}