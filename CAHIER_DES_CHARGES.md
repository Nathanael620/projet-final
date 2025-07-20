# CAHIER DES CHARGES – Plateforme de Tutorat, Sessions, Paiements et Messagerie

---

## 1. Présentation du projet
 
La plateforme a pour objectif de mettre en relation des étudiants et des tuteurs pour organiser des sessions de tutorat en ligne, gérer les paiements, la messagerie, les profils, les feedbacks, la FAQ, et l’administration. Elle doit être sécurisée, ergonomique, évolutive et conforme aux normes de sécurité et de confidentialité des données.

La solution vise à :
- Faciliter la recherche et la réservation de sessions de tutorat.
- Permettre la gestion des paiements et des portefeuilles utilisateurs.
- Offrir une messagerie interne sécurisée.
- Gérer les profils, rôles et permissions des utilisateurs.
- Fournir un espace d’administration complet.
- Assurer la conformité RGPD et l’accessibilité.

---

## 2. Objectifs détaillés

### 2.1 Objectif principal
Développer une plateforme web complète, fiable et sécurisée pour la gestion de sessions de tutorat, de paiements, de profils, de messagerie et d’administration.

### 2.2 Objectifs secondaires
- Offrir une expérience utilisateur optimale sur tous supports (responsive design).
- Garantir la sécurité des transactions et des données personnelles.
- Permettre l’évolutivité et la maintenance aisée.
- Respecter les normes d’accessibilité (WCAG 2.1 AA) et de protection des données (RGPD).
- Fournir des outils d’analyse et de reporting pour l’administration.

---

## 3. Analyse de contexte et parties prenantes

### 3.1 Contexte
Le tutorat en ligne est en forte croissance. Les étudiants recherchent des solutions flexibles et fiables pour accéder à des compétences variées, tandis que les tuteurs souhaitent valoriser leur expertise et gérer leur activité simplement.

### 3.2 Parties prenantes
| Rôle           | Description                                                                 |
|----------------|-----------------------------------------------------------------------------|
| Étudiant       | Utilisateur cherchant à réserver des sessions de tutorat.                   |
| Tuteur         | Utilisateur proposant des sessions de tutorat.                              |
| Administrateur | Supervise la plateforme, gère les utilisateurs, les paiements, les contenus.|
| Support        | Gère les demandes d’assistance et les incidents.                            |
| Développeur    | Conçoit, développe, maintient la plateforme.                                |
| Partenaires    | Établissements, organismes éducatifs, prestataires de paiement.             |

### 3.3 Analyse SWOT
| Forces                        | Faiblesses                      | Opportunités                  | Menaces                        |
|-------------------------------|----------------------------------|-------------------------------|-------------------------------|
| Offre complète (sessions, paiement, messagerie) | Dépendance à la connexion internet | Marché en croissance | Risques de fraudes/piratage |
| Sécurité avancée              | Complexité technique             | Partenariats éducatifs        | Concurrence accrue            |
| UX soignée, responsive        | Nécessité de support utilisateur | Extension à l’international   | Évolutions réglementaires     |

---

## 4. Fonctionnalités détaillées

### 4.1 Gestion des utilisateurs
#### 4.1.1 Authentification et inscription
- Inscription par email, mot de passe fort (min. 8 caractères, majuscule, minuscule, chiffre, caractère spécial).
- Vérification de l’email via lien unique.
- Connexion sécurisée, gestion des sessions, déconnexion sur tous les appareils.
- Réinitialisation du mot de passe par email sécurisé.
- Authentification à deux facteurs (2FA) optionnelle (email ou application d’authentification).
- Limitation des tentatives de connexion (anti-brute-force).
- Journalisation des connexions et des tentatives échouées.

#### 4.1.2 Profils utilisateurs
- Champs obligatoires : nom, prénom, email, mot de passe, rôle.
- Champs optionnels : avatar, bio, compétences (liste déroulante et saisie libre), niveau, disponibilité, téléphone (masqué par défaut).
- Gestion des rôles : étudiant, tuteur, administrateur (possibilité d’ajouter modérateur, support, etc.).
- Profil public/privé, gestion de la visibilité des informations.
- Modification des informations personnelles, gestion de l’avatar (upload, suppression, recadrage).
- Historique des connexions, gestion des sessions actives (affichage, déconnexion à distance).
- Affichage des statistiques personnelles (nombre de sessions, feedbacks, note moyenne, etc.).

