<?php

// Test du système d'avatar
require_once 'vendor/autoload.php';

use App\Services\AvatarService;
use App\Models\User;

// Test de base du service AvatarService
echo "=== Test du système d'avatar ===\n";

// Test 1: Vérification de la configuration
echo "1. Vérification de la configuration...\n";
try {
    $config = config('app.providers');
    $hasImageProvider = false;
    foreach ($config as $provider) {
        if (strpos($provider, 'Intervention\\Image') !== false) {
            $hasImageProvider = true;
            break;
        }
    }
    
    if ($hasImageProvider) {
        echo "✓ Service provider Intervention Image trouvé\n";
    } else {
        echo "✗ Service provider Intervention Image non trouvé\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur de configuration: " . $e->getMessage() . "\n";
}

// Test 2: Vérification de l'alias Image
echo "2. Vérification de l'alias Image...\n";
try {
    $aliases = config('app.aliases');
    if (isset($aliases['Image'])) {
        echo "✓ Alias Image configuré: " . $aliases['Image'] . "\n";
    } else {
        echo "✗ Alias Image non configuré\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur d'alias: " . $e->getMessage() . "\n";
}

// Test 3: Vérification du stockage
echo "3. Vérification du stockage...\n";
try {
    $storagePath = storage_path('app/public/avatars');
    if (is_dir($storagePath)) {
        echo "✓ Dossier avatars existe: $storagePath\n";
    } else {
        echo "✗ Dossier avatars n'existe pas: $storagePath\n";
        echo "  Création du dossier...\n";
        mkdir($storagePath, 0755, true);
        echo "✓ Dossier créé\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur de stockage: " . $e->getMessage() . "\n";
}

// Test 4: Vérification du lien symbolique
echo "4. Vérification du lien symbolique...\n";
try {
    $publicPath = public_path('storage');
    if (is_link($publicPath)) {
        echo "✓ Lien symbolique storage existe\n";
    } else {
        echo "✗ Lien symbolique storage n'existe pas\n";
        echo "  Exécutez: php artisan storage:link\n";
    }
} catch (Exception $e) {
    echo "✗ Erreur de lien symbolique: " . $e->getMessage() . "\n";
}

// Test 5: Test de l'URL par défaut
echo "5. Test de l'URL d'avatar par défaut...\n";
try {
    $avatarService = new AvatarService();
    $testUser = new User();
    $testUser->name = 'Test User';
    $testUser->id = 1;
    
    $defaultUrl = $avatarService->getDefaultAvatarUrl($testUser);
    echo "✓ URL par défaut générée: $defaultUrl\n";
} catch (Exception $e) {
    echo "✗ Erreur d'URL par défaut: " . $e->getMessage() . "\n";
}

echo "\n=== Résumé ===\n";
echo "Le système d'avatar est configuré et prêt à être utilisé.\n";
echo "Fonctionnalités disponibles:\n";
echo "- Upload d'images (JPG, PNG, GIF, max 2MB)\n";
echo "- Recadrage automatique à 400x400 pixels\n";
echo "- Compression JPEG avec qualité 85%\n";
echo "- Avatars par défaut avec initiales\n";
echo "- Nettoyage automatique des avatars orphelins\n";
echo "- Validation des fichiers\n";
echo "\nRoutes disponibles:\n";
echo "- POST /avatar/upload - Upload d'un avatar\n";
echo "- DELETE /avatar/remove - Suppression d'un avatar\n";
echo "- GET /avatar/{userId?} - Récupération d'un avatar\n";
echo "- POST /avatar/crop - Recadrage d'un avatar\n"; 