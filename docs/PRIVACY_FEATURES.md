# Fonctionnalités de Confidentialité

## Vue d'ensemble

La plateforme de tutorat intègre plusieurs fonctionnalités de confidentialité pour protéger les données personnelles des utilisateurs, conformément au RGPD et aux bonnes pratiques de sécurité.

## Protection du Numéro de Téléphone

### Principe de fonctionnement

Le numéro de téléphone des utilisateurs est protégé par défaut et n'est visible que dans des cas spécifiques :

1. **L'utilisateur lui-même** : Peut voir son propre numéro de téléphone
2. **Les administrateurs** : Peuvent voir tous les numéros de téléphone
3. **Autres utilisateurs** : Ne peuvent pas voir le numéro de téléphone

### Implémentation technique

#### Modèle User

Deux nouvelles méthodes ont été ajoutées au modèle `User` :

```php
/**
 * Get masked phone number for privacy
 */
public function getMaskedPhone(): string
{
    if (!$this->phone) {
        return 'Non renseigné';
    }
    
    return 'Numéro masqué pour la confidentialité';
}

/**
 * Check if phone number should be visible (only for admin or own profile)
 */
public function canViewPhone(User $viewer = null): bool
{
    // Admin can always see phone numbers
    if ($viewer && $viewer->isAdmin()) {
        return true;
    }
    
    // User can see their own phone number
    if ($viewer && $viewer->id === $this->id) {
        return true;
    }
    
    return false;
}
```

#### Utilisation dans les vues

Dans les vues Blade, utilisez cette syntaxe :

```php
@if($user->phone)
    <i class="fas fa-phone me-2"></i>
    {{ $user->canViewPhone(auth()->user()) ? $user->phone : $user->getMaskedPhone() }}
@endif
```

### Configuration

La configuration est centralisée dans `config/profile.php` :

```php
'privacy' => [
    'default_public_profile' => true,
    'show_email_to_public' => false,
    'show_phone_to_public' => false,
    'phone_visibility' => [
        'admin_can_view' => true,
        'user_can_view_own' => true,
        'others_can_view' => false,
        'masked_message' => 'Numéro masqué pour la confidentialité',
    ],
],
```

### Pages affectées

Les pages suivantes ont été mises à jour pour respecter cette confidentialité :

1. **Profil public** (`/profile/{user}`)
2. **Profil tuteur** (`/tutors/{tutor}`)
3. **Tableau de bord** (`/dashboard`)
4. **Messagerie** (`/messages/{user}`)

### Tests

Des tests automatisés vérifient le bon fonctionnement :

```php
public function test_phone_number_is_masked_for_privacy(): void
{
    $user = User::factory()->create(['phone' => '0123456789']);
    $otherUser = User::factory()->create(['phone' => '0987654321']);

    // L'utilisateur peut voir son propre numéro
    $this->assertTrue($user->canViewPhone($user));
    
    // Un autre utilisateur ne peut pas voir le numéro
    $this->assertFalse($user->canViewPhone($otherUser));
    
    // Un admin peut voir tous les numéros
    $admin = User::factory()->create(['role' => 'admin']);
    $this->assertTrue($user->canViewPhone($admin));
}
```

## Autres Fonctionnalités de Confidentialité

### Profil Public/Privé

- Les utilisateurs peuvent choisir si leur profil est public ou privé
- Les profils privés ne sont accessibles qu'aux utilisateurs autorisés

### Protection de l'Email

- L'email n'est pas affiché publiquement par défaut
- Seuls les administrateurs et l'utilisateur lui-même peuvent voir l'email

### Gestion des Sessions

- Les utilisateurs peuvent voir et déconnecter leurs sessions actives
- Historique des connexions avec IP et user agent

### Suppression de Compte

- Processus sécurisé avec confirmation par mot de passe
- Suppression définitive ou désactivation temporaire

## Conformité RGPD

### Droits des utilisateurs

1. **Droit d'accès** : Les utilisateurs peuvent voir leurs données personnelles
2. **Droit de rectification** : Modification des informations de profil
3. **Droit à l'effacement** : Suppression du compte
4. **Droit à la portabilité** : Export des données (à implémenter)

### Journalisation

- Toutes les actions sensibles sont journalisées
- Accès aux données personnelles tracés
- Tentatives d'accès non autorisées enregistrées

## Bonnes Pratiques

### Pour les développeurs

1. **Toujours vérifier les permissions** avant d'afficher des données sensibles
2. **Utiliser les méthodes du modèle** pour la logique de confidentialité
3. **Tester les cas limites** de confidentialité
4. **Documenter les changements** de politique de confidentialité

### Pour les administrateurs

1. **Former les utilisateurs** sur les paramètres de confidentialité
2. **Surveiller les tentatives d'accès** non autorisées
3. **Réviser régulièrement** les politiques de confidentialité
4. **Répondre rapidement** aux demandes de suppression de données

## Évolutions Futures

### Fonctionnalités prévues

1. **Chiffrement des données sensibles** en base de données
2. **Anonymisation automatique** des comptes inactifs
3. **Consentement granulaire** pour chaque type de données
4. **Audit trail complet** des accès aux données

### Intégrations

1. **API de confidentialité** pour les applications tierces
2. **Webhooks de confidentialité** pour les notifications
3. **Export automatisé** des données personnelles
4. **Interface d'administration** avancée pour la confidentialité 