#### 4.1.3 Gestion des rôles et permissions
- Attribution de rôles à l’inscription ou par l’admin.
- Permissions granulaires (accès admin, gestion des paiements, modération, etc.).
- Système extensible pour de futurs rôles (modérateur, support, etc.).
- Interface d’administration pour la gestion des rôles et des permissions.

### 4.2 Gestion des sessions de tutorat
#### 4.2.1 Création et réservation
- Recherche de tuteurs par compétence, niveau, disponibilité, note, prix.
- Filtres avancés (langue, créneau horaire, avis, etc.).
- Création de sessions par les étudiants ou proposition par les tuteurs.
- Validation, modification, annulation de sessions (avec règles de délai et notifications).
- Statuts : à venir, en cours, terminée, annulée, en attente de paiement, en litige.
- Notifications automatiques (email, in-app, SMS optionnel) pour chaque étape (création, modification, annulation, rappel avant session, etc.).
- Gestion des conflits de planning (vérification de la disponibilité en temps réel).
- Export des sessions (CSV, PDF) pour l’utilisateur.

#### 4.2.2 Suivi et historique
- Tableau de bord personnel avec historique des sessions (filtrable par statut, date, tuteur/étudiant).
- Affichage des détails de chaque session (date, heure, durée, tuteur/étudiant, statut, lien de visioconférence, etc.).
- Possibilité de reprogrammer ou d’annuler une session selon les règles définies.
- Archivage automatique des sessions terminées.

#### 4.2.3 Rappels et sécurité
- Rappels automatiques avant chaque session (email, notification in-app, SMS optionnel).
- Système anti-no-show (pénalités, alertes, historique des absences).
- Gestion des litiges (interface de déclaration, suivi, résolution par l’admin).

### 4.3 Messagerie interne
#### 4.3.1 Envoi et réception de messages
- Messagerie temps réel (WebSocket ou polling long).
- Historique des conversations, notifications de nouveaux messages (in-app, email).
- Filtres par utilisateur, recherche dans les messages (par mot-clé, date, utilisateur).
- Signalement de messages inappropriés (modération par l’admin).
- Blocage d’utilisateurs (empêche l’envoi de nouveaux messages).
- Gestion des pièces jointes (images, PDF, taille max 5 Mo, scan antivirus).

#### 4.3.2 Groupes et discussions multiples
- Création de groupes de discussion (optionnel).
- Gestion des membres, invitations, modération des groupes.
- Historique des discussions de groupe, notifications spécifiques.

### 4.4 Paiements et portefeuille
#### 4.4.1 Portefeuille utilisateur
- Création automatique du portefeuille à l’inscription.
- Solde affiché en temps réel, historique des transactions (filtrable par date, type, montant).
- Ajout de fonds (Stripe, PayPal, CB), retrait (virement bancaire, PayPal).
- Conversion de devises (optionnel, selon l’internationalisation).
- Génération de relevés téléchargeables (PDF, CSV).
- Notifications lors de chaque opération (ajout, retrait, paiement, remboursement).

#### 4.4.2 Paiement des sessions
- Paiement à la réservation ou après la session (configurable).
- Utilisation du portefeuille ou paiement direct (CB, Stripe, PayPal).
- Gestion des remboursements (annulation, litige, erreur technique).
- Génération automatique de factures PDF (conformes à la législation).
- Historique des paiements, filtres avancés (statut, date, session, tuteur/étudiant).

#### 4.4.3 Sécurité des transactions
- Chiffrement des données sensibles (SSL/TLS, stockage sécurisé).
- Journalisation des opérations critiques (audit trail).
- Détection de fraudes (analyse comportementale, alertes automatiques, blocage temporaire).
- Limitation des montants par transaction et par période (configurable).
- Double validation pour les retraits importants.

### 4.5 Feedback, notation et FAQ
#### 4.5.1 Système d’évaluation
- Notation des tuteurs par les étudiants (étoiles, commentaire obligatoire).
- Affichage des notes sur les profils, classement des tuteurs par note et nombre d’avis.
- Possibilité de répondre à un avis (droit de réponse du tuteur).
- Modération des avis (signalement, suppression par l’admin).
- Statistiques de satisfaction (taux de recommandation, évolution des notes).

