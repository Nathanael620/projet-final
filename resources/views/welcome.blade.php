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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #3b82f6 !important;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: #3b82f6;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 0.5s;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 0.8s;
        }

        .hero-buttons {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 1.1s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-hero {
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 0 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-hero {
            background: white;
            color: #3b82f6;
            border: 2px solid white;
        }

        .btn-primary-hero:hover {
            background: transparent;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-hero:hover {
            background: white;
            color: #3b82f6;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Services Section */
        .services-section {
            padding: 100px 0;
            background: #f8fafc;
        }

        .section-title {
            text-align: center;
            margin-bottom: 80px;
            opacity: 0;
            transform: translateY(30px);
        }

        .section-title.animate {
            animation: fadeInUp 1s ease forwards;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(30px);
        }

        .service-card.animate {
            animation: fadeInUp 1s ease forwards;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
            transition: all 0.3s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .service-description {
            color: #6b7280;
            line-height: 1.6;
        }

        /* How it works Section */
        .how-it-works {
            padding: 100px 0;
            background: white;
        }

        .step-card {
            text-align: center;
            padding: 40px 20px;
            opacity: 0;
            transform: translateY(30px);
        }

        .step-card.animate {
            animation: fadeInUp 1s ease forwards;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            position: relative;
        }

        .step-number::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -60px;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: translateY(-50%);
        }

        .step-card:last-child .step-number::after {
            display: none;
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .step-description {
            color: #6b7280;
            line-height: 1.6;
        }

        /* FAQ Section */
        .faq-section {
            padding: 100px 0;
            background: #f8fafc;
        }

        .accordion-item {
            border: none;
            margin-bottom: 15px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(30px);
        }

        .accordion-item.animate {
            animation: fadeInUp 1s ease forwards;
        }

        .accordion-button {
            background: white;
            border: none;
            padding: 20px 25px;
            font-weight: 600;
            color: #1f2937;
        }

        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-body {
            padding: 20px 25px;
            background: white;
            color: #6b7280;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,0 1000,1000 0,1000"/></svg>');
            background-size: cover;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(30px);
        }

        .cta-title.animate {
            animation: fadeInUp 1s ease forwards;
        }

        .cta-description {
            font-size: 1.25rem;
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(30px);
        }

        .cta-description.animate {
            animation: fadeInUp 1s ease forwards 0.3s;
        }

        .btn-cta {
            background: white;
            color: #3b82f6;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            opacity: 0;
            transform: translateY(30px);
        }

        .btn-cta.animate {
            animation: fadeInUp 1s ease forwards 0.6s;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            color: #3b82f6;
        }

        /* Footer */
        .footer {
            background: #1f2937;
            color: white;
            padding: 60px 0 30px;
            font-weight: 400;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .footer h5, .footer h6 {
            color: white;
            margin-bottom: 20px;
            font-weight: 600;
            text-shadow: none;
        }

        .footer p {
            color: #e5e7eb;
            font-weight: 400;
            line-height: 1.6;
        }

        .footer a {
            color: #d1d5db;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 400;
        }

        .footer a:hover {
            color: white;
            text-decoration: none;
        }

        .footer .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s ease;
            color: white;
        }

        .footer .social-links a:hover {
            background: #3b82f6;
            transform: translateY(-3px);
            color: white;
        }

        .footer .text-muted {
            color: #e5e7eb !important;
            font-weight: 400;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .step-number::after {
                display: none;
            }
            
            .btn-hero {
                display: block;
                margin: 10px 0;
            }
        }

        /* Scroll animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
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
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Trouvez l'aide dont vous avez besoin
                        </h1>
                        <p class="hero-subtitle">
                            Connectez-vous avec des étudiants et enseignants compétents pour obtenir du soutien académique, 
                            technique et méthodologique dans votre parcours universitaire.
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('register') }}" class="btn-hero btn-primary-hero">
                                <i class="fas fa-rocket me-2"></i>Commencer maintenant
                            </a>
                            <a href="#how-it-works" class="btn-hero btn-outline-hero">
                                <i class="fas fa-info-circle me-2"></i>En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <div class="hero-image" style="opacity: 0; transform: translateX(50px); animation: fadeInRight 1s ease forwards 1.4s;">
                            <i class="fas fa-graduation-cap" style="font-size: 15rem; color: rgba(255,255,255,0.3);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-title">
                <h2 class="display-4 fw-bold mb-3">Nos Services</h2>
                <p class="lead text-muted">
                    Une plateforme complète pour l'entraide universitaire
                </p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="service-title">Recherche de Tuteurs</h5>
                        <p class="service-description">
                            Trouvez facilement des étudiants ou enseignants compétents selon vos besoins 
                            et votre niveau d'études.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h5 class="service-title">Planification de Séances</h5>
                        <p class="service-description">
                            Organisez des séances de soutien en présentiel ou en visioconférence 
                            selon vos disponibilités.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h5 class="service-title">Messagerie & Chat</h5>
                        <p class="service-description">
                            Communiquez en temps réel avec vos tuteurs et créez des groupes 
                            de discussion pour vos projets.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h5 class="service-title">Système de Notation</h5>
                        <p class="service-description">
                            Évaluez vos séances et consultez les avis d'autres étudiants 
                            pour choisir le meilleur tuteur.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h5 class="service-title">FAQ Collaborative</h5>
                        <p class="service-description">
                            Posez vos questions et partagez vos connaissances 
                            dans notre forum d'entraide.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h5 class="service-title">Notifications</h5>
                        <p class="service-description">
                            Recevez des alertes en temps réel pour vos messages, 
                            séances et nouvelles demandes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2 class="display-4 fw-bold mb-3">Comment ça marche ?</h2>
                <p class="lead text-muted">
                    En 3 étapes simples, trouvez l'aide dont vous avez besoin
                </p>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4 class="step-title">Créez votre profil</h4>
                        <p class="step-description">
                            Inscrivez-vous et complétez votre profil avec vos compétences 
                            ou vos besoins d'aide.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4 class="step-title">Trouvez votre tuteur</h4>
                        <p class="step-description">
                            Recherchez parmi nos tuteurs selon la matière, le niveau 
                            et les disponibilités.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4 class="step-title">Organisez votre séance</h4>
                        <p class="step-description">
                            Planifiez votre séance, communiquez via le chat 
                            et évaluez l'expérience.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq-section">
        <div class="container">
            <div class="section-title">
                <h2 class="display-4 fw-bold mb-3">Questions Fréquentes</h2>
                <p class="lead text-muted">
                    Tout ce que vous devez savoir sur Soutiens-moi!
                </p>
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
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Prêt à commencer ?</h2>
                <p class="cta-description">
                    Rejoignez notre communauté d'entraide universitaire dès aujourd'hui !
                </p>
                <a href="{{ route('register') }}" class="btn-cta">
                    <i class="fas fa-user-plus me-2"></i>Créer mon compte gratuitement
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-hands-helping me-2"></i>Soutiens-moi!</h5>
                    <p>
                        La plateforme d'entraide universitaire qui connecte étudiants et enseignants 
                        pour un apprentissage collaboratif.
                    </p>
                </div>
                <div class="col-md-2">
                    <h6>Services</h6>
                    <ul class="list-unstyled">
                        <li><a href="#services">Recherche de tuteurs</a></li>
                        <li><a href="#services">Planification</a></li>
                        <li><a href="#services">Messagerie</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Aide</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Conditions</a></li>
                        <li><a href="#">Confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Suivez-nous</h6>
                    <div class="social-links">
                        <a href="https://www.facebook.com/profile.php?id=61578131171276"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.tiktok.com/@soutienmoi?_t=ZM-8y8ihgCLarw&_r=1"><i class="fab fa-tiktok"></i></a>
                        <a href="https://www.instagram.com/soutienmoi6?igsh=eHpsNTZ2NXZxYzBj&utm_source=qr"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/in/soutien-moi-62999a375?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Soutiens-moi!. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Développé par la TN.TEAM pour le projet final</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.section-title, .service-card, .step-card, .accordion-item');
            animateElements.forEach(el => {
                observer.observe(el);
            });

            // Animate CTA section elements
            const ctaElements = document.querySelectorAll('.cta-title, .cta-description, .btn-cta');
            ctaElements.forEach(el => {
                observer.observe(el);
            });
        });

        // Add floating animation to shapes
        const shapes = document.querySelectorAll('.shape');
        shapes.forEach((shape, index) => {
            shape.style.animationDelay = `${index * 2}s`;
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-section');
            if (parallax) {
                const speed = scrolled * 0.5;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });
    </script>
</body>
</html>
