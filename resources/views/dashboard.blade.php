@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête du dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Bonjour, {{ $user->name }} !</h1>
                    <p class="text-muted mb-0">
                        @if($user->isTutor())
                            Tuteur - {{ ucfirst($user->level) }}
                        @else
                            Étudiant - {{ ucfirst($user->level) }}
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $user->isTutor() ? 'success' : 'primary' }} fs-6">
                        {{ $user->isTutor() ? 'Tuteur' : 'Étudiant' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                    <h4 class="card-title mb-1">{{ $stats['total_sessions'] }}</h4>
                    <p class="card-text text-muted small">Séances totales</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                    <h4 class="card-title mb-1">{{ number_format($stats['rating'], 1) }}</h4>
                    <p class="card-text text-muted small">Note moyenne</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                    <h4 class="card-title mb-1">{{ $stats['upcoming_sessions'] }}</h4>
                    <p class="card-text text-muted small">Séances à venir</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        @if($user->isTutor())
                            <i class="fas fa-euro-sign fa-2x text-success"></i>
                        @else
                            <i class="fas fa-graduation-cap fa-2x text-success"></i>
                        @endif
                    </div>
                    <h4 class="card-title mb-1">
                        @if($user->isTutor())
                            {{ number_format($stats['total_earnings'], 2) }}€
                        @else
                            {{ $stats['completed_sessions'] }}
                        @endif
                    </h4>
                    <p class="card-text text-muted small">
                        @if($user->isTutor())
                            Gains totaux
                        @else
                            Séances terminées
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user->isStudent())
                            <a href="{{ route('tutors.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Trouver un tuteur
                            </a>
                            <a href="{{ route('sessions.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>
                                Demander une séance
                            </a>
                        @else
                            <a href="{{ route('sessions.index') }}" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>
                                Voir mes demandes
                            </a>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>
                                Modifier mon profil
                            </a>
                        @endif
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-comments me-2"></i>
                            Messages
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user text-info me-2"></i>
                        Mon profil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-center">
                            <div class="mb-2">
                                <i class="fas fa-user-circle fa-3x text-muted"></i>
                            </div>
                        </div>
                        <div class="col-8">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <p class="text-muted small mb-2">{{ $user->email }}</p>
                            @if($user->phone)
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                </p>
                            @endif
                            <div class="mb-2">
                                {!! $user->getRatingStars() !!}
                                <small class="text-muted ms-1">({{ $user->rating ?? 0 }})</small>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                                Modifier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compétences et bio -->
    @if($user->skills || $user->bio)
    <div class="row g-4 mb-4">
        @if($user->skills)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools text-success me-2"></i>
                        Compétences
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($user->skills as $skill)
                        <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        @if($user->bio)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Bio
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $user->bio }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Séances récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history text-secondary me-2"></i>
                            Séances récentes
                        </h5>
                        <a href="{{ route('sessions.index') }}" class="btn btn-outline-primary btn-sm">
                            Voir toutes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentSessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>{{ $user->isTutor() ? 'Étudiant' : 'Tuteur' }}</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSessions as $session)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($session->title, 30) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $session->getSubjectText() }}</small>
                                        </td>
                                        <td>
                                            @if($user->isTutor())
                                                {{ $session->student->name }}
                                            @else
                                                {{ $session->tutor->name }}
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $session->scheduled_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $session->getStatusBadgeClass() }}">
                                                {{ $session->getStatusText() }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Aucune séance récente</h6>
                            <p class="text-muted small">
                                @if($user->isStudent())
                                    Vous n'avez pas encore demandé de séance de soutien.
                                @else
                                    Aucune demande de séance ne vous a été adressée.
                                @endif
                            </p>
                            @if($user->isStudent())
                                <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>
                                    Demander une séance
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