#### 4.5.2 FAQ intelligente et chatbot
- FAQ dynamique, recherche par mots-clés, suggestions automatiques.
- Catégorisation des questions, affichage des plus consultées.
- Chatbot pour répondre aux questions fréquentes, escalade vers le support humain si besoin.
- Système d’auto-apprentissage du chatbot (ajout de nouvelles réponses par l’admin).

### 4.6 Administration
#### 4.6.1 Tableau de bord admin
- Statistiques globales (sessions, utilisateurs, revenus, feedbacks, taux de satisfaction, taux de litiges).
- Gestion des utilisateurs (création, modification, suppression, suspension, réinitialisation de mot de passe).
- Gestion des sessions (validation, annulation, reprogrammation, gestion des litiges).
- Gestion des paiements (validation, remboursement, suivi des transactions, export comptable).
- Gestion des feedbacks et FAQ (modération, ajout, suppression, édition).
- Gestion des signalements (messages, utilisateurs, sessions).
- Gestion des logs et de la sécurité (audit, alertes, blocage d’IP, etc.).
- Interface de configuration avancée (paramètres généraux, moyens de paiement, notifications, etc.).

#### 4.6.2 Gestion des accès et logs
- Historique détaillé des actions admin (création, modification, suppression, connexion, etc.).
- Logs de sécurité (tentatives d’intrusion, accès non autorisés, erreurs critiques).
- Gestion des droits d’accès par rôle et par utilisateur (ACL).
- Journalisation des modifications de configuration.

---

## 5. Exigences techniques détaillées

### 5.1 Architecture
- **Backend** : Laravel 12.x, PHP 8.2+, MySQL 8+, API RESTful pour certaines fonctionnalités (mobile, intégrations futures).
- **Frontend** : Blade, Bootstrap 5, Tailwind CSS, JavaScript (optionnel : Vue.js/React pour modules dynamiques).
- **Séparation claire backend/frontend** : possibilité d’évolution vers SPA ou application mobile.
- **Hébergement** : Serveur Linux, Nginx/Apache, SSL obligatoire.
- **Stockage** : Fichiers utilisateurs (avatars, pièces jointes) sur disque sécurisé ou cloud (S3 compatible).
- **Sauvegardes** : Automatisées, chiffrées, rétention configurable.

### 5.2 Sécurité
- CSRF, XSS, validation côté serveur et client.
- Chiffrement des mots de passe (bcrypt/argon2).
- Gestion des rôles et permissions (middleware Laravel, policies, gates).
- Logs d’accès et d’erreurs, alertes en cas d’anomalie.
- Conformité RGPD (droit à l’oubli, portabilité, consentement cookies, anonymisation sur demande).
- Limitation des accès par IP, blocage automatique après X tentatives échouées.
- Surveillance des accès admin (double authentification, logs renforcés).

### 5.3 Accessibilité et UX
- Respect des normes WCAG 2.1 AA.
- Navigation clavier, contraste élevé, textes alternatifs pour les images.
- Responsive design (mobile, tablette, desktop).
- Tests d’accessibilité automatisés et manuels.

### 5.4 Performances
- Mise en cache (pages, requêtes, assets, Redis/Memcached).
- Optimisation des requêtes SQL, pagination, lazy loading.
- Monitoring (Sentry, Laravel Telescope, logs personnalisés).
- CDN pour les assets statiques.

### 5.5 Tests et qualité
- Tests unitaires, fonctionnels, end-to-end (PHPUnit, Pest, Cypress).
- CI/CD (GitHub Actions, GitLab CI, déploiement automatisé).
- Revue de code systématique, respect des conventions PSR.
- Couverture de tests minimale exigée : 80%.

---

## 6. Diagrammes et modélisation

### 6.1 Diagramme de cas d’utilisation (UML textuel)
```
@startuml
actor Etudiant
actor Tuteur
actor Admin

Etudiant --> (S’inscrire)
Etudiant --> (Réserver une session)
Etudiant --> (Envoyer un message)
Etudiant --> (Payer une session)
Etudiant --> (Laisser un feedback)

Tuteur --> (S’inscrire)
Tuteur --> (Proposer une session)
Tuteur --> (Envoyer un message)
Tuteur --> (Recevoir un paiement)
Tuteur --> (Consulter feedback)

Admin --> (Gérer utilisateurs)
Admin --> (Gérer sessions)
Admin --> (Gérer paiements)
Admin --> (Gérer FAQ)
@enduml
```

