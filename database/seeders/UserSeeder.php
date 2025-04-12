<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      

        // Créer différents types d'utilisateurs
        $memberTypes = ['resident', 'visitor', 'official', 'worker'];
        $genders = ['male', 'female'];
        
        // Créer 50 utilisateurs avec des données aléatoires
        for ($i = 5; $i <= 50; $i++) {
            $points = rand(0, 300);
            $level = 'débutant';
            
            if ($points >= 200) {
                $level = 'expert';
            } elseif ($points >= 100) {
                $level = 'avancé';
            } elseif ($points >= 50) {
                $level = 'intermédiaire';
            }
            
            User::create([
                'login' => 'user' . $i,
                'name' => 'Nom' . $i,
                'firstname' => 'Prénom' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password' . $i),
                'birth_date' => Carbon::now()->subYears(rand(18, 70))->subDays(rand(0, 365)),
                'gender' => $genders[array_rand($genders)],
                'member_type' => $memberTypes[array_rand($memberTypes)],
                'profile_picture' => null,
                'last_login_date' => Carbon::now()->subDays(rand(0, 30)),
                'points' => $points,
                'login_streak' => rand(0, 30),
                'level' => $level,
                'is_admin' => false,
                'email_verified_at' => rand(0, 10) > 2 ? Carbon::now() : null,
            ]);
        }
    }
}