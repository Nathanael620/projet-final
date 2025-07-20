@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-star text-warning me-2"></i>
                            Mes avis donnés
                        </h4>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $feedbacks->total() }} avis</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($feedbacks->count() > 0)
                        @foreach($feedbacks as $feedback)
                            <div class="feedback-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($feedback->reviewed->avatar)
                                                <img src="{{ Storage::url($feedback->reviewed->avatar) }}" 
                                                     alt="{{ $feedback->reviewed->name }}"
                                                     class="rounded-circle"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $feedback->reviewed->name }}
                                                <span class="badge bg-secondary ms-2">{{ ucfirst($feedback->reviewed->role) }}</span>
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
                                </div>
                                
                                @if($feedback->comment)
                                    <div class="feedback-comment">
                                        <p class="mb-0">{{ $feedback->comment }}</p>
                                    </div>
                                @endif
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        @if($feedback->is_anonymous)
                                            Avis anonyme
                                        @else
                                            Avis public
                                        @endif
                                        • {{ $feedback->getTypeText() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $feedbacks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun avis donné</h5>
                            <p class="text-muted">Vous n'avez pas encore donné d'avis pour vos séances.</p>
                            <a href="{{ route('sessions.index') }}" class="btn btn-primary">
                                <i class="fas fa-calendar me-2"></i>Voir mes séances
                            </a>
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
    margin-left: 65px;
}
</style>
@endsection 