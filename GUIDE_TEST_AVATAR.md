# 🧪 Guide de Test - Système d'Avatar

## 🎯 Étapes de Test pour Diagnostiquer le Problème

### Étape 1 : Test de l'API Directement

1. **Allez sur la page de test simple** :
   ```
   http://localhost:8000/test-simple-avatar
   ```

2. **Sélectionnez une image** (JPG, PNG, GIF, max 2MB)

3. **Cliquez sur "Tester Upload"**

4. **Vérifiez le résultat** :
   - ✅ **Succès** : L'API fonctionne, le problème est dans l'interface
   - ❌ **Erreur** : Le problème est dans l'API

---

### Étape 2 : Test de l'Interface Utilisateur

1. **Allez sur votre profil** :
   ```
   http://localhost:8000/profile
   ```

2. **Ouvrez les outils de développement** (F12)

3. **Allez dans l'onglet "Console"**

4. **Essayez de changer votre photo** :
   - Cliquez sur "Changer la photo"
   - Sélectionnez une image
   - Regardez s'il y a des erreurs dans la console

---

### Étape 3 : Vérification des Erreurs

#### Si vous voyez des erreurs dans la console :

**Erreur CSRF** :
```
419 CSRF token mismatch
```
→ Vérifiez que `<meta name="csrf-token">` est présent dans le layout

**Erreur JavaScript** :
```
Uncaught TypeError: Cannot read property 'addEventListener' of null
```
→ Le fichier `avatar-manager.js` n'est pas chargé correctement

**Erreur 404** :
```
404 Not Found /avatar/upload
```
→ Les routes ne sont pas correctement configurées

---

### Étape 4 : Tests de Diagnostic

#### Test A : Vérification des Routes
```bash
php artisan route:list | findstr avatar
```
Vous devriez voir :
- `POST avatar/upload`
- `DELETE avatar/remove`
- `GET avatar/{userId?}`
- `GET test-simple-avatar`

#### Test B : Vérification du JavaScript
1. Ouvrez les outils de développement (F12)
2. Allez dans l'onglet "Network"
3. Rechargez la page `/profile`
4. Vérifiez que `avatar-manager.js` est chargé (statut 200)

#### Test C : Vérification du Stockage
```bash
# Vérifiez que le dossier existe
dir storage\app\public\avatars

# Vérifiez le lien symbolique
dir public\storage
```

---

### Étape 5 : Solutions par Type de Problème

#### 🚨 Problème : L'API ne fonctionne pas

**Symptômes** :
- Erreur 500 sur `/avatar/upload`
- Message d'erreur dans la réponse JSON

**Solutions** :
1. Vérifiez les logs Laravel :
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Vérifiez Intervention Image :
   ```bash
   composer show intervention/image
   ```

3. Vérifiez les permissions :
   ```bash
   chmod -R 755 storage/app/public/avatars
   ```

#### 🚨 Problème : L'interface ne fonctionne pas

**Symptômes** :
- Aucune réaction quand vous cliquez sur "Changer la photo"
- Erreurs JavaScript dans la console

**Solutions** :
1. Vérifiez que le JavaScript est chargé :
   ```html
   <!-- Dans la page, vérifiez que cette ligne est présente -->
   <script src="{{ asset('js/avatar-manager.js') }}"></script>
   ```

2. Vérifiez que Bootstrap est chargé :
   ```html
   <!-- Bootstrap doit être disponible pour les modals -->
   ```

3. Vérifiez les erreurs de console (F12)

#### 🚨 Problème : L'image s'upload mais ne s'affiche pas

**Symptômes** :
- Upload réussi (message de succès)
- L'image ne s'affiche pas dans l'interface

**Solutions** :
1. Vérifiez l'URL de l'avatar :
   ```php
   // Dans la vue, ajoutez temporairement :
   {{ dd($user->avatar) }}
   {{ dd(Storage::url($user->avatar)) }}
   ```

2. Vérifiez le lien symbolique :
   ```bash
   php artisan storage:link
   ```

3. Vérifiez que le fichier existe :
   ```bash
   dir storage\app\public\avatars
   ```

---

### Étape 6 : Test Complet

#### Test 1 : Upload Simple
1. Allez sur `http://localhost:8000/test-simple-avatar`
2. Sélectionnez une image de test (1MB max)
3. Cliquez sur "Tester Upload"
4. Vérifiez le résultat

#### Test 2 : Interface Complète
1. Allez sur `http://localhost:8000/profile`
2. Connectez-vous si nécessaire
3. Cliquez sur "Changer la photo"
4. Sélectionnez une image
5. Vérifiez que l'image s'affiche

#### Test 3 : Suppression
1. Si vous avez un avatar, cliquez sur "Supprimer"
2. Vérifiez que l'avatar par défaut s'affiche

---

### 🎯 Résumé des Points de Vérification

1. ✅ **Routes** : `php artisan route:list | findstr avatar`
2. ✅ **JavaScript** : Vérifiez la console (F12)
3. ✅ **API** : Testez sur `/test-simple-avatar`
4. ✅ **Stockage** : Vérifiez les dossiers et permissions
5. ✅ **Lien symbolique** : `php artisan storage:link`
6. ✅ **Logs** : `tail -f storage/logs/laravel.log`

---

### 📞 Si Rien Ne Fonctionne

1. **Collectez les informations** :
   - Capturez d'écran des erreurs console
   - Copiez les logs Laravel
   - Notez les étapes exactes qui échouent

2. **Testez avec une image simple** :
   - Format JPG
   - Taille < 1MB
   - Résolution 100x100 pixels

3. **Vérifiez l'environnement** :
   - Version PHP : `php --version`
   - Version Laravel : `php artisan --version`
   - Extensions PHP : `php -m | findstr gd`

**Le système est conçu pour être robuste. En suivant ces étapes, vous devriez identifier et résoudre le problème !** 🚀 