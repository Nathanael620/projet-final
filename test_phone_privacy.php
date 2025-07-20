<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test de la confidentialité du numéro de téléphone ===\n\n";

// Récupérer un utilisateur
$user = User::first();

if (!$user) {
    echo "Aucun utilisateur trouvé dans la base de données.\n";
    exit;
}

echo "Utilisateur: " . $user->name . "\n";
echo "Numéro de téléphone réel: " . ($user->phone ?: 'Non renseigné') . "\n";
echo "Numéro masqué: " . $user->getMaskedPhone() . "\n";
echo "Peut voir son propre numéro: " . ($user->canViewPhone($user) ? 'Oui' : 'Non') . "\n";

// Créer un autre utilisateur pour tester
$otherUser = User::where('id', '!=', $user->id)->first();

if ($otherUser) {
    echo "\nAutre utilisateur: " . $otherUser->name . "\n";
    echo "Peut voir le numéro de " . $user->name . ": " . ($user->canViewPhone($otherUser) ? 'Oui' : 'Non') . "\n";
}

// Créer un admin pour tester
$admin = User::where('role', 'admin')->first();

if ($admin) {
    echo "\nAdmin: " . $admin->name . "\n";
    echo "Peut voir le numéro de " . $user->name . ": " . ($user->canViewPhone($admin) ? 'Oui' : 'Non') . "\n";
}

echo "\n=== Test terminé ===\n"; 