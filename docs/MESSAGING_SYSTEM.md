# Système de Messagerie - Documentation Complète

## Vue d'ensemble

Le système de messagerie de la plateforme de tutorat offre une expérience de communication moderne et complète entre étudiants et tuteurs. Il inclut des fonctionnalités avancées comme les messages en temps réel, la gestion des fichiers, les notifications push, et une interface utilisateur intuitive.

## Fonctionnalités principales

### 1. Messagerie en temps réel
- **Actualisation automatique** : Vérification des nouveaux messages toutes les 5 secondes
- **Notifications push** : Alertes instantanées pour les nouveaux messages
- **Indicateurs de statut** : Messages lus/non lus avec horodatage
- **Typing indicators** : Indicateurs de frappe (en développement)

### 2. Gestion des fichiers
- **Support des images** : Upload et affichage d'images dans les conversations
- **Documents** : Partage de fichiers PDF, DOC, TXT, etc.
- **Prévisualisation** : Aperçu des fichiers avant envoi
- **Téléchargement sécurisé** : Accès contrôlé aux fichiers partagés

### 3. Interface utilisateur moderne
- **Design responsive** : Interface adaptée mobile et desktop
- **Recherche intelligente** : Recherche en temps réel dans les conversations
- **Filtres avancés** : Tri par statut, type d'utilisateur, date
- **Actions rapides** : Marquer comme lu, supprimer, partager

### 4. Notifications
- **Notifications email** : Emails automatiques pour les messages non lus
- **Notifications push** : Alertes navigateur en temps réel
- **Préférences utilisateur** : Contrôle des types de notifications
- **Statistiques** : Compteurs de messages et conversations

## Architecture technique

### Contrôleurs

#### MessageController
```php
// Méthodes principales
- index() : Liste des conversations
- show() : Affichage d'une conversation
- store() : Envoi d'un message (AJAX)
- getNewMessages() : Récupération des nouveaux messages
- markAsRead() : Marquer comme lu
- search() : Recherche dans les messages
- destroy() : Supprimer un message
- downloadFile() : Télécharger un fichier
- getStats() : Statistiques de messagerie
```

### Modèles

#### Message
```php
// Attributs
- sender_id : ID de l'expéditeur
- receiver_id : ID du destinataire
- content : Contenu du message
- type : Type (text, file, image)
- file_path : Chemin du fichier (optionnel)
- is_read : Statut de lecture
- read_at : Horodatage de lecture

// Relations
- sender() : Relation vers l'expéditeur
- receiver() : Relation vers le destinataire

// Méthodes utilitaires
- markAsRead() : Marquer comme lu
- getTypeIcon() : Icône du type
- getFormattedContent() : Contenu formaté
```

#### User (extensions)
```php
// Méthodes ajoutées
- getUnreadMessagesCount() : Nombre de messages non lus
- sentMessages() : Messages envoyés
- receivedMessages() : Messages reçus
```

### Services

#### MessageNotificationService
```php
// Fonctionnalités
- notifyNewMessage() : Envoi de notifications
- isUserOnline() : Vérification statut en ligne
- sendRealTimeNotification() : Notifications temps réel
- sendEmailNotification() : Notifications email
- getUserMessageStats() : Statistiques utilisateur
- cleanupOldNotifications() : Nettoyage anciens messages
```

### Événements

#### NewMessageReceived
```php
// Broadcasting
- Channel : messages.{user_id}
- Event : new-message
- Data : Message complet avec métadonnées
```

## Routes

### Routes principales
```php
// Liste des conversations
GET /messages → messages.index

// Conversation spécifique
GET /messages/{user} → messages.show

// Envoi de message
POST /messages/{user} → messages.store

// Nouveaux messages (AJAX)
GET /messages/{user}/new → messages.new

// Marquer comme lu
POST /messages/{user}/read → messages.read

// Recherche
POST /messages/search → messages.search

// Supprimer message
DELETE /messages/{message} → messages.destroy

// Télécharger fichier
GET /messages/{message}/download → messages.download

// Statistiques
GET /messages/stats → messages.stats
```

## Interface utilisateur

### Vue principale (messages.index)
- **Barre de recherche** : Recherche en temps réel
- **Filtres** : Par statut, type d'utilisateur, date
- **Liste des conversations** : Avec aperçu et compteurs
- **Statistiques** : Vue d'ensemble des activités
- **Actions rapides** : Marquer comme lu, ouvrir conversation

### Vue conversation (messages.show)
- **En-tête** : Informations sur l'utilisateur
- **Zone de messages** : Affichage chronologique
- **Formulaire d'envoi** : Avec support fichiers
- **Sidebar** : Profil, actions rapides, séances communes
- **Modal images** : Affichage plein écran des images

### Composants réutilisables
- **message.blade.php** : Affichage d'un message
- **Conversation item** : Élément de liste de conversation
- **File preview** : Aperçu des fichiers

## Fonctionnalités JavaScript

