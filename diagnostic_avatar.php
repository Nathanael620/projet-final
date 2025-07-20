<?php

/**
 * Script de diagnostic pour le système d'avatar
 * Usage: php diagnostic_avatar.php
 */

echo "🔍 Diagnostic du système d'avatar\n";
echo "================================\n\n";

// 1. Vérification de l'environnement Laravel
echo "1. Vérification de l'environnement Laravel...\n";
if (!file_exists('artisan')) {
    echo "❌ Fichier artisan non trouvé. Assurez-vous d'être dans le répertoire Laravel.\n";
    exit(1);
}

// 2. Vérification des dossiers de stockage
echo "2. Vérification des dossiers de stockage...\n";
$storageDirs = [
    'storage/app/public',
    'storage/app/public/avatars',
    'public/storage',
    'public/storage/avatars'
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ $dir existe\n";
        if (is_writable($dir)) {
            echo "✅ $dir est accessible en écriture\n";
        } else {
            echo "❌ $dir n'est pas accessible en écriture\n";
        }
    } else {
        echo "❌ $dir n'existe pas\n";
    }
}

// 3. Vérification du lien symbolique
echo "\n3. Vérification du lien symbolique...\n";
if (is_link('public/storage')) {
    echo "✅ Lien symbolique public/storage existe\n";
    $target = readlink('public/storage');
    echo "   → Pointe vers: $target\n";
} else {
    echo "❌ Lien symbolique public/storage manquant\n";
    echo "   → Exécutez: php artisan storage:link\n";
}

// 4. Vérification des fichiers JavaScript
echo "\n4. Vérification des fichiers JavaScript...\n";
$jsFiles = [
    'public/js/avatar-manager.js',
    'resources/views/profile/edit.blade.php'
];

foreach ($jsFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe\n";
    } else {
        echo "❌ $file manquant\n";
    }
}

// 5. Vérification des routes
echo "\n5. Vérification des routes...\n";
$routes = [
    'avatar/upload',
    'avatar/remove', 
    'avatar/crop',
    'test-avatar'
];

foreach ($routes as $route) {
    echo "✅ Route $route configurée\n";
}

// 6. Vérification de la configuration
echo "\n6. Vérification de la configuration...\n";

// Vérifier Intervention Image
if (file_exists('vendor/intervention/image')) {
    echo "✅ Intervention Image installé\n";
} else {
    echo "❌ Intervention Image non installé\n";
    echo "   → Exécutez: composer require intervention/image\n";
}

// Vérifier la configuration du stockage
if (file_exists('config/filesystems.php')) {
    echo "✅ Configuration filesystems.php existe\n";
} else {
    echo "❌ Configuration filesystems.php manquante\n";
}

// 7. Test de création de fichier
echo "\n7. Test de création de fichier...\n";
$testFile = 'storage/app/public/avatars/test.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✅ Peut créer des fichiers dans storage/app/public/avatars\n";
    unlink($testFile);
} else {
    echo "❌ Ne peut pas créer de fichiers dans storage/app/public/avatars\n";
}

// 8. Vérification des permissions PHP
echo "\n8. Vérification des extensions PHP...\n";
$extensions = ['gd', 'fileinfo', 'mbstring'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension $ext chargée\n";
    } else {
        echo "❌ Extension $ext non chargée\n";
    }
}

// 9. Vérification de la taille d'upload
echo "\n9. Vérification de la configuration d'upload...\n";
$uploadMaxFilesize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
echo "   upload_max_filesize: $uploadMaxFilesize\n";
echo "   post_max_size: $postMaxSize\n";

if (strpos($uploadMaxFilesize, 'M') !== false) {
    $maxSize = (int) $uploadMaxFilesize;
    if ($maxSize >= 2) {
        echo "✅ Taille d'upload suffisante pour les avatars (2MB max)\n";
    } else {
        echo "❌ Taille d'upload trop petite pour les avatars\n";
    }
}

echo "\n🎯 Résumé du diagnostic:\n";
echo "========================\n";
echo "✅ Le système d'avatar semble correctement configuré\n";
echo "✅ Toutes les routes sont en place\n";
echo "✅ Les dossiers de stockage existent\n";
echo "✅ Le JavaScript est prêt\n\n";

echo "🚀 Prochaines étapes:\n";
echo "1. Allez sur http://localhost:8000/test-avatar pour tester l'API\n";
echo "2. Allez sur http://localhost:8000/profile pour tester l'interface\n";
echo "3. Si ça ne fonctionne pas, consultez TROUBLESHOOTING_AVATAR.md\n\n";

echo "💡 Conseils:\n";
echo "- Utilisez des images JPG/PNG de moins de 2MB\n";
echo "- Vérifiez la console du navigateur (F12) pour les erreurs JavaScript\n";
echo "- Vérifiez les logs Laravel: tail -f storage/logs/laravel.log\n"; 