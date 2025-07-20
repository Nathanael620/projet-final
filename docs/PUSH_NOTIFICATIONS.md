# Système de Notifications Push

## Vue d'ensemble

Le système de notifications push de la plateforme de tutorat permet d'envoyer des notifications en temps réel aux utilisateurs lors de différents événements liés aux séances. Ces notifications apparaissent sous forme de popups dans le navigateur et sont également stockées en base de données.

## Fonctionnalités

### Types de notifications supportés

1. **Création de séance** (`session_created_student`, `session_created_tutor`)
2. **Acceptation de séance** (`session_accepted`)
3. **Refus de séance** (`session_rejected`)
4. **Annulation de séance** (`session_cancelled`)
5. **Rappel de séance** (`session_reminder_student`, `session_reminder_tutor`)
6. **Modification de séance** (`session_updated`)
7. **Fin de séance** (`session_completed_student`, `session_completed_tutor`)

### Caractéristiques

- **Temps réel** : Vérification automatique toutes les 30 secondes
- **Animations** : Entrée et sortie fluides avec barre de progression
- **Actions** : Boutons d'action pour naviguer directement vers la séance
- **Auto-suppression** : Disparition automatique après 5 secondes
- **Marquage automatique** : Notifications marquées comme lues automatiquement

## Architecture technique

### Backend

#### Services

##### PushNotificationService
```php
class PushNotificationService
{
    // Méthodes principales
    - notifySessionCreated(Session $session)
    - notifySessionAccepted(Session $session)
    - notifySessionRejected(Session $session)
    - notifySessionCancelled(Session $session, User $cancelledBy)
    - notifySessionReminder(Session $session)
    - notifySessionUpdated(Session $session, User $updatedBy)
    - notifySessionCompleted(Session $session)
}
```

##### NotificationService
```php
class NotificationService
{
    // Méthodes principales
    - createNotification(User $user, string $type, array $data)
    - markAsRead(Notification $notification)
    - markAllAsRead(User $user)
    - getUnreadNotifications(User $user, int $limit = 10)
}
```

#### Modèle Notification
```php
class Notification extends Model
{
    protected $fillable = [
        'uuid',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}
```

#### Contrôleur API
```php
class NotificationController extends Controller
{
    // Endpoints
    - POST /api/notifications/check
    - POST /api/notifications/{notification}/mark-read
    - GET /api/notifications/unread-count
    - GET /api/notifications
    - POST /api/notifications/mark-all-read
    - DELETE /api/notifications/{notification}
    - DELETE /api/notifications/delete-read
}
```

### Frontend

#### PushNotificationManager (JavaScript)
```javascript
class PushNotificationManager {
    // Méthodes principales
    - init()
    - startPolling()
    - checkNewNotifications()
    - showNotification(notificationData)
    - dismissNotification(element)
    - handleNotificationAction(url)
    - markAsRead(notificationId)
}
```

## Intégration dans les séances

### Création de séance
```php
// Dans SessionController::store()
$this->pushNotificationService->notifySessionCreated($session);
```

### Acceptation/Refus de séance
```php
// Dans SessionController::update()
if ($request->status === 'accepted') {
    $this->pushNotificationService->notifySessionAccepted($session);
} else {
    $this->pushNotificationService->notifySessionRejected($session);
}
```

### Annulation de séance
```php
// Dans SessionController::destroy()
$this->pushNotificationService->notifySessionCancelled($session, $user);
```

## Structure des données de notification

### Format standard
```php
[
    'title' => 'Titre de la notification',
    'message' => 'Message détaillé',
    'session_id' => 123,
    'session_title' => 'Titre de la séance',
    'scheduled_at' => '15/01/2025 14:30',
    'session_url' => '/sessions/123',
    'icon' => 'fas fa-calendar-plus',
    'color' => 'success',
    'action_text' => 'Voir la séance',
    'action_url' => '/sessions/123',
]
```

### Exemples par type

#### Séance créée (Étudiant)
```php
[
    'title' => 'Séance créée avec succès',
    'message' => 'Votre demande de séance avec Jean Dupont a été créée et est en attente de validation.',
    'session_id' => 123,
    'session_title' => 'Mathématiques - Niveau débutant',
    'tutor_name' => 'Jean Dupont',
    'scheduled_at' => '15/01/2025 14:30',
    'session_url' => '/sessions/123',
    'icon' => 'fas fa-calendar-plus',
    'color' => 'success',
    'action_text' => 'Voir la séance',
    'action_url' => '/sessions/123',
]
```

