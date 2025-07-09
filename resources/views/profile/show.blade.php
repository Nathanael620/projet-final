@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête du profil public -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="position-relative mb-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle fa-8x text-muted"></i>
                                @endif
                                <div class="position-absolute bottom-0 end-0">
                                    <span class="badge bg-{{ $user->isTutor() ? 'success' : 'primary' }} fs-6">
                                        {{ $user->isTutor() ? 'Tuteur' : 'Étudiant' }}
                                    </span>
                                </div>
                            </div>
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">{{ ucfirst($user->level) }}</p>
                            
                            @if($user->isTutor())
                                <div class="mb-3">
                                    {!! $user->getRatingStars() !!}
                                    <div class="mt-1">
                                        <small class="text-muted">({{ $user->rating ?? 0 }}) - {{ $user->total_sessions ?? 0 }} séances</small>
                                    </div>
                                </div>
                                
                                @if($user->is_available)
                                    <span class="badge bg-success fs-6 mb-2">
                                        <i class="fas fa-check me-1"></i>Disponible
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6 mb-2">
                                        <i class="fas fa-times me-1"></i>Indisponible
                                    </span>
                                @endif
                            @endif
                        </div>
                        
                        <div class="col-md-9">
                            <div class="row g-4">
                                @if($user->isTutor())
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-success mb-1">{{ number_format($user->hourly_rate ?? 20, 2) }}€</h5>
                                        <small class="text-muted">Tarif horaire</small>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-primary mb-1">{{ $user->isTutor() ? $user->tutorSessions()->count() : $user->studentSessions()->count() }}</h5>
                                        <small class="text-muted">Séances totales</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-info mb-1">{{ $user->created_at->format('Y') }}</h5>
                                        <small class="text-muted">Membre depuis</small>
                                    </div>
                                </div>
                            </div>
                            
                            @if($user->bio)
                            <div class="mt-4">
                                <h6 class="text-muted mb-2">À propos</h6>
                                <p class="text-muted">{{ $user->bio }}</p>
                            </div>
                            @endif
                            
                            @if($user->skills && $user->isTutor())
                            <div class="mt-4">
                                <h6 class="text-muted mb-2">Compétences</h6>
                                <div>
                                    @foreach($user->skills as $skill)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations de contact -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-address-card text-info me-2"></i>
                        Informations de contact
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

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(auth()->check() && auth()->id() !== $user->id)
                            <a href="{{ route('messages.show', $user) }}" class="btn btn-outline-primary">
                                <i class="fas fa-comments me-2"></i>
                                Envoyer un message
                            </a>
                            
                            @if($user->isTutor() && auth()->user()->isStudent())
                                <a href="{{ route('sessions.create', ['tutor_id' => $user->id]) }}" class="btn btn-success">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    Demander une séance
                                </a>
                            @endif
                        @endif
                        
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour au dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Séances récentes -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Séances récentes
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentSessions = $user->isTutor() 
                            ? $user->tutorSessions()->with(['student'])->latest()->take(5)->get()
                            : $user->studentSessions()->with(['tutor'])->latest()->take(5)->get();
                    @endphp
                    
                    @if($recentSessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>{{ $user->isTutor() ? 'Étudiant' : 'Tuteur' }}</th>
                                        <th>Date</th>
                                        <th>Statut</th>
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
                                @if($user->isTutor())
                                    Ce tuteur n'a pas encore effectué de séances.
                                @else
                                    Cet étudiant n'a pas encore participé à des séances.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 