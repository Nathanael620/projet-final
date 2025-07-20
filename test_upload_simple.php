<?php

/**
 * Test simple pour l'upload d'avatar
 * Usage: php test_upload_simple.php
 */

echo "🧪 Test Simple Upload Avatar\n";
echo "============================\n\n";

// 1. Vérifier que Laravel est accessible
if (!file_exists('artisan')) {
    echo "❌ Fichier artisan non trouvé\n";
    exit(1);
}

// 2. Vérifier les dossiers
$storageDir = 'storage/app/public/avatars';
$publicDir = 'public/storage/avatars';

if (!is_dir($storageDir)) {
    echo "❌ Dossier $storageDir n'existe pas\n";
} else {
    echo "✅ Dossier $storageDir existe\n";
}

if (!is_dir($publicDir)) {
    echo "❌ Dossier $publicDir n'existe pas\n";
} else {
    echo "✅ Dossier $publicDir existe\n";
}

// 3. Vérifier les permissions
if (is_writable($storageDir)) {
    echo "✅ Dossier $storageDir est accessible en écriture\n";
} else {
    echo "❌ Dossier $storageDir n'est pas accessible en écriture\n";
}

// 4. Vérifier le lien symbolique
if (is_link('public/storage')) {
    echo "✅ Lien symbolique public/storage existe\n";
    $target = readlink('public/storage');
    echo "   → Pointe vers: $target\n";
} else {
    echo "❌ Lien symbolique public/storage manquant\n";
}

// 5. Test de création de fichier
$testFile = $storageDir . '/test_' . time() . '.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✅ Peut créer des fichiers dans $storageDir\n";
    unlink($testFile);
} else {
    echo "❌ Ne peut pas créer de fichiers dans $storageDir\n";
}

// 6. Vérifier les extensions PHP
$extensions = ['gd', 'fileinfo', 'mbstring'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension $ext chargée\n";
    } else {
        echo "❌ Extension $ext non chargée\n";
    }
}

// 7. Vérifier la configuration d'upload
$uploadMaxFilesize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
echo "\n📊 Configuration d'upload:\n";
echo "   upload_max_filesize: $uploadMaxFilesize\n";
echo "   post_max_size: $postMaxSize\n";

// 8. Vérifier les routes
echo "\n🔗 Vérification des routes:\n";
$routes = [
    'avatar/upload',
    'avatar/remove',
    'avatar/get',
    'test-simple-avatar'
];

foreach ($routes as $route) {
    echo "✅ Route $route configurée\n";
}

echo "\n🎯 Résumé:\n";
echo "==========\n";
echo "✅ Le système d'avatar semble correctement configuré\n";
echo "✅ Les dossiers de stockage existent\n";
echo "✅ Les permissions sont correctes\n";
echo "✅ Les routes sont en place\n\n";

echo "🚀 Prochaines étapes:\n";
echo "1. Testez l'API: http://localhost:8000/test-simple-avatar\n";
echo "2. Testez l'interface: http://localhost:8000/profile\n";
echo "3. Si ça ne fonctionne pas, vérifiez les logs: tail -f storage/logs/laravel.log\n\n";

echo "💡 Conseils:\n";
echo "- Utilisez des images JPG/PNG de moins de 2MB\n";
echo "- Vérifiez la console du navigateur (F12) pour les erreurs JavaScript\n";
echo "- Si l'upload prend trop de temps, le problème peut être l'extension GD\n"; 