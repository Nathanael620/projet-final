@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <!-- En-tête de la conversation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-user-circle fa-3x text-muted"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">
                                {{ $otherUser->name }}
                                @if($otherUser->isTutor())
                                    <span class="badge bg-success ms-2">Tuteur</span>
                                @else
                                    <span class="badge bg-primary ms-2">Étudiant</span>
                                @endif
                            </h5>
                            <p class="text-muted mb-1">{{ $otherUser->email }}</p>
                            @if($otherUser->isTutor())
                                <div class="d-flex align-items-center">
                                    {!! $otherUser->getRatingStars() !!}
                                    <small class="text-muted ms-2">({{ $otherUser->rating ?? 0 }}) - {{ $otherUser->total_sessions ?? 0 }} séances</small>
                                </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Conversation
                    </h6>
                </div>
                <div class="card-body" id="messages-container" style="height: 400px; overflow-y: auto;">
                    @if($messages->count() > 0)
                        @foreach($messages as $message)
                            <div class="message-item mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
                                <div class="d-inline-block {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" style="max-width: 70%;">
                                    <div class="message-content">
                                        {{ $message->content }}
                                    </div>
                                    <div class="message-meta mt-2">
                                        <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('d/m/Y H:i') }}
                                            @if($message->sender_id === auth()->id())
                                                <i class="fas fa-check-double ms-1 {{ $message->is_read ? 'text-info' : '' }}"></i>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Aucun message pour le moment</p>
                            <p class="text-muted small">Commencez la conversation !</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formulaire d'envoi -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('messages.store', $otherUser) }}" id="message-form">
                        @csrf
                        <div class="input-group">
                            <textarea name="content" id="message-content" class="form-control" 
                                      rows="3" placeholder="Tapez votre message..." required></textarea>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Profil de l'utilisateur -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">Profil</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-4x text-muted"></i>
                    </div>
                    
                    <h6 class="text-center mb-2">{{ $otherUser->name }}</h6>
                    <p class="text-muted text-center small mb-3">{{ $otherUser->email }}</p>
                    
                    @if($otherUser->phone)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-phone me-2"></i>{{ $otherUser->phone }}
                        </small>
                    </div>
                    @endif
                    
                    @if($otherUser->bio)
                    <div class="mb-3">
                        <small class="text-muted">{{ Str::limit($otherUser->bio, 100) }}</small>
                    </div>
                    @endif
                    
                    @if($otherUser->skills)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Compétences :</small>
                        @foreach(array_slice($otherUser->skills, 0, 3) as $skill)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @if($otherUser->isTutor())
                    <div class="text-center">
                        <div class="mb-2">
                            <strong class="text-success">{{ number_format($otherUser->hourly_rate ?? 20, 2) }}€</strong>
                            <small class="text-muted">/heure</small>
                        </div>
                        <a href="{{ route('tutors.show', $otherUser) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>
                            Voir le profil complet
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">Actions rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($otherUser->isTutor() && auth()->user()->isStudent())
                            <a href="{{ route('sessions.create', ['tutor_id' => $otherUser->id]) }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Demander une séance
                            </a>
                        @endif
                        
                        @if(auth()->user()->isTutor() && $otherUser->isStudent())
                            <a href="{{ route('sessions.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Proposer une séance
                            </a>
                        @endif
                        
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour aux messages
                        </a>
                    </div>
                </div>
            </div>

            <!-- Séances en commun -->
            @if($commonSessions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">Séances en commun</h6>
                </div>
                <div class="card-body">
                    @foreach($commonSessions->take(3) as $session)
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0 me-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="d-block">{{ Str::limit($session->title, 25) }}</small>
                            <small class="text-muted">{{ $session->scheduled_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge {{ $session->getStatusBadgeClass() }}">{{ $session->getStatusText() }}</span>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($commonSessions->count() > 3)
                    <div class="text-center mt-2">
                        <small class="text-muted">+{{ $commonSessions->count() - 3 }} autres séances</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageContent = document.getElementById('message-content');
    
    // Scroll vers le bas des messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Scroll initial
    scrollToBottom();
    
    // Gestion de l'envoi du formulaire
    messageForm.addEventListener('submit', function(e) {
        if (messageContent.value.trim() === '') {
            e.preventDefault();
            return;
        }
        
        // Désactiver le bouton pendant l'envoi
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    });
    
    // Auto-resize du textarea
    messageContent.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
});
</script>
@endpush

<style>
.message-item .bg-primary {
    background-color: #007bff !important;
}

.message-item .bg-light {
    background-color: #f8f9fa !important;
}

#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#messages-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection 