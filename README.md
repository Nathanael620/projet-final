# Plateforme de Tutorat - Projet Final

Une plateforme web moderne pour connecter étudiants et tuteurs, développée avec Laravel et Bootstrap.

## 🚀 Fonctionnalités

### 👥 Gestion des utilisateurs
- **Inscription/Connexion** : Système d'authentification complet
- **Profils utilisateurs** : Gestion des profils étudiants et tuteurs
- **Rôles** : Étudiant, Tuteur, Administrateur
- **Compétences** : Gestion des compétences des tuteurs
- **Évaluations** : Système de notation et commentaires

### 📚 Gestion des séances
- **Demande de séance** : Les étudiants peuvent demander des séances de soutien
- **Acceptation/Refus** : Les tuteurs peuvent accepter ou refuser les demandes
- **Types de séances** : En ligne ou en présentiel
- **Statuts** : En attente, Acceptée, Terminée, Annulée
- **Planification** : Gestion des dates et heures de séances
- **Prix** : Calcul automatique basé sur le tarif horaire du tuteur

### 💬 Système de messagerie
- **Conversations** : Messagerie privée entre utilisateurs
- **Notifications** : Messages non lus
- **Historique** : Conservation de l'historique des conversations
- **Actions rapides** : Demande de séance depuis les messages

### 🔍 Recherche et filtres
- **Recherche de tuteurs** : Par matière, niveau, prix
- **Filtres de séances** : Par statut, matière, type
- **Tri** : Par note, disponibilité, prix

### 📊 Dashboard
- **Statistiques** : Vue d'ensemble des activités
- **Séances récentes** : Liste des dernières séances
- **Actions rapides** : Accès direct aux fonctionnalités principales

### 🛠️ Administration
- **Gestion des utilisateurs** : Vue d'ensemble des utilisateurs
- **Gestion des séances** : Supervision des séances
- **Statistiques** : Données globales de la plateforme

## 🛠️ Technologies utilisées

- **Backend** : Laravel 11 (PHP 8.2+)
- **Frontend** : Bootstrap 5, FontAwesome
- **Base de données** : MySQL/PostgreSQL
- **Authentification** : Laravel Breeze
- **Validation** : Laravel Request Validation
- **Relations** : Eloquent ORM

## 📁 Structure du projet

```
projet-final/
├── app/
│   ├── Http/Controllers/     # Contrôleurs
│   ├── Models/              # Modèles Eloquent
│   ├── Http/Middleware/     # Middleware personnalisés
│   └── Http/Requests/       # Classes de validation
├── database/
│   ├── migrations/          # Migrations de base de données
│   ├── seeders/            # Seeders pour les données de test
│   └── factories/          # Factories pour les tests
├── resources/
│   └── views/              # Vues Blade
│       ├── auth/           # Vues d'authentification
│       ├── dashboard/      # Dashboard principal
│       ├── sessions/       # Gestion des séances
│       ├── tutors/         # Profils des tuteurs
│       ├── messages/       # Système de messagerie
│       └── layouts/        # Layouts de base
├── routes/
│   └── web.php            # Routes principales
└── public/                # Assets publics
```

## 🚀 Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL/PostgreSQL
- Node.js et npm (pour les assets)

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-repo]
   cd projet-final
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurer la base de données**
   ```bash
   # Modifier .env avec vos paramètres de base de données
   php artisan migrate
   php artisan db:seed
   ```

5. **Installer les dépendances frontend**
   ```bash
   npm install
   npm run dev
   ```

6. **Démarrer le serveur**
   ```bash
   php artisan serve
   ```

## 👥 Utilisateurs par défaut

Après l'installation, les utilisateurs suivants sont créés :

### Administrateur
- Email : `admin@example.com`
- Mot de passe : `password`
- Rôle : Administrateur

### Tuteurs
- Email : `tutor1@example.com` / `tutor2@example.com`
- Mot de passe : `password`
- Rôle : Tuteur

### Étudiants
- Email : `student1@example.com` / `student2@example.com`
- Mot de passe : `password`
- Rôle : Étudiant

## 📋 Fonctionnalités détaillées

### Pour les étudiants
1. **Inscription/Connexion** : Créer un compte étudiant
2. **Recherche de tuteurs** : Parcourir et filtrer les tuteurs disponibles
3. **Demande de séance** : Réserver une séance avec un tuteur
4. **Suivi des séances** : Voir le statut et les détails des séances
5. **Messagerie** : Communiquer avec les tuteurs
6. **Évaluation** : Noter et commenter les séances terminées

### Pour les tuteurs
1. **Profil complet** : Gérer les informations personnelles et compétences
2. **Demandes de séances** : Recevoir et gérer les demandes
3. **Acceptation/Refus** : Accepter ou refuser les séances
4. **Gestion des séances** : Voir les détails et ajouter des notes
5. **Messagerie** : Communiquer avec les étudiants
6. **Statistiques** : Voir les gains et évaluations

### Pour les administrateurs
1. **Gestion des utilisateurs** : Voir tous les utilisateurs
2. **Supervision des séances** : Surveiller les séances
3. **Statistiques globales** : Vue d'ensemble de la plateforme
4. **Gestion des FAQ** : Créer et gérer les questions fréquentes

## 🔧 Configuration

### Variables d'environnement importantes

```env
APP_NAME="Plateforme de Tutorat"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tutoring_platform
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🧪 Tests

```bash
# Lancer les tests
php artisan test

# Tests avec couverture
php artisan test --coverage
```

## 📝 API Endpoints

### Authentification
- `POST /login` - Connexion
- `POST /logout` - Déconnexion
- `POST /register` - Inscription

### Séances
- `GET /sessions` - Liste des séances
- `POST /sessions` - Créer une séance
- `GET /sessions/{id}` - Détails d'une séance
- `PATCH /sessions/{id}` - Mettre à jour une séance
- `DELETE /sessions/{id}` - Supprimer une séance

### Tuteurs
- `GET /tutors` - Liste des tuteurs
- `GET /tutors/{id}` - Profil d'un tuteur

### Messages
- `GET /messages` - Liste des conversations
- `GET /messages/{user}` - Conversation avec un utilisateur
- `POST /messages/{user}` - Envoyer un message

## 🤝 Contribution

1. Fork le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👨‍💻 Auteur

Développé dans le cadre d'un projet final de formation.

## 🆘 Support

Pour toute question ou problème :
1. Vérifier la documentation
2. Consulter les issues existantes
3. Créer une nouvelle issue avec les détails du problème

---

**Note** : Ce projet est en développement actif. Les fonctionnalités peuvent évoluer.
