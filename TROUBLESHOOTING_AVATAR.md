# 🔧 Guide de Dépannage - Système d'Avatar

## 🚨 Problème : "Quand je choisis ma photo de profil ça n'apparaît pas"

### ✅ Solutions à essayer dans l'ordre :

---

## 1. 🔍 Vérification de Base

### Testez d'abord l'API directement :
1. Allez sur : `http://localhost:8000/test-avatar`
2. Sélectionnez une image
3. Cliquez sur "Upload Avatar"
4. Vérifiez si vous obtenez une réponse de succès

**Si ça fonctionne** → Le problème est dans l'interface utilisateur
**Si ça ne fonctionne pas** → Le problème est dans l'API

---

## 2. 🛠️ Problèmes Courants et Solutions

### A. Problème : Erreur CSRF
**Symptômes** : Erreur 419 ou "CSRF token mismatch"

**Solution** :
```html
<!-- Vérifiez que cette ligne est présente dans votre layout -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### B. Problème : Fichier JavaScript non chargé
**Symptômes** : Aucune réaction quand vous cliquez sur "Changer la photo"

**Solution** :
1. Ouvrez les outils de développement (F12)
2. Allez dans l'onglet "Console"
3. Vérifiez s'il y a des erreurs JavaScript
4. Vérifiez que `avatar-manager.js` est bien chargé

### C. Problème : Permissions de stockage
**Symptômes** : Erreur lors de l'upload

**Solution** :
```bash
# Vérifiez les permissions
chmod -R 755 storage/app/public/avatars
chmod -R 755 public/storage
```

### D. Problème : Lien symbolique manquant
**Symptômes** : Images non affichées après upload

**Solution** :
```bash
php artisan storage:link
```

---

## 3. 🔧 Debug Détaillé

### Étape 1 : Vérifiez les logs Laravel
```bash
tail -f storage/logs/laravel.log
```

### Étape 2 : Vérifiez la console du navigateur
1. Ouvrez les outils de développement (F12)
2. Allez dans l'onglet "Console"
3. Regardez s'il y a des erreurs JavaScript

### Étape 3 : Vérifiez le réseau
1. Ouvrez les outils de développement (F12)
2. Allez dans l'onglet "Network"
3. Essayez d'uploader une image
4. Vérifiez la requête `/avatar/upload`

---

## 4. 🧪 Tests de Diagnostic

### Test 1 : Vérification de l'API
```bash
# Testez l'endpoint directement
curl -X POST http://localhost:8000/avatar/upload \
  -H "Content-Type: multipart/form-data" \
  -F "avatar=@test-image.jpg" \
  -H "X-CSRF-TOKEN: YOUR_TOKEN"
```

### Test 2 : Vérification du stockage
```bash
# Vérifiez que le dossier existe
ls -la storage/app/public/avatars

# Vérifiez le lien symbolique
ls -la public/storage
```

### Test 3 : Vérification de la base de données
```sql
-- Vérifiez si l'avatar est bien enregistré
SELECT id, name, avatar FROM users WHERE id = YOUR_USER_ID;
```

---

## 5. 🎯 Solutions Spécifiques

### Si l'upload fonctionne mais l'image ne s'affiche pas :

1. **Vérifiez l'URL de l'avatar** :
   ```php
   // Dans la vue, ajoutez temporairement :
   {{ dd($user->avatar) }}
   {{ dd(Storage::url($user->avatar)) }}
   ```

2. **Vérifiez le stockage** :
   ```bash
   # Vérifiez si le fichier existe
   ls -la storage/app/public/avatars/
   ls -la public/storage/avatars/
   ```

### Si l'upload ne fonctionne pas du tout :

1. **Vérifiez les routes** :
   ```bash
   php artisan route:list | grep avatar
   ```

2. **Vérifiez les permissions** :
   ```bash
   # Donnez les bonnes permissions
   chmod -R 755 storage/
   chmod -R 755 public/storage/
   ```

3. **Vérifiez la configuration** :
   ```bash
   # Vérifiez que Intervention Image est bien installé
   composer show intervention/image
   ```

---

## 6. 🚀 Solutions Avancées

### Si rien ne fonctionne, essayez cette approche :

1. **Réinstallez les dépendances** :
   ```bash
   composer install --no-dev
   ```

2. **Nettoyez le cache** :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

3. **Redémarrez le serveur** :
   ```bash
   # Arrêtez le serveur (Ctrl+C)
   # Puis relancez-le
   php artisan serve
   ```

---

## 7. 📞 Support

### Si le problème persiste :

1. **Collectez les informations** :
   - Version de Laravel : `php artisan --version`
   - Version de PHP : `php --version`
   - Logs d'erreur : `tail -n 50 storage/logs/laravel.log`
   - Erreurs console : Capturez d'écran des erreurs JavaScript

2. **Testez avec une image simple** :
   - Utilisez une image JPG de moins de 1MB
   - Testez avec une image de 100x100 pixels

3. **Vérifiez l'environnement** :
   - Extension GD ou Imagick installée
   - Permissions du serveur web
   - Configuration PHP (upload_max_filesize, post_max_size)

---

## 🎯 Résumé des Étapes de Diagnostic

1. ✅ Testez l'API directement : `http://localhost:8000/test-avatar`
2. ✅ Vérifiez les logs Laravel
3. ✅ Vérifiez la console du navigateur
4. ✅ Vérifiez les permissions de stockage
5. ✅ Vérifiez le lien symbolique
6. ✅ Testez avec une image simple
7. ✅ Redémarrez le serveur si nécessaire

**Le système d'avatar est conçu pour être robuste. Si vous suivez ces étapes, vous devriez identifier et résoudre le problème !** 🚀 