### Gestion des messages
```javascript
// Envoi de message
function sendMessage() {
    // Validation et envoi AJAX
    // Mise à jour interface
    // Scroll automatique
}

// Vérification nouveaux messages
function checkNewMessages() {
    // Polling toutes les 5 secondes
    // Ajout automatique si nouveaux
    // Notification sonore
}

// Recherche en temps réel
function searchMessages(query) {
    // Debounce 500ms
    // Requête AJAX
    // Affichage résultats
}
```

### Gestion des fichiers
```javascript
// Upload de fichier
function handleFileUpload(file) {
    // Validation type/taille
    // Prévisualisation
    // Envoi avec message
}

// Téléchargement
function downloadFile(messageId) {
    // Redirection vers route de téléchargement
}
```

## Notifications

### Types de notifications
1. **Temps réel** : Pour utilisateurs en ligne
2. **Email** : Pour utilisateurs hors ligne
3. **Push** : Notifications navigateur

### Configuration email
```php
// Template : resources/views/emails/new-message.blade.php
// Inclut : Contenu du message, informations expéditeur, statistiques
// Actions : Bouton de réponse directe
```

## Sécurité

### Contrôles d'accès
- **Authentification requise** : Toutes les routes protégées
- **Vérification propriétaire** : Seul l'expéditeur peut supprimer
- **Validation fichiers** : Types et tailles autorisés
- **Sanitisation contenu** : Protection XSS

### Gestion des fichiers
- **Stockage sécurisé** : Dossier public/messages
- **Noms uniques** : Timestamp + random string
- **Accès contrôlé** : Vérification participation conversation
- **Nettoyage automatique** : Suppression anciens fichiers

## Performance

### Optimisations
- **Cache des statistiques** : 5 minutes de cache
- **Pagination** : Limitation des résultats
- **Indexation** : Index sur sender_id, receiver_id, created_at
- **Eager loading** : Relations chargées en une requête

### Monitoring
- **Logs détaillés** : Toutes les actions importantes
- **Métriques** : Temps de réponse, nombre de messages
- **Erreurs** : Capture et log des exceptions

## Tests

### Commande de test
```bash
# Statistiques utilisateur
php artisan messages:test --user-id=1 --action=stats

# Envoi de message test
php artisan messages:test --user-id=1 --action=send

# Test notifications
php artisan messages:test --user-id=1 --action=notifications

# Nettoyage anciens messages
php artisan messages:test --action=cleanup

# Conversations récentes
php artisan messages:test --user-id=1 --action=conversations
```

## Configuration

### Variables d'environnement
```env
# Taille max des fichiers (10MB)
MESSAGE_FILE_MAX_SIZE=10240

# Types de fichiers autorisés
MESSAGE_ALLOWED_FILE_TYPES=image/*,.pdf,.doc,.docx,.txt,.zip,.rar

# Intervalle de vérification (5 secondes)
MESSAGE_CHECK_INTERVAL=5000

# Durée de cache des statistiques (5 minutes)
MESSAGE_STATS_CACHE_TTL=300
```

### Configuration broadcasting
```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true,
    ],
],
```

## Maintenance

### Tâches planifiées
```php
// Nettoyage automatique des anciens messages
$schedule->daily()->call(function () {
    app(MessageNotificationService::class)->cleanupOldNotifications();
});

// Mise à jour des statistiques
$schedule->hourly()->call(function () {
    // Mise à jour cache statistiques
});
```

### Logs et monitoring
- **Logs d'activité** : Tous les envois et réceptions
- **Logs d'erreur** : Échecs d'envoi, problèmes fichiers
- **Métriques** : Nombre de messages par jour, utilisateurs actifs

## Améliorations futures

### Fonctionnalités prévues
1. **Messages vocaux** : Enregistrement et partage audio
2. **Réactions** : Emojis et réactions aux messages
3. **Messages éphémères** : Auto-destruction après lecture
4. **Groupes** : Conversations à plusieurs
5. **Statuts** : Disponible, occupé, absent
6. **Messages programmés** : Envoi différé
7. **Modération** : Filtrage automatique contenu inapproprié

### Optimisations techniques
1. **WebSockets** : Remplacement du polling par WebSockets
2. **Compression** : Compression des images automatique
3. **CDN** : Distribution des fichiers via CDN
4. **Cache Redis** : Cache avancé pour les performances
5. **Queue** : Traitement asynchrone des notifications

## Support et dépannage

### Problèmes courants
1. **Messages non reçus** : Vérifier les logs et la configuration email
2. **Fichiers non uploadés** : Vérifier permissions et configuration
3. **Notifications manquées** : Vérifier configuration broadcasting
4. **Performance lente** : Vérifier indexation et cache

### Commandes utiles
```bash
# Vider le cache
php artisan cache:clear

# Vérifier les logs
tail -f storage/logs/laravel.log

# Tester les notifications
php artisan messages:test --action=notifications

# Nettoyer les anciens fichiers
php artisan messages:test --action=cleanup
```

---

**Version** : 2.0  
**Dernière mise à jour** : {{ date('d/m/Y') }}  
**Auteur** : Équipe de développement 