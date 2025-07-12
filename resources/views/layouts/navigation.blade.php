<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ auth()->check() ? route('dashboard') : route('home') }}">
            Soutiens-moi!
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('tutors.*') ? ' active' : '' }}" href="{{ route('tutors.index') }}">Tuteurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('sessions.*') ? ' active' : '' }}" href="{{ route('sessions.index') }}">Séances</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('messages.*') ? ' active' : '' }}" href="{{ route('messages.index') }}">Messages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('notifications.*') ? ' active' : '' }}" href="{{ route('notifications.index') }}">
                        Notifications
                        @php
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                @endauth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle{{ request()->routeIs('faqs.*') ? ' active' : '' }}" href="{{ route('faqs.index') }}" id="faqDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-question-circle me-1"></i>FAQ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="faqDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('faqs.index') }}">
                                <i class="fas fa-list me-2"></i>Gérer les FAQ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('faqs.create') }}">
                                <i class="fas fa-plus me-2"></i>Nouvelle FAQ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('faqs.chatbot') }}">
                                <i class="fas fa-robot me-2"></i>Chatbot IA
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('faqs.public') }}">
                                <i class="fas fa-globe me-2"></i>FAQ Publique
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i>
                                    {{ __('Profil') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.sessions') }}">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    {{ __('Sessions') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('payments.wallet') }}">
                                    <i class="fas fa-wallet me-2"></i>
                                    {{ __('Portefeuille') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('payments.history') }}">
                                    <i class="fas fa-history me-2"></i>
                                    {{ __('Paiements') }}
                                </a>
                            </li>
                            @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cog me-2"></i>
                                        {{ __('Administration') }}
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
