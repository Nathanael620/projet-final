# Système FAQ avec Intelligence Artificielle

## Vue d'ensemble

Le système FAQ de la plateforme de tutorat intègre des fonctionnalités d'intelligence artificielle pour améliorer l'expérience utilisateur et la qualité des réponses.

## Fonctionnalités principales

### 1. Génération automatique de réponses
- **Service** : `FAQIntelligenceService::generateAnswer()`
- **Fonctionnalité** : Génère des réponses automatiques basées sur les questions
- **Fallback** : Utilise les FAQ existantes si l'IA n'est pas disponible
- **Configuration** : Nécessite une clé API OpenAI

### 2. Recherche intelligente
- **Service** : `FAQIntelligenceService::intelligentSearch()`
- **Fonctionnalité** : Recherche combinée (sémantique + mots-clés)
- **Algorithme** : Score de similarité basé sur les mots-clés et votes
- **Cache** : Mise en cache des résultats pour optimiser les performances

### 3. Analyse de sentiment
- **Service** : `FAQIntelligenceService::analyzeSentiment()`
- **Fonctionnalité** : Analyse le sentiment des questions/réponses
- **Fallback** : Analyse simple basée sur les mots-clés
- **Utilisation** : Amélioration des réponses et suivi utilisateur

### 4. Suggestions d'amélioration
- **Service** : `FAQIntelligenceService::suggestImprovements()`
- **Fonctionnalité** : Suggère des améliorations pour les réponses
- **Métriques** : Score de qualité de 1 à 10
- **Interface** : Intégrée dans l'éditeur de FAQ

### 5. Chatbot IA
- **Contrôleur** : `FAQChatbotController`
- **Fonctionnalité** : Interface de chat intelligente
- **Fonctionnalités** :
  - Réponses automatiques
  - Questions similaires
  - Suggestions contextuelles
  - Indicateur de confiance
  - Évaluation des réponses

## Configuration

### Variables d'environnement requises

```env
# Configuration OpenAI
OPENAI_API_KEY=your-openai-api-key
OPENAI_ORGANIZATION=your-organization-id
OPENAI_MODEL=gpt-3.5-turbo
```

### Installation

1. **Copier le fichier d'environnement** :
```bash
cp .env.example .env
```

