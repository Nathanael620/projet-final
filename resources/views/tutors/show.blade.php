@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête du profil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-5x text-muted"></i>
                            </div>
                            <div class="mb-3">
                                {!! $tutor->getRatingStars() !!}
                                <div class="mt-1">
                                    <small class="text-muted">({{ $tutor->rating ?? 0 }}) - {{ $tutor->total_sessions ?? 0 }} séances</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-success fs-6">{{ ucfirst($tutor->level) }}</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h3 mb-1">{{ $tutor->name }}</h1>
                                    <p class="text-muted mb-2">Tuteur qualifié</p>
                                </div>
                                <div class="text-end">
                                    <div class="mb-2">
                                        <span class="h4 text-success">{{ number_format($tutor->hourly_rate ?? 20, 2) }}€</span>
                                        <small class="text-muted">/heure</small>
                                    </div>
                                    <a href="{{ route('sessions.create', ['tutor_id' => $tutor->id]) }}" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        Réserver une séance
                                    </a>
                                </div>
                            </div>
                            
                            @if($tutor->bio)
                            <div class="mb-3">
                                <h6>À propos</h6>
                                <p class="text-muted">{{ $tutor->bio }}</p>
                            </div>
                            @endif
                            
                            @if($tutor->skills)
                            <div class="mb-3">
                                <h6>Compétences</h6>
                                <div>
                                    @foreach($tutor->skills as $skill)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informations de contact</h6>
                                    <ul class="list-unstyled text-muted">
                                        @if($tutor->email)
                                        <li><i class="fas fa-envelope me-2"></i>{{ $tutor->email }}</li>
                                        @endif
                                        @if($tutor->phone)
                                        <li><i class="fas fa-phone me-2"></i>{{ $tutor->canViewPhone(auth()->user()) ? $tutor->phone : $tutor->getMaskedPhone() }}</li>
                                        @endif
                                        <li><i class="fas fa-clock me-2"></i>
                                            @if($tutor->is_available)
                                                <span class="text-success">Disponible</span>
                                            @else
                                                <span class="text-danger">Indisponible</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Statistiques</h6>
                                    <ul class="list-unstyled text-muted">
                                        <li><i class="fas fa-calendar-check me-2"></i>{{ $tutor->total_sessions ?? 0 }} séances effectuées</li>
                                        <li><i class="fas fa-star me-2"></i>Note moyenne : {{ number_format($tutor->rating ?? 0, 1) }}/5</li>
                                        <li><i class="fas fa-euro-sign me-2"></i>Tarif : {{ number_format($tutor->hourly_rate ?? 20, 2) }}€/heure</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matières enseignées -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book text-primary me-2"></i>
                        Matières enseignées
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <i class="fas fa-calculator fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">Mathématiques</h6>
                                    <small class="text-muted">Tous niveaux</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <i class="fas fa-atom fa-2x text-info me-3"></i>
                                <div>
                                    <h6 class="mb-1">Physique</h6>
                                    <small class="text-muted">Tous niveaux</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <i class="fas fa-flask fa-2x text-success me-3"></i>
                                <div>
                                    <h6 class="mb-1">Chimie</h6>
                                    <small class="text-muted">Tous niveaux</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avis et commentaires (placeholder) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments text-warning me-2"></i>
                        Avis des étudiants
                    </h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Aucun avis pour le moment</h6>
                    <p class="text-muted small">Soyez le premier à laisser un avis après votre séance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Séances récentes (placeholder) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Séances récentes
                    </h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Aucune séance récente</h6>
                    <p class="text-muted small">Les séances récentes apparaîtront ici</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 