### 6.2 Diagramme de classes (UML textuel)
```
@startuml
class User {
  +id: int
  +name: string
  +email: string
  +role: string
  +password: string
  +avatar: string
  +bio: text
  +skills: array
  +level: string
  +is_available: bool
  +hourly_rate: decimal
  +rating: decimal
  +is_active: bool
  +created_at: datetime
  +updated_at: datetime
}
class Session {
  +id: int
  +student_id: int
  +tutor_id: int
  +status: string
  +title: string
  +description: text
  +start_time: datetime
  +end_time: datetime
  +price: decimal
  +created_at: datetime
  +updated_at: datetime
}
class Payment {
  +id: int
  +user_id: int
  +session_id: int
  +amount: decimal
  +status: string
  +payment_method: string
  +paid_at: datetime
  +created_at: datetime
  +updated_at: datetime
}
class Wallet {
  +id: int
  +user_id: int
  +balance: decimal
  +currency: string
  +created_at: datetime
  +updated_at: datetime
}
class Message {
  +id: int
  +sender_id: int
  +receiver_id: int
  +content: text
  +is_read: bool
  +created_at: datetime
  +updated_at: datetime
}
User "1" -- "*" Session : participe
User "1" -- "1" Wallet : possède
Session "1" -- "*" Payment : lié à
User "1" -- "*" Message : envoie
User "1" -- "*" Message : reçoit
@enduml
```

### 6.3 Diagramme de base de données (extrait textuel)
| Table         | Champs principaux                                                                 |
|---------------|-----------------------------------------------------------------------------------|
| users         | id, name, email, password, role, avatar, bio, skills, level, is_available, ...    |
| sessions      | id, student_id, tutor_id, status, title, description, start_time, end_time, ...   |
| payments      | id, user_id, session_id, amount, status, payment_method, paid_at, ...             |
| wallets       | id, user_id, balance, currency, ...                                               |
| messages      | id, sender_id, receiver_id, content, is_read, ...                                 |
| feedbacks     | id, reviewer_id, reviewed_id, rating, comment, ...                                |
| notifications | id, notifiable_id, notifiable_type, data, read_at, ...                            |

---

## 7. User stories, scénarios d’usage et cas limites

### 7.1 User stories principales
- **En tant qu’étudiant**, je veux rechercher un tuteur par compétence afin de réserver une session adaptée à mes besoins.
- **En tant que tuteur**, je veux gérer mes disponibilités pour optimiser mon emploi du temps.
- **En tant qu’étudiant**, je veux payer une session en toute sécurité via mon portefeuille ou carte bancaire.
- **En tant qu’admin**, je veux visualiser les statistiques globales pour piloter la plateforme.
- **En tant qu’étudiant**, je veux pouvoir laisser un feedback après chaque session.
- **En tant que tuteur**, je veux consulter les avis reçus et y répondre.
- **En tant qu’utilisateur**, je veux pouvoir signaler un comportement inapproprié.
- **En tant qu’admin**, je veux pouvoir suspendre un utilisateur ou une session en cas de problème.

### 7.2 Scénarios détaillés
#### Scénario : Réservation d’une session
1. L’étudiant se connecte à la plateforme.
2. Il recherche un tuteur par compétence, niveau, disponibilité.
3. Il consulte le profil du tuteur, propose une date et une heure.
4. Le tuteur reçoit une notification, accepte ou propose un autre créneau.
5. L’étudiant valide la proposition et procède au paiement.
6. Les deux parties reçoivent une notification de confirmation.
7. Un rappel est envoyé avant la session.
8. Après la session, l’étudiant peut laisser un feedback.

#### Scénario : Paiement d’une session
1. L’étudiant choisit le mode de paiement (portefeuille, Stripe, PayPal).
2. Le système vérifie le solde ou redirige vers le prestataire externe.
3. Une fois le paiement validé, la session passe au statut « confirmée ».
4. Un reçu/facture est généré et envoyé par email.
5. L’historique du paiement est mis à jour.

