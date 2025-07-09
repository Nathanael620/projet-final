@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- En-tête du profil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="position-relative mb-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle fa-6x text-muted"></i>
                                @endif
                                <div class="position-absolute bottom-0 end-0">
                                    <span class="badge bg-{{ $user->isTutor() ? 'success' : 'primary' }} fs-6">
                                        {{ $user->isTutor() ? 'Tuteur' : 'Étudiant' }}
                                    </span>
                                </div>
                            </div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ ucfirst($user->level) }}</p>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-primary mb-1">{{ $stats['total_sessions'] }}</h5>
                                        <small class="text-muted">Séances totales</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-success mb-1">{{ $stats['completed_sessions'] }}</h5>
                                        <small class="text-muted">Séances terminées</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        @if($user->isTutor())
                                            <h5 class="text-warning mb-1">{{ number_format($stats['total_earnings'], 2) }}€</h5>
                                            <small class="text-muted">Gains totaux</small>
                                        @else
                                            <h5 class="text-warning mb-1">{{ number_format($stats['total_spent'], 2) }}€</h5>
                                            <small class="text-muted">Dépenses totales</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-info mb-1">{{ $stats['unread_messages'] }}</h5>
                                        <small class="text-muted">Messages non lus</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations du profil -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Informations du profil
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Paramètres spécifiques aux tuteurs -->
            @if($user->isTutor())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog text-success me-2"></i>
                        Paramètres tuteur
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.tutor-settings')
                </div>
            </div>
            @endif

            <!-- Informations de sécurité -->
            <div class="card border-0 shadow-sm mb-4">
                <x-security-info :user="$user" />
            </div>

            <!-- Gestion des sessions -->
            <div class="card border-0 shadow-sm mb-4">
                <x-session-manager :user="$user" />
            </div>

            <!-- Changement de mot de passe -->
            <div class="card border-0 shadow-sm mb-4" id="password-section">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lock text-warning me-2"></i>
                        Sécurité
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Suppression du compte -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 text-danger">
                        <i class="fas fa-trash me-2"></i>
                        Zone dangereuse
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('sessions.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar me-2"></i>
                            Mes séances
                        </a>
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-comments me-2"></i>
                            Messages
                        </a>
                        @if($user->isStudent())
                            <a href="{{ route('tutors.index') }}" class="btn btn-outline-warning">
                                <i class="fas fa-search me-2"></i>
                                Trouver un tuteur
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statut de disponibilité (tuteurs) -->
            @if($user->isTutor())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-toggle-on text-success me-2"></i>
                        Disponibilité
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Statut actuel :</span>
                        <span class="badge {{ $user->is_available ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_available ? 'Disponible' : 'Indisponible' }}
                        </span>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('profile.availability') }}" class="d-grid">
                        @csrf
                        <input type="hidden" name="is_available" value="{{ $user->is_available ? 0 : 1 }}">
                        <button type="submit" class="btn btn-{{ $user->is_available ? 'outline-danger' : 'outline-success' }}">
                            <i class="fas fa-toggle-{{ $user->is_available ? 'off' : 'on' }} me-2"></i>
                            {{ $user->is_available ? 'Devenir indisponible' : 'Devenir disponible' }}
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Informations de contact -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-address-card text-info me-2"></i>
                        Contact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">
                            <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                        </small>
                    </div>
                    @if($user->phone)
                    <div class="mb-2">
                        <small class="text-muted d-block">
                            <i class="fas fa-phone me-2"></i>{{ $user->phone }}
                        </small>
                    </div>
                    @endif
                    <div class="mb-2">
                        <small class="text-muted d-block">
                            <i class="fas fa-calendar me-2"></i>Membre depuis {{ $user->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
