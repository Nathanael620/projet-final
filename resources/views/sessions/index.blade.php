@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Mes Séances</h1>
                    <p class="text-muted mb-0">
                        @if(auth()->user()->isTutor())
                            Séances de soutien que vous donnez
                        @else
                            Séances de soutien que vous avez demandées
                        @endif
                    </p>
                </div>
                @if(auth()->user()->isStudent())
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Demander une séance
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Statut</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Acceptée</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="subject" class="form-label">Matière</label>
                            <select name="subject" id="subject" class="form-select">
                                <option value="">Toutes les matières</option>
                                <option value="mathematics" {{ request('subject') == 'mathematics' ? 'selected' : '' }}>Mathématiques</option>
                                <option value="physics" {{ request('subject') == 'physics' ? 'selected' : '' }}>Physique</option>
                                <option value="chemistry" {{ request('subject') == 'chemistry' ? 'selected' : '' }}>Chimie</option>
                                <option value="computer_science" {{ request('subject') == 'computer_science' ? 'selected' : '' }}>Informatique</option>
                                <option value="languages" {{ request('subject') == 'languages' ? 'selected' : '' }}>Langues</option>
                                <option value="other" {{ request('subject') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>En ligne</option>
                                <option value="in_person" {{ request('type') == 'in_person' ? 'selected' : '' }}>En présentiel</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-filter me-1"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des séances -->
    @if($sessions->count() > 0)
        <div class="row">
            @foreach($sessions as $session)
                <div class="col-12 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">{{ $session->title }}</h5>
                                        <span class="badge {{ $session->getStatusBadgeClass() }} fs-6">
                                            {{ $session->getStatusText() }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted mb-2">{{ Str::limit($session->description, 150) }}</p>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-book me-1"></i>
                                                {{ $session->getSubjectText() }}
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-signal me-1"></i>
                                                {{ ucfirst($session->level) }}
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $session->getFormattedDuration() }}
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-euro-sign me-1"></i>
                                                {{ $session->getFormattedPrice() }}
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        @if(auth()->user()->isStudent())
                                            <small class="text-muted me-3">
                                                <i class="fas fa-user-tie me-1"></i>
                                                {{ $session->tutor->name }}
                                            </small>
                                        @else
                                            <small class="text-muted me-3">
                                                <i class="fas fa-user-graduate me-1"></i>
                                                {{ $session->student->name }}
                                            </small>
                                        @endif
                                        
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $session->scheduled_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            Voir détails
                                        </a>
                                        
                                        @if($session->status === 'pending' && auth()->user()->isStudent())
                                            <form method="POST" action="{{ route('sessions.destroy', $session) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette séance ?')">
                                                    <i class="fas fa-times me-1"></i>
                                                    Annuler
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($session->status === 'pending' && auth()->user()->isTutor())
                                            <div class="btn-group btn-group-sm" role="group">
                                                <form method="POST" action="{{ route('sessions.update', $session) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('sessions.update', $session) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune séance trouvée</h5>
                        <p class="text-muted">
                            @if(auth()->user()->isStudent())
                                Vous n'avez pas encore demandé de séance de soutien.
                            @else
                                Aucune demande de séance ne vous a été adressée.
                            @endif
                        </p>
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Demander une séance
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 