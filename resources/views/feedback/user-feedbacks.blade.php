@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" 
                                         alt="{{ $user->name }}"
                                         class="rounded-circle"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle fa-4x text-muted"></i>
                                @endif
                            </div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ ucfirst($user->role) }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-primary mb-1">{{ number_format($stats['average_rating'], 1) }}</h3>
                                    <div class="mb-2">
                                        {!! $user->getRatingStars() !!}
                                    </div>
                                    <small class="text-muted">Note moyenne</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-success mb-1">{{ $stats['total_feedbacks'] }}</h3>
                                    <small class="text-muted">Avis reçus</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-info mb-1">{{ $user->total_sessions ?? 0 }}</h3>
                                    <small class="text-muted">Séances totales</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="text-end">
                                @if($user->isTutor())
                                    <span class="badge bg-success fs-6 mb-2">
                                        <i class="fas fa-check me-1"></i>{{ $user->getAvailabilityText() }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ number_format($user->hourly_rate ?? 0, 2) }}€/heure</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution des notes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Distribution des notes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @for ($i = 5; $i >= 1; $i--)
                            <div class="col-md-2 col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <small class="text-muted">{{ $i }} étoiles</small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-warning" 
                                                 style="width: {{ $stats['rating_distribution'][$i]['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <small class="text-muted">{{ $stats['rating_distribution'][$i]['count'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des feedbacks -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Avis reçus ({{ $feedbacks->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($feedbacks->count() > 0)
                        @foreach($feedbacks as $feedback)
                            <div class="feedback-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($feedback->is_anonymous)
                                                <i class="fas fa-user-secret fa-2x text-muted"></i>
                                            @elseif($feedback->reviewer->avatar)
                                                <img src="{{ Storage::url($feedback->reviewer->avatar) }}" 
                                                     alt="{{ $feedback->reviewer->name }}"
                                                     class="rounded-circle"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                @if($feedback->is_anonymous)
                                                    Utilisateur anonyme
                                                @else
                                                    {{ $feedback->reviewer->name }}
                                                @endif
                                            </h6>
                                            <div class="mb-1">
                                                {!! $feedback->getRatingStars() !!}
                                                <small class="text-muted ms-2">{{ $feedback->rating }}/5</small>
                                            </div>
                                            <small class="text-muted">
                                                {{ $feedback->created_at->format('d/m/Y H:i') }}
                                                • Séance : {{ $feedback->session->title }}
                                            </small>
                                        </div>
                                    </div>
                                    
                                    @if(Auth::user()->id === $feedback->reviewer_id)
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('feedback.edit', $feedback) }}">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('feedback.destroy', $feedback) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce feedback ?')">
                                                            <i class="fas fa-trash me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($feedback->comment)
                                    <div class="feedback-comment">
                                        <p class="mb-0">{{ $feedback->comment }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $feedbacks->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun avis pour le moment</h5>
                            <p class="text-muted">Cet utilisateur n'a pas encore reçu d'avis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.feedback-item:last-child {
    border-bottom: none !important;
}

.feedback-comment {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-left: 55px;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endsection 