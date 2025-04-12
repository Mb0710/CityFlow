<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAction;
use App\Models\User;
use App\Models\ConnectedObject;
use Carbon\Carbon;

class UserActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si des utilisateurs et des objets connectés existent
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('Aucun utilisateur trouvé. Veuillez exécuter UserSeeder d\'abord.');
            return;
        }
        
        $connectedObjects = ConnectedObject::all();
        
        if ($connectedObjects->isEmpty()) {
            $this->command->info('Aucun objet connecté trouvé. Veuillez créer des objets connectés d\'abord.');
            return;
        }
        
        // Types d'actions disponibles
        $actionTypes = [
            'login' => [5, 'Connexion au système'],
            'scan' => [10, 'Scan d\'un objet'],
            'interaction' => [15, 'Interaction avec un objet'],
            'completion' => [25, 'Completion d\'une action'],
            'sharing' => [20, 'Partage de contenu'],
        ];
        
        // Générer des actions pour les 90 derniers jours
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        // Nombre total d'actions à générer
        $totalActions = 1000;
        
        for ($i = 0; $i < $totalActions; $i++) {
            // Choisir un utilisateur aléatoire
            $user = $users->random();
            
            // Choisir un type d'action aléatoire
            $actionType = array_rand($actionTypes);
            
            // Choisir un objet aléatoire
            $object = $connectedObjects->random();
            
            // Générer une date aléatoire dans les 90 derniers jours
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            // Créer l'action
            UserAction::create([
                'user_id' => $user->id,
                'action_type' => $actionType,
                'object_id' => $object->id,
                'description' => $actionTypes[$actionType][1] . ' ' . $object->name,
                'points' => $actionTypes[$actionType][0],
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }
        
        // Créer des actions pour chaque jour des 30 derniers jours pour certains utilisateurs actifs
        $activeUsers = $users->random(min(5, $users->count()));
        
        foreach ($activeUsers as $user) {
            for ($day = 0; $day < 30; $day++) {
                $date = Carbon::now()->subDays($day);
                $actionCount = rand(1, 5);
                
                for ($i = 0; $i < $actionCount; $i++) {
                    $actionType = array_rand($actionTypes);
                    $object = $connectedObjects->random();
                    
                    UserAction::create([
                        'user_id' => $user->id,
                        'action_type' => $actionType,
                        'object_id' => $object->id,
                        'description' => $actionTypes[$actionType][1] . ' ' . $object->name,
                        'points' => $actionTypes[$actionType][0],
                        'created_at' => $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                        'updated_at' => $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                    ]);
                }
            }
        }
    }
}