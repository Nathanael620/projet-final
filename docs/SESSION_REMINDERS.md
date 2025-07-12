# Système de Rappels de Séances

## Vue d'ensemble

Le système de rappels automatiques envoie des notifications par email aux participants des séances 10 minutes avant le début de chaque séance. Il gère différemment les séances en ligne et en présentiel.

## Fonctionnalités

### 🎯 Séances en ligne
- **Lien Meet automatique** : Génération d'un lien Google Meet unique
- **Code de réunion** : Affichage du code pour rejoindre la visioconférence
- **Instructions** : Conseils pour une séance en ligne réussie

### 📍 Séances en présentiel
- **Rappel du lieu** : Affichage de l'adresse de rendez-vous
- **Instructions** : Conseils pour arriver à l'heure
- **Informations pratiques** : Détails sur le lieu de la séance

## Architecture

### Commandes Artisan

#### `sessions:send-reminders`
Commande principale qui envoie automatiquement les rappels.

```bash
php artisan sessions:send-reminders
```

#### `sessions:test-reminders`
Commande de test pour vérifier le système.

```bash
# Test avec une séance existante
php artisan sessions:test-reminders

# Test vers un email spécifique
php artisan sessions:test-reminders --email=test@example.com
```

### Planification automatique

Le système est configuré pour s'exécuter automatiquement toutes les minutes via le Kernel Laravel :

```php
// app/Console/Kernel.php
$schedule->command('sessions:send-reminders')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

## Configuration

### 1. Migration de base de données

Le champ `reminder_sent` a été ajouté à la table `support_sessions` :

```sql
ALTER TABLE support_sessions ADD COLUMN reminder_sent BOOLEAN DEFAULT FALSE;
```

### 2. Configuration des emails

Assurez-vous que la configuration email est correcte dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@soutiens-moi.fr
MAIL_FROM_NAME="Soutiens-moi!"
```

## Interface d'administration

### Accès
- URL : `/admin/reminders`
- Rôle requis : `admin`

### Fonctionnalités
- **Vue d'ensemble** : Statistiques des rappels
- **Séances à rappeler** : Liste des séances nécessitant un rappel
- **Historique** : Rappels envoyés dans les 24h
- **Envoi manuel** : Bouton pour déclencher l'envoi immédiat

## Template d'email

### Structure
- **En-tête** : Titre et urgence (10 minutes)
- **Informations de séance** : Détails complets
- **Lien Meet** : Pour les séances en ligne
- **Lieu** : Pour les séances en présentiel
- **Conseils** : Recommandations pour une séance réussie

### Personnalisation
Le template s'adapte automatiquement selon :
- Le type de séance (en ligne/présentiel)
- Le rôle du destinataire (tuteur/étudiant)
- Les informations spécifiques de la séance

## Gestion des erreurs

### Logs
Les erreurs d'envoi sont loggées dans `storage/logs/laravel.log`

### Protection contre les doublons
- Le champ `reminder_sent` empêche l'envoi multiple
- La commande vérifie ce champ avant l'envoi

### Gestion des exceptions
- Les erreurs d'envoi n'interrompent pas le processus
- Chaque séance est traitée indépendamment

## Tests

### Test manuel
```bash
# Créer une séance de test
php artisan tinker
$session = App\Models\Session::where('status', 'confirmed')->first();
$session->update(['scheduled_at' => now()->addMinutes(5));

# Tester l'envoi
php artisan sessions:test-reminders
```

### Test automatique
```bash
# Exécuter la commande de rappel
php artisan sessions:send-reminders
```

## Maintenance

### Surveillance
- Vérifier les logs d'erreur régulièrement
- Surveiller l'interface d'administration
- Contrôler les statistiques d'envoi

### Optimisations
- La commande s'exécute en arrière-plan
- Protection contre les chevauchements
- Traitement par lots pour les performances

## Dépannage

### Problèmes courants

#### Emails non envoyés
1. Vérifier la configuration SMTP
2. Contrôler les logs d'erreur
3. Tester avec la commande de test

#### Rappels en double
1. Vérifier le champ `reminder_sent`
2. Contrôler la planification du cron
3. Examiner les logs de la commande

#### Liens Meet invalides
1. Vérifier la génération des codes
2. Tester les liens manuellement
3. Contrôler le format des URLs

### Commandes utiles
```bash
# Vérifier les séances à rappeler
php artisan tinker
App\Models\Session::where('status', 'confirmed')
    ->where('scheduled_at', '>', now())
    ->where('scheduled_at', '<', now()->addMinutes(10))
    ->where('reminder_sent', false)
    ->get();

# Réinitialiser un rappel
php artisan tinker
$session = App\Models\Session::find(1);
$session->update(['reminder_sent' => false]);
```

## Évolutions futures

### Fonctionnalités prévues
- **Rappels multiples** : 24h, 1h, 10min avant
- **Personnalisation** : Templates personnalisables
- **Intégration** : Slack, SMS, push notifications
- **Analytics** : Statistiques d'ouverture et de clics

### Améliorations techniques
- **Queue** : Traitement asynchrone des emails
- **Cache** : Optimisation des requêtes
- **Monitoring** : Dashboard de surveillance
- **API** : Endpoints pour les intégrations 