#### Séance créée (Tuteur)
```php
[
    'title' => 'Nouvelle demande de séance',
    'message' => 'Marie Martin vous a demandé une séance de mathematics.',
    'session_id' => 123,
    'session_title' => 'Mathématiques - Niveau débutant',
    'student_name' => 'Marie Martin',
    'subject' => 'mathematics',
    'level' => 'beginner',
    'scheduled_at' => '15/01/2025 14:30',
    'duration' => 60,
    'price' => 20.00,
    'session_url' => '/sessions/123',
    'icon' => 'fas fa-calendar-check',
    'color' => 'info',
    'action_text' => 'Répondre à la demande',
    'action_url' => '/sessions/123',
]
```

## Configuration

### Polling
- **Intervalle** : 30 secondes par défaut
- **Configurable** : Modifiable dans `push-notifications.js`

### Affichage
- **Position** : Coin supérieur droit
- **Durée** : 5 secondes par défaut
- **Animation** : Slide depuis la droite
- **Barre de progression** : Indique le temps restant

### Couleurs par type
- **Success** : Séances créées, acceptées, terminées
- **Info** : Nouvelles demandes pour tuteurs
- **Warning** : Rappels, annulations
- **Danger** : Refus de séances

## Tests

### Tests unitaires
```bash
php artisan test --filter=PushNotificationTest
```

### Tests couverts
- Création de séance (étudiant et tuteur)
- Acceptation de séance
- Refus de séance
- Annulation de séance
- Rappel de séance
- Fin de séance

## Utilisation

### Pour les développeurs

#### Ajouter un nouveau type de notification
1. Ajouter la méthode dans `PushNotificationService`
2. Définir l'icône dans `getIconForType()`
3. Créer le test correspondant
4. Intégrer dans le contrôleur approprié

#### Personnaliser l'affichage
```javascript
// Modifier les styles dans push-notifications.js
notification.style.cssText = `
    // Styles personnalisés
`;
```

### Pour les utilisateurs

#### Fonctionnement automatique
- Les notifications apparaissent automatiquement
- Pas de configuration requise
- Compatible avec tous les navigateurs modernes

#### Actions disponibles
- **Cliquer sur l'action** : Navigation directe vers la séance
- **Cliquer sur "Ignorer"** : Fermer la notification
- **Cliquer sur "X"** : Fermer la notification
- **Attendre** : Auto-suppression après 5 secondes

## Sécurité

### Authentification
- Toutes les routes API nécessitent une authentification
- Vérification que l'utilisateur possède la notification

### Validation
- Validation des données d'entrée
- Protection CSRF sur toutes les requêtes
- Sanitisation des données affichées

### Logs
- Journalisation de toutes les notifications envoyées
- Traçabilité des erreurs
- Monitoring des performances

## Performance

### Optimisations
- Polling intelligent (seulement si l'utilisateur est actif)
- Limitation du nombre de notifications affichées
- Cache des notifications récentes
- Compression des données JSON

### Monitoring
- Temps de réponse des API
- Nombre de notifications par utilisateur
- Taux de lecture des notifications
- Erreurs de livraison

## Évolutions futures

### Fonctionnalités prévues
1. **Notifications par email** : Envoi automatique d'emails
2. **Notifications SMS** : Pour les urgences
3. **Préférences utilisateur** : Choix des types de notifications
4. **Notifications groupées** : Regroupement des notifications similaires
5. **Historique complet** : Page dédiée aux notifications

### Intégrations
1. **WebSockets** : Communication en temps réel
2. **Service Workers** : Notifications hors ligne
3. **Push API** : Notifications système
4. **Mobile** : Application mobile native

## Dépannage

### Problèmes courants

#### Notifications ne s'affichent pas
1. Vérifier que le JavaScript est chargé
2. Vérifier la console pour les erreurs
3. Vérifier que l'utilisateur est connecté
4. Vérifier les permissions du navigateur

#### Notifications en double
1. Vérifier le polling
2. Vérifier les timestamps
3. Nettoyer le cache du navigateur

#### Performance lente
1. Réduire la fréquence de polling
2. Limiter le nombre de notifications
3. Optimiser les requêtes API

### Logs utiles
```bash
# Vérifier les logs Laravel
tail -f storage/logs/laravel.log

# Vérifier les erreurs JavaScript
# Ouvrir la console du navigateur (F12)
``` 