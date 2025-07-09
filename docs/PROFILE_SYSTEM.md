# Système de Profil Utilisateur

## Vue d'ensemble

Le système de profil utilisateur de la plateforme de tutorat offre une gestion complète des informations personnelles, des paramètres de compte et des fonctionnalités spécifiques aux rôles (tuteur/étudiant).

## Fonctionnalités principales

### 1. Gestion des informations de base
- **Nom et email** : Informations d'identification principales
- **Téléphone** : Contact optionnel
- **Bio** : Description personnelle (max 1000 caractères)
- **Niveau** : Débutant, Intermédiaire, Avancé
- **Avatar** : Photo de profil avec upload et gestion automatique

### 2. Fonctionnalités spécifiques aux tuteurs
- **Compétences** : Liste de matières enseignées
- **Tarif horaire** : Prix par heure (5-200€)
- **Disponibilité** : Statut disponible/indisponible
- **Statistiques** : Séances, gains, notes moyennes

### 3. Sécurité et confidentialité
- **Changement de mot de passe** : Avec validation et conseils
- **Suppression de compte** : Processus sécurisé avec confirmation
- **Profil public/privé** : Contrôle de la visibilité

### 4. Statistiques et analytics
- **Séances totales et terminées**
- **Taux de complétion**
- **Gains totaux (tuteurs) / Dépenses (étudiants)**
- **Messages non lus**

## Structure technique

### Contrôleurs

#### ProfileController
```php
// Méthodes principales
- edit() : Affichage du formulaire de profil
- update() : Mise à jour des informations
- updateAvailability() : Gestion de la disponibilité
- updateHourlyRate() : Mise à jour du tarif
- destroy() : Suppression de compte
- show() : Affichage du profil public
```

#### AvatarController
```php
// Gestion des avatars
- upload() : Upload d'un nouvel avatar
- remove() : Suppression de l'avatar
```

### Modèles

#### User (extensions)
```php
// Nouvelles méthodes ajoutées
- getLevelText() : Texte formaté du niveau
- getSkillsBadges() : Compétences formatées
- getAvailabilityText() : Statut de disponibilité
- getTotalEarnings() : Gains totaux (tuteurs)
- getTotalSpent() : Dépenses totales (étudiants)
- getCompletionRate() : Taux de complétion
```

### Middleware

#### ProfileAccess
- Vérification des permissions d'accès aux profils
- Gestion des profils publics/privés
- Accès basé sur les relations (séances, messages)

### Validation

#### ProfileUpdateRequest
```php
// Règles de validation
- name : requis, max 255 caractères
- email : requis, unique, format email
- phone : optionnel, max 20 caractères
- bio : optionnel, max 1000 caractères
- level : requis, enum (beginner/intermediate/advanced)
- avatar : image, max 2MB, formats autorisés
- skills : tableau de compétences (tuteurs uniquement)
- hourly_rate : numérique, 5-200€ (tuteurs uniquement)
```

## Interface utilisateur

### Page de profil principal (`/profile`)
- **En-tête avec avatar et statistiques**
- **Formulaires organisés en sections**
- **Actions rapides**
- **Paramètres spécifiques aux tuteurs**

### Composants réutilisables
- `profile-stats.blade.php` : Affichage des statistiques
- `update-profile-information-form.blade.php` : Formulaire principal
- `tutor-settings.blade.php` : Paramètres tuteur
- `update-password-form.blade.php` : Changement de mot de passe
- `delete-user-form.blade.php` : Suppression de compte

### Profil public (`/profile/{user}`)
- **Informations publiques**
- **Statistiques de performance**
- **Actions disponibles** (message, demande de séance)
- **Séances récentes**

## Configuration

### Fichier de configuration (`config/profile.php`)
```php
'avatar' => [
    'max_size' => 2048,
    'allowed_types' => ['jpeg', 'png', 'jpg', 'gif'],
    'storage_path' => 'avatars',
],

'skills' => [
    'mathematics' => 'Mathématiques',
    'physics' => 'Physique',
    // ...
],

'tutor' => [
    'min_hourly_rate' => 5,
    'max_hourly_rate' => 200,
    'default_hourly_rate' => 20,
],
```

## Routes

```php
// Profil utilisateur
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::post('/profile/availability', [ProfileController::class, 'updateAvailability'])->name('profile.availability');
Route::post('/profile/hourly-rate', [ProfileController::class, 'updateHourlyRate'])->name('profile.hourly-rate');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show')->middleware('profile.access');

// Gestion des avatars
Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');
```

## JavaScript

### AvatarManager (`public/js/avatar-manager.js`)
- **Upload automatique** des avatars
- **Prévisualisation** en temps réel
- **Validation** côté client
- **Notifications** interactives

## Tests

### ProfileTest
- **Tests de fonctionnalités** principales
- **Validation** des formulaires
- **Permissions** d'accès
- **Upload** d'avatars
- **Suppression** de compte

## Sécurité

### Mesures implémentées
- **Validation** stricte des données
- **Sanitisation** des uploads
- **Permissions** granulaires
- **Confirmation** pour actions critiques
- **Protection CSRF** sur tous les formulaires

### Gestion des fichiers
- **Validation** des types MIME
- **Limitation** de taille
- **Stockage sécurisé** dans `storage/app/public/avatars`
- **Nettoyage automatique** des anciens fichiers

## Utilisation

### Pour les développeurs
1. **Configuration** : Ajuster les paramètres dans `config/profile.php`
2. **Personnalisation** : Modifier les vues dans `resources/views/profile/`
3. **Extension** : Ajouter de nouveaux champs dans le modèle User
4. **Tests** : Exécuter `php artisan test --filter=ProfileTest`

### Pour les utilisateurs
1. **Accès** : Menu "Profil" dans la navigation
2. **Modification** : Remplir les formulaires et sauvegarder
3. **Avatar** : Cliquer sur l'avatar pour le changer
4. **Sécurité** : Utiliser la section "Sécurité" pour le mot de passe

## Améliorations futures

### Fonctionnalités suggérées
- **Notifications** en temps réel
- **Export** des données personnelles
- **Intégration** avec les réseaux sociaux
- **Galerie** de photos
- **Préférences** de notification
- **Historique** des modifications
- **Backup** automatique des données

### Optimisations techniques
- **Cache** des statistiques
- **Compression** automatique des images
- **CDN** pour les avatars
- **API** REST pour les modifications
- **Webhooks** pour les événements 