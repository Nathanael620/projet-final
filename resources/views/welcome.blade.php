<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Soutiens-moi!') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-hands-helping me-2"></i>
                Soutiens-moi!
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Comment ça marche</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('/dashboard') }}">Tableau de bord</a></li>
                                <li><a class="dropdown-item" href="{{ url('/profile') }}">Mon profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Déconnexion</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Trouvez l'aide dont vous avez besoin
                    </h1>
                    <p class="lead mb-4">
                        Connectez-vous avec des étudiants et enseignants compétents pour obtenir du soutien académique, 
                        technique et méthodologique dans votre parcours universitaire.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                            Commencer maintenant
                        </a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg">
                            En savoir plus
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <img src="https://via.placeholder.com/500x400/007bff/ffffff?text=Soutiens-moi!" 
                             alt="Soutiens-moi!" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Nos Services</h2>
                    <p class="lead text-muted">
                        Une plateforme complète pour l'entraide universitaire
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Recherche de Tuteurs</h5>
                            <p class="card-text">
                                Trouvez facilement des étudiants ou enseignants compétents selon vos besoins 
                                et votre niveau d'études.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-calendar-alt fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Planification de Séances</h5>
                            <p class="card-text">
                                Organisez des séances de soutien en présentiel ou en visioconférence 
                                selon vos disponibilités.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-comments fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Messagerie & Chat</h5>
                            <p class="card-text">
                                Communiquez en temps réel avec vos tuteurs et créez des groupes 
                                de discussion pour vos projets.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-star fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Système de Notation</h5>
                            <p class="card-text">
                                Évaluez vos séances et consultez les avis d'autres étudiants 
                                pour choisir le meilleur tuteur.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-question-circle fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">FAQ Collaborative</h5>
                            <p class="card-text">
                                Posez vos questions et partagez vos connaissances 
                                dans notre forum d'entraide.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-bell fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title">Notifications</h5>
                            <p class="card-text">
                                Recevez des alertes en temps réel pour vos messages, 
                                séances et nouvelles demandes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section id="how-it-works" class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Comment ça marche ?</h2>
                    <p class="lead text-muted">
                        En 3 étapes simples, trouvez l'aide dont vous avez besoin
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <span class="h3 mb-0">1</span>
                        </div>
                    </div>
                    <h4>Créez votre profil</h4>
                    <p class="text-muted">
                        Inscrivez-vous et complétez votre profil avec vos compétences 
                        ou vos besoins d'aide.
                    </p>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <span class="h3 mb-0">2</span>
                        </div>
                    </div>
                    <h4>Trouvez votre tuteur</h4>
                    <p class="text-muted">
                        Recherchez parmi nos tuteurs selon la matière, le niveau 
                        et les disponibilités.
                    </p>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <span class="h3 mb-0">3</span>
                        </div>
                    </div>
                    <h4>Organisez votre séance</h4>
                    <p class="text-muted">
                        Planifiez votre séance, communiquez via le chat 
                        et évaluez l'expérience.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Questions Fréquentes</h2>
                    <p class="lead text-muted">
                        Tout ce que vous devez savoir sur Soutiens-moi!
                    </p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Comment fonctionne le système de paiement ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les paiements se font via Mobile Money (MOMO) et Orange Money. 
                                    Une commission est prélevée sur chaque transaction pour maintenir la plateforme.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Comment sont sélectionnés les tuteurs ?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les tuteurs sont des étudiants ou enseignants qui ont prouvé leurs compétences 
                                    dans leurs domaines. Ils sont évalués par la communauté.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Puis-je annuler une séance ?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oui, vous pouvez annuler une séance jusqu'à 24h avant le début. 
                                    Au-delà, des frais peuvent s'appliquer.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Comment se déroule une séance en visioconférence ?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Un lien Zoom est généré automatiquement et envoyé aux participants. 
                                    Vous recevez une notification 15 minutes avant le début de la séance.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-6 fw-bold mb-4">Prêt à commencer ?</h2>
            <p class="lead mb-4">
                Rejoignez notre communauté d'entraide universitaire dès aujourd'hui !
            </p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                Créer mon compte gratuitement
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Soutiens-moi!</h5>
                    <p class="text-muted">
                        La plateforme d'entraide universitaire qui connecte étudiants et enseignants 
                        pour un apprentissage collaboratif.
                    </p>
                </div>
                <div class="col-md-2">
                    <h6>Services</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Recherche de tuteurs</a></li>
                        <li><a href="#" class="text-muted">Planification</a></li>
                        <li><a href="#" class="text-muted">Messagerie</a></li>
                        <li><a href="#" class="text-muted">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Aide</a></li>
                        <li><a href="#" class="text-muted">Contact</a></li>
                        <li><a href="#" class="text-muted">Conditions</a></li>
                        <li><a href="#" class="text-muted">Confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Suivez-nous</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 Soutiens-moi!. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">Développé avec ❤️ pour la communauté universitaire</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </body>
</html>