#### Scénario : Messagerie
1. L’étudiant clique sur « Contacter le tuteur ».
2. Une conversation s’ouvre, les messages sont échangés en temps réel.
3. Les notifications s’affichent en cas de nouveau message.
4. L’utilisateur peut signaler un message inapproprié.

#### Scénario : Gestion des litiges
1. L’étudiant ou le tuteur signale un problème sur une session.
2. L’admin reçoit une notification et accède à l’interface de gestion des litiges.
3. L’admin analyse les éléments, contacte les parties si besoin.
4. L’admin tranche le litige (remboursement, suspension, avertissement, etc.).
5. Les parties sont notifiées de la décision.

#### Cas limites et erreurs gérés
- Tentative de réservation sur un créneau déjà pris : message d’erreur, suggestion d’autres créneaux.
- Paiement refusé (solde insuffisant, carte refusée) : message d’erreur, possibilité de réessayer.
- Session annulée par le tuteur : notification immédiate, remboursement automatique.
- Utilisateur banni : accès bloqué, message explicite, possibilité de contacter le support.
- Tentative d’accès à une ressource non autorisée : erreur 403, log de sécurité.

---

## 8. Exigences complémentaires

### 8.1 RGPD et confidentialité
- Consentement explicite à l’inscription (case à cocher, lien vers la politique de confidentialité).
- Droit à l’oubli : suppression/anonymisation des données sur demande.
- Export des données personnelles (format JSON, CSV, PDF).
- Journalisation des accès aux données sensibles.
- Politique de cookies conforme (bandeau, gestion des préférences).

### 8.2 Support et maintenance
- Système de ticket/support intégré (création, suivi, réponse, clôture).
- Documentation technique (installation, configuration, déploiement, API, schémas BDD).
- Manuel utilisateur (guide d’utilisation illustré, FAQ, vidéos tutos).
- Procédures de sauvegarde et restauration (fréquence, stockage, test de restauration).
- Plan de maintenance préventive (mises à jour, surveillance, alertes).

### 8.3 Évolutivité
- Architecture modulaire, ajout facile de nouvelles fonctionnalités (plugins, modules).
- API publique documentée (OpenAPI/Swagger) pour intégrations futures (mobile, partenaires).
- Préparation à l’internationalisation (i18n, gestion des langues, devises).
- Scalabilité horizontale (load balancing, microservices optionnels).

---

## 9. Planning prévisionnel détaillé

| Semaine | Tâches principales |
|---------|--------------------|
| 1       | Analyse, conception BDD, maquettes, validation du cahier des charges |
| 2       | Authentification, gestion des profils, sessions de base |
| 3       | Paiements, portefeuille, messagerie |
| 4       | Feedback, FAQ, chatbot, administration |
| 5       | Tests, corrections, documentation, déploiement |
| 6       | Recette, formation, mise en production, support initial |

---

## 10. Critères de recette et tests

### 10.1 Critères de réussite
- Application fonctionnelle, sécurisée, ergonomique, accessible.
- Paiements fiables et traçables, conformité légale.
- Expérience utilisateur fluide sur tous supports.
- Respect des délais et des fonctionnalités prévues.
- Documentation complète et à jour.

### 10.2 Cahier de tests détaillé
| Test ID | Fonctionnalité | Scénario | Préconditions | Résultat attendu | Statut |
|---------|---------------|----------|---------------|------------------|--------|
| T-001   | Authentification | Connexion avec email/mot de passe valide | Utilisateur existant | Accès au tableau de bord | OK/KO |
| T-002   | Authentification | Connexion avec mauvais mot de passe | Utilisateur existant | Message d’erreur, blocage après 5 tentatives | OK/KO |
| T-003   | Paiement | Paiement d’une session avec portefeuille | Solde suffisant | Solde débité, session confirmée | OK/KO |
| T-004   | Paiement | Paiement d’une session avec carte refusée | Carte invalide | Message d’erreur, possibilité de réessayer | OK/KO |
| T-005   | Messagerie | Envoi d’un message à un tuteur | Session existante | Message visible dans l’historique | OK/KO |
| T-006   | Feedback | Laisser un avis sur un tuteur | Session terminée | Note affichée sur le profil | OK/KO |
| T-007   | Sécurité | Tentative d’accès à une page admin sans droits | Utilisateur non admin | Erreur 403, log de sécurité | OK/KO |
| T-008   | Accessibilité | Navigation clavier sur toutes les pages | - | Navigation possible sans souris | OK/KO |
| T-009   | RGPD | Export des données personnelles | Utilisateur connecté | Fichier JSON/CSV/PDF généré | OK/KO |
| T-010   | Support | Création d’un ticket d’assistance | Utilisateur connecté | Ticket créé, notification support | OK/KO |

