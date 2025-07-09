<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des portefeuilles pour tous les utilisateurs
        $users = User::all();

        foreach ($users as $user) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => rand(0, 500), // Solde aléatoire entre 0 et 500€
                'currency' => 'EUR',
                'is_active' => true,
            ]);
        }
    }
} 