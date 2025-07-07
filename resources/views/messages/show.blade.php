@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- En-tête de la conversation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-user-circle fa-2x text-muted"></i>
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
                            <p class="text-muted small mb-0">
                                {{ $otherUser->getSkillsString() }}
                                @if($otherUser->isTutor())
                                    • {!! $otherUser->getRatingStars() !!} ({{ $otherUser->rating ?? 0 }})
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zone des messages -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body" style="height: 400px; overflow-y: auto;" id="messages-container">
                    @if($messages->count() > 0)
                        @foreach($messages as $message)
                            <div class="message-item mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
                                <div class="d-inline-block {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" style="max-width: 70%;">
                                    @if($message->sender_id !== auth()->id())
                                        <small class="d-block mb-1 text-muted">
                                            {{ $message->sender->name }}
                                        </small>
                                    @endif
                                    
                                    <div class="message-content">
                                        @if($message->type === 'text')
                                            <p class="mb-1">{{ $message->content }}</p>
                                        @elseif($message->type === 'file')
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file me-2"></i>
                                                <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="text-decoration-none">
                                                    Fichier joint
                                                </a>
                                            </div>
                                        @elseif($message->type === 'image')
                                            <img src="{{ asset('storage/' . $message->file_path) }}" alt="Image" class="img-fluid rounded" style="max-width: 200px;">
                                        @endif
                                    </div>
                                    
                                    <small class="d-block mt-1 {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                        {{ $message->created_at->format('H:i') }}
                                        @if($message->is_read && $message->sender_id === auth()->id())
                                            <i class="fas fa-check-double ms-1"></i>
                                        @elseif($message->sender_id === auth()->id())
                                            <i class="fas fa-check ms-1"></i>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Aucun message dans cette conversation</p>
                            <p class="text-muted small">Envoyez le premier message !</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formulaire d'envoi -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('messages.store', ['user' => $otherUser->id]) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                          rows="3" placeholder="Tapez votre message..." required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" 
                                           accept="image/*,.pdf,.doc,.docx,.txt">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Formats acceptés : images, PDF, documents (max 10MB)</small>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Envoyer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Scroll automatique vers le bas
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
});

// Auto-resize du textarea
document.querySelector('textarea[name="content"]').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
</script>

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