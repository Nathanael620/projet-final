@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Trouver un tuteur</h1>
                    <p class="text-muted mb-0">Découvrez nos tuteurs qualifiés</p>
                </div>
                <div>
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Demander une séance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('tutors.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="subject" class="form-label">Matière</label>
                            <select name="subject" id="subject" class="form-select">
                                <option value="">Toutes les matières</option>
                                <option value="mathematics" {{ request('subject') == 'mathematics' ? 'selected' : '' }}>Mathématiques</option>
                                <option value="physics" {{ request('subject') == 'physics' ? 'selected' : '' }}>Physique</option>
                                <option value="chemistry" {{ request('subject') == 'chemistry' ? 'selected' : '' }}>Chimie</option>
                                <option value="biology" {{ request('subject') == 'biology' ? 'selected' : '' }}>Biologie</option>
                                <option value="computer_science" {{ request('subject') == 'computer_science' ? 'selected' : '' }}>Informatique</option>
                                <option value="languages" {{ request('subject') == 'languages' ? 'selected' : '' }}>Langues</option>
                                <option value="literature" {{ request('subject') == 'literature' ? 'selected' : '' }}>Littérature</option>
                                <option value="history" {{ request('subject') == 'history' ? 'selected' : '' }}>Histoire</option>
                                <option value="geography" {{ request('subject') == 'geography' ? 'selected' : '' }}>Géographie</option>
                                <option value="economics" {{ request('subject') == 'economics' ? 'selected' : '' }}>Économie</option>
                                <option value="other" {{ request('subject') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="level" class="form-label">Niveau</label>
                            <select name="level" id="level" class="form-select">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Débutant</option>
                                <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                                <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Avancé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="max_price" class="form-label">Prix max (€/h)</label>
                            <input type="number" name="max_price" id="max_price" class="form-control" 
                                   value="{{ request('max_price') }}" min="0" step="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des tuteurs -->
    <div class="row g-4">
        @forelse($tutors as $tutor)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-circle fa-3x text-muted"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">{{ $tutor->name }}</h5>
                            <p class="text-muted small mb-1">{{ ucfirst($tutor->level) }}</p>
                            <div class="mb-1">
                                {!! $tutor->getRatingStars() !!}
                                <small class="text-muted ms-1">({{ $tutor->rating ?? 0 }})</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($tutor->skills)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Compétences :</small>
                        @foreach(array_slice($tutor->skills, 0, 3) as $skill)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                        @endforeach
                        @if(count($tutor->skills) > 3)
                            <small class="text-muted">+{{ count($tutor->skills) - 3 }} autres</small>
                        @endif
                    </div>
                    @endif
                    
                    @if($tutor->bio)
                    <p class="card-text small text-muted mb-3">
                        {{ Str::limit($tutor->bio, 100) }}
                    </p>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong class="text-success">{{ number_format($tutor->hourly_rate ?? 20, 2) }}€</strong>
                            <small class="text-muted">/heure</small>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i>
                                {{ $tutor->total_sessions ?? 0 }} séances
                            </small>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('tutors.show', $tutor) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>
                            Voir le profil
                        </a>
                        <a href="{{ route('sessions.create', ['tutor_id' => $tutor->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Réserver une séance
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun tuteur trouvé</h5>
                    <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tutors->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $tutors->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 