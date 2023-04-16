<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AccessToken;
use App\Models\ClientApp;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();
        
        // Créer 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->id = $faker->uuid();
            $user->first_name = $faker->firstName();
            $user->last_name = $faker->lastName();
            $user->email = $faker->unique()->safeEmail();
            $user->password = bcrypt('password'); // mot de passe pour tous les utilisateurs
            $user->save();
        }
        
        // Créer 3 applications clientes
        for ($i = 0; $i < 3; $i++) {
            $client = new ClientApp();
            $client->id = $faker->uuid();
            $client->name = $faker->company();
            $client->return_url = $faker->url();
            $client->public_key = md5(uniqid('', true));
            $client->secret_key = md5(uniqid('', true));
            $client->save();
        }
        
        // Pour chaque utilisateur, créer un jeton d'accès pour chaque application cliente
        $users = User::all();
        $clients = ClientApp::all();
        
        foreach ($users as $user) {
            foreach ($clients as $client) {
                $accessToken = new AccessToken();
                $accessToken->client_id = $client->id;
                $accessToken->user_id = $user->id;
                $accessToken->id = $faker->uuid();
                $accessToken->expires_at = $faker->dateTimeBetween('+1 day', '+1 week');
                $accessToken->save();
            }
        }
    
    }
}