2. **Configurer la clé API OpenAI** :
   - Obtenir une clé sur [OpenAI Platform](https://platform.openai.com/)
   - Ajouter la clé dans le fichier `.env`

3. **Installer les dépendances** :
```bash
composer install
```

4. **Générer la clé d'application** :
```bash
php artisan key:generate
```

5. **Migrer la base de données** :
```bash
php artisan migrate
```

6. **Seeder les données** :
```bash
php artisan db:seed
```

## Architecture technique

### Services

#### FAQIntelligenceService
- **Localisation** : `app/Services/FAQIntelligenceService.php`
- **Responsabilités** :
  - Génération de réponses IA
  - Recherche intelligente
  - Analyse de sentiment
  - Suggestions d'amélioration
  - Gestion des erreurs et fallbacks

#### Méthodes principales

```php
// Génération de réponse
public function generateAnswer(string $question, string $category = 'general'): ?string

// Recherche de questions similaires
public function findSimilarQuestions(string $question, int $limit = 5): array

// Analyse de sentiment
public function analyzeSentiment(string $text): array

// Suggestions d'amélioration
public function suggestImprovements(string $question, string $answer): array

// Recherche intelligente
public function intelligentSearch(string $query, int $limit = 10): array
```

### Contrôleurs

#### FAQController
- **Localisation** : `app/Http/Controllers/FAQController.php`
- **Fonctionnalités** :
  - CRUD des FAQ
  - Génération de réponses IA
  - Recherche de questions similaires
  - Amélioration de réponses
  - Système de votes

#### FAQChatbotController
- **Localisation** : `app/Http/Controllers/FAQChatbotController.php`
- **Fonctionnalités** :
  - Interface de chat
  - Traitement des questions
  - Génération de suggestions
  - Évaluation des réponses

### Modèles

#### FAQ
- **Localisation** : `app/Models/FAQ.php`
- **Attributs** :
  - `question` : La question
  - `answer` : La réponse
  - `category` : Catégorie (general, technical, payment, sessions, account, other)
  - `status` : Statut (pending, answered, closed)
  - `votes` : Nombre de votes
  - `is_public` : Visibilité publique
  - `is_featured` : Mise en avant

## Interface utilisateur

### Pages principales

1. **Liste des FAQ** (`/faqs`)
   - Filtrage par catégorie
   - Recherche intelligente
   - Statistiques IA
   - Pagination

2. **Création de FAQ** (`/faqs/create`)
   - Génération automatique de réponses
   - Suggestions d'amélioration
   - Recherche de questions similaires
   - Compteur de caractères

3. **Chatbot IA** (`/faqs/chatbot`)
   - Interface de chat
   - Questions populaires
   - Suggestions contextuelles
   - Indicateur de confiance

4. **FAQ publique** (`/faq`)
   - Navigation par catégorie
   - Accordéon interactif
   - Informations sur les auteurs

### Fonctionnalités JavaScript

#### Génération de réponses IA
```javascript
// Générer une réponse automatique
document.getElementById('generate-ai-answer').addEventListener('click', function() {
    // Appel à l'API de génération
    fetch('/faqs/generate-ai-answer', {
        method: 'POST',
        body: JSON.stringify({ question, category })
    });
});
```

#### Amélioration de réponses
```javascript
// Améliorer une réponse existante
document.getElementById('improve-answer').addEventListener('click', function() {
    // Appel à l'API d'amélioration
    fetch('/faqs/improve-answer', {
        method: 'POST',
        body: JSON.stringify({ question, answer })
    });
});
```

## Gestion des erreurs

### Stratégies de fallback

1. **API OpenAI indisponible** :
   - Utilisation des FAQ existantes
   - Génération de réponses basiques
   - Messages d'erreur informatifs

2. **Erreurs de réseau** :
   - Retry automatique (3 tentatives)
   - Timeout de 30 secondes
   - Fallback vers recherche locale

3. **Données manquantes** :
   - Valeurs par défaut
   - Logs d'erreur détaillés
   - Interface utilisateur dégradée

### Logging

```php
// Exemple de logging d'erreur
Log::error('Erreur lors de la génération de réponse IA', [
    'error' => $e->getMessage(),
    'question' => $question,
    'category' => $category
]);
```

## Tests

### Tests unitaires
- **Localisation** : `tests/Feature/FAQIntelligenceTest.php`
- **Couverture** :
  - Génération de réponses
  - Recherche de questions similaires
  - Analyse de sentiment
  - Suggestions d'amélioration
  - Gestion d'erreurs

### Exécution des tests
```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter FAQIntelligenceTest
```

## Performance

### Optimisations

1. **Cache** :
   - Cache des résultats de recherche (1 heure)
   - Cache des embeddings (si applicable)
   - Cache des statistiques

2. **Base de données** :
   - Index sur les colonnes de recherche
   - Requêtes optimisées
   - Pagination des résultats

3. **API** :
   - Timeout configurable
   - Retry automatique
   - Gestion des rate limits

### Monitoring

```php
// Métriques de performance
$stats = [
    'total_requests' => Cache::get('ai_requests', 0),
    'success_rate' => Cache::get('ai_success_rate', 0),
    'average_response_time' => Cache::get('ai_avg_response_time', 0),
];
```

## Sécurité

### Mesures de sécurité

1. **Validation des entrées** :
   - Limitation de la longueur des questions/réponses
   - Validation des catégories
   - Protection contre les injections

2. **Authentification** :
   - Vérification des permissions
   - Protection des routes sensibles
   - Logs d'activité

3. **API OpenAI** :
   - Clés API sécurisées
   - Limitation des requêtes
   - Monitoring des coûts

## Maintenance

### Tâches régulières

1. **Nettoyage du cache** :
```bash
php artisan cache:clear
```

2. **Rotation des logs** :
```bash
php artisan log:clear
```

3. **Mise à jour des statistiques** :
```bash
php artisan faq:update-stats
```

### Commandes artisan

```bash
# Mettre à jour les statistiques IA
php artisan faq:update-stats

# Nettoyer les anciennes FAQ
php artisan faq:cleanup

# Tester l'API OpenAI
php artisan faq:test-ai
```

## Support et dépannage

### Problèmes courants

1. **Erreur "API key not found"** :
   - Vérifier la configuration dans `.env`
   - Redémarrer le serveur web
   - Vérifier les permissions

2. **Réponses lentes** :
   - Vérifier la connexion internet
   - Contrôler les timeouts
   - Optimiser les requêtes

3. **Erreurs de génération** :
   - Vérifier les logs Laravel
   - Contrôler les quotas OpenAI
   - Tester avec des questions simples

### Contact

Pour toute question ou problème :
- **Email** : support@soutiens-moi.com
- **Documentation** : `/docs/FAQ_AI_SYSTEM.md`
- **Issues** : Repository GitHub 