---

## 11. Annexes, glossaire, procédures

### 11.1 Glossaire
- **Session** : Séance de tutorat planifiée entre un étudiant et un tuteur.
- **Portefeuille** : Compte virtuel permettant de stocker des fonds pour payer ou recevoir des paiements.
- **Feedback** : Évaluation laissée par un utilisateur après une session.
- **FAQ** : Foire Aux Questions, base de connaissances dynamique.
- **2FA** : Authentification à deux facteurs.
- **RGPD** : Règlement Général sur la Protection des Données.
- **ACL** : Access Control List, gestion fine des droits d’accès.
- **SPA** : Single Page Application.
- **API** : Application Programming Interface.

### 11.2 Procédures et politiques
- Procédure de création d’un compte utilisateur (étapes, validations, emails envoyés).
- Procédure de réinitialisation du mot de passe (sécurité, délais, logs).
- Procédure de gestion des litiges (déclaration, traitement, résolution, notification).
- Politique de sécurité des mots de passe (longueur, complexité, renouvellement).
- Politique de sauvegarde et restauration (fréquence, stockage, test de restauration).
- Politique de gestion des incidents (détection, alerte, résolution, communication).

### 11.3 Exemples d’écrans (wireframes textuels)
#### Page d’accueil (étudiant non connecté)
```
+-------------------------------------------------------------+
| [Logo] [Connexion] [Inscription]                            |
+-------------------------------------------------------------+
| Bienvenue sur la plateforme de tutorat !                    |
| [Rechercher un tuteur] [Découvrir les fonctionnalités]      |
+-------------------------------------------------------------+
| [Avantages] [Témoignages] [FAQ] [Contact]                   |
+-------------------------------------------------------------+
```
#### Tableau de bord étudiant
```
+-------------------------------------------------------------+
| [Menu] [Profil] [Déconnexion]                               |
+-------------------------------------------------------------+
| Mes prochaines sessions :                                   |
| - Mathématiques avec M. Dupont, 12/09/2025 14h              |
| - Physique avec Mme Martin, 15/09/2025 10h                  |
+-------------------------------------------------------------+
| [Réserver une nouvelle session] [Consulter l’historique]    |
+-------------------------------------------------------------+
| [Solde portefeuille] [Ajouter des fonds] [Retirer]          |
+-------------------------------------------------------------+
| [Messagerie] [Feedbacks] [Support]                          |
+-------------------------------------------------------------+
```
#### Fiche tuteur
```
+-------------------------------------------------------------+
| [Photo] M. Dupont (Mathématiques, 4.8/5, 120 avis)          |
+-------------------------------------------------------------+
| Compétences : Mathématiques, Physique                       |
| Niveau : Avancé                                             |
| Tarif : 25€/h                                               |
| Disponibilités : Lundi, Mercredi, Vendredi                  |
+-------------------------------------------------------------+
| [Réserver une session] [Envoyer un message]                 |
+-------------------------------------------------------------+
| [Avis des étudiants]                                        |
+-------------------------------------------------------------+
```

### 11.4 Références et ressources
- [Laravel documentation](https://laravel.com/docs)
- [Stripe API](https://stripe.com/docs/api)
- [PayPal API](https://developer.paypal.com/docs/api/overview/)
- [RGPD](https://www.cnil.fr/fr/rgpd-de-quoi-parle-t-on)
- [WCAG 2.1](https://www.w3.org/WAI/standards-guidelines/wcag/)
- [Bootstrap](https://getbootstrap.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [PlantUML](https://plantuml.com/)

---

# Fin du cahier des charges exhaustif

Ce document est prêt à être utilisé pour le développement, la gestion de projet, la contractualisation ou l’appel d’offres. Il peut être enrichi avec des annexes techniques, des schémas UML détaillés, des exemples de code, des politiques de sécurité, des procédures de déploiement, etc. Chaque section est complète et opérationnelle. 