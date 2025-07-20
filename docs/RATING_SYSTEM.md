# Système de Notation et Feedback

## Vue d'ensemble

Le système de notation de la plateforme de tutorat permet aux utilisateurs (étudiants et tuteurs) de s'évaluer mutuellement après chaque séance terminée. Ce système favorise la transparence et aide les utilisateurs à faire des choix éclairés.

## Fonctionnalités principales

### 1. Notation par étoiles
- **Système 5 étoiles** : Notation de 1 à 5 étoiles
- **Labels descriptifs** : Chaque note correspond à un niveau de satisfaction
  - 1 étoile : Très décevant
  - 2 étoiles : Décevant
  - 3 étoiles : Correct
  - 4 étoiles : Très bien
  - 5 étoiles : Excellent

### 2. Commentaires
- **Commentaires optionnels** : Maximum 1000 caractères
- **Avis anonymes** : Possibilité de rendre un commentaire anonyme
- **Modération** : Les commentaires sont visibles par tous les utilisateurs

### 3. Système bidirectionnel
- **Étudiant → Tuteur** : Évaluation de la qualité de l'enseignement
- **Tuteur → Étudiant** : Évaluation de la participation et du sérieux

### 4. Gestion des feedbacks
- **Modification** : Possible dans les 24h suivant la création
- **Suppression** : Par l'auteur ou l'administrateur
- **Historique** : Conservation de l'historique des modifications

## Structure technique

### Modèle Feedbacks

```php
class Feedbacks extends Model
{
    protected $fillable = [
        'session_id',
        'reviewer_id',      // Celui qui laisse le feedback
        'reviewed_id',      // Celui qui reçoit le feedback
        'rating',           // 1-5 étoiles
        'comment',          // Commentaire optionnel
        'type',             // 'student_to_tutor' ou 'tutor_to_student'
        'is_anonymous',     // Booléen
    ];
}
```

### Contrôleur FeedbackController

#### Méthodes principales
- `create()` : Afficher le formulaire de notation
- `store()` : Enregistrer un nouveau feedback
- `edit()` : Modifier un feedback existant
- `update()` : Mettre à jour un feedback
- `destroy()` : Supprimer un feedback
- `userFeedbacks()` : Afficher les feedbacks d'un utilisateur
- `myFeedbacks()` : Afficher ses propres feedbacks

### Vues

#### 1. Formulaire de notation (`feedback/create.blade.php`)
- Interface intuitive avec étoiles interactives
- Compteur de caractères pour les commentaires
- Option d'anonymat
- Validation en temps réel

#### 2. Avis d'un utilisateur (`feedback/user-feedbacks.blade.php`)
- Statistiques détaillées (note moyenne, nombre d'avis)
- Distribution des notes avec graphiques
- Liste paginée des feedbacks
- Actions de modification/suppression

#### 3. Mes avis (`feedback/my-feedbacks.blade.php`)
- Vue des feedbacks donnés par l'utilisateur connecté
- Gestion des modifications et suppressions
- Informations sur l'anonymat

## Règles métier

### Conditions de notation
1. **Séance terminée** : Seules les séances avec le statut 'completed' peuvent être notées
2. **Participation** : Seuls les participants de la séance peuvent noter
3. **Unicité** : Un utilisateur ne peut noter qu'une fois par séance
4. **Délai** : Pas de limite de temps pour noter une séance terminée

### Modifications
- **Délai** : 24h maximum après la création
- **Autorisation** : Seul l'auteur peut modifier son feedback
- **Impact** : La modification met à jour automatiquement la note moyenne

### Suppression
- **Auteur** : Peut supprimer ses propres feedbacks
- **Admin** : Peut supprimer n'importe quel feedback
- **Impact** : La suppression met à jour automatiquement la note moyenne

## Calcul des notes moyennes

### Méthode de calcul
```php
public function getAverageRating(): float
{
    $feedbacks = $this->receivedFeedbacks();
    if ($feedbacks->count() === 0) {
        return 0;
    }
    
    return round($feedbacks->avg('rating'), 2);
}
```

### Mise à jour automatique
- **Création** : La note moyenne est mise à jour lors de la création d'un feedback
- **Modification** : La note moyenne est recalculée lors de la modification
- **Suppression** : La note moyenne est recalculée lors de la suppression

## Interface utilisateur

### Affichage des étoiles
```php
public function getRatingStars(): string
{
    $rating = $this->rating ?? 0;
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    $stars = str_repeat('<i class="fas fa-star text-warning"></i>', $fullStars);
    if ($halfStar) {
        $stars .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    $stars .= str_repeat('<i class="far fa-star text-warning"></i>', $emptyStars);
    
    return $stars;
}
```

### Distribution des notes
- **Graphiques** : Barres de progression pour chaque niveau
- **Pourcentages** : Calcul automatique de la répartition
- **Compteurs** : Nombre d'avis par niveau

## Intégration

### Navigation
- **Menu utilisateur** : Lien "Mes avis" dans le dropdown
- **Profil public** : Bouton "Voir les avis" sur les profils
- **Séances terminées** : Bouton "Noter cette séance"

### Routes
```php
// Routes de feedback et notation
Route::get('/feedback/{session}/create', [FeedbackController::class, 'create']);
Route::post('/feedback/{session}', [FeedbackController::class, 'store']);
Route::get('/feedback/my-feedbacks', [FeedbackController::class, 'myFeedbacks']);
Route::get('/feedback/{feedback}/edit', [FeedbackController::class, 'edit']);
Route::put('/feedback/{feedback}', [FeedbackController::class, 'update']);
Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy']);
Route::get('/users/{user}/feedbacks', [FeedbackController::class, 'userFeedbacks']);
```

## Sécurité

### Validation
- **Rating** : Entier entre 1 et 5
- **Commentaire** : Maximum 1000 caractères
- **Autorisation** : Vérification des permissions à chaque action

### Protection
- **CSRF** : Protection contre les attaques CSRF
- **Autorisation** : Vérification des rôles et permissions
- **Validation** : Validation côté serveur et client

## Tests

### Seeder de test
Le `FeedbackSeeder` génère des données de test réalistes :
- Séances terminées avec feedbacks
- Commentaires variés et réalistes
- Distribution équilibrée des notes
- Mise à jour automatique des moyennes

### Commandes de test
```bash
# Générer des données de test
php artisan db:seed --class=FeedbackSeeder

# Tester le système complet
php artisan test --filter=FeedbackTest
```

## Améliorations futures

### Fonctionnalités prévues
1. **Modération** : Système de modération des commentaires
2. **Filtres** : Filtrage des feedbacks par date, note, etc.
3. **Notifications** : Alertes pour les nouveaux feedbacks
4. **Export** : Export des données de feedback
5. **Analytics** : Statistiques avancées pour les tuteurs

### Optimisations
1. **Cache** : Mise en cache des notes moyennes
2. **Indexation** : Optimisation des requêtes de base de données
3. **Pagination** : Amélioration des performances pour les grandes listes
4. **API** : Endpoints API pour les applications mobiles

## Support

Pour toute question ou problème lié au système de notation, consultez :
- La documentation technique
- Les logs d'erreur
- L'équipe de développement 