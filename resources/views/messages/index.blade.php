@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Messages</h1>
                    <p class="text-muted mb-0">Vos conversations avec les autres utilisateurs</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2">
                        {{ auth()->user()->getUnreadMessagesCount() }} non lus
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des conversations -->
    @if($conversations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @foreach($conversations as $conversation)
                            <div class="conversation-item p-3 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }}">
                                <div class="d-flex align-items-center">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0 me-3">
                                        <div class="position-relative">
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                            @if($conversation['unread_count'] > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $conversation['unread_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="flex-grow-1 me-3">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0">
                                                {{ $conversation['user']->name }}
                                                @if($conversation['user']->isTutor())
                                                    <span class="badge bg-success ms-1">Tuteur</span>
                                                @else
                                                    <span class="badge bg-primary ms-1">Étudiant</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                {{ $conversation['last_message_time']->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        <p class="text-muted mb-1 small">
                                            @if($conversation['last_message']->sender_id === auth()->id())
                                                <i class="fas fa-reply text-primary me-1"></i>
                                            @else
                                                <i class="fas fa-arrow-right text-success me-1"></i>
                                            @endif
                                            {{ Str::limit($conversation['last_message']->content, 50) }}
                                        </p>
                                        
                                        <div class="d-flex align-items-center">
                                            <small class="text-muted me-3">
                                                <i class="fas fa-{{ $conversation['user']->isTutor() ? 'user-tie' : 'user-graduate' }} me-1"></i>
                                                {{ $conversation['user']->getSkillsString() }}
                                            </small>
                                            @if($conversation['user']->isTutor())
                                                <small class="text-muted">
                                                    {!! $conversation['user']->getRatingStars() !!}
                                                    <span class="ms-1">({{ $conversation['user']->rating ?? 0 }})</span>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('messages.show', $conversation['user']) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-comments me-1"></i>
                                            Ouvrir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune conversation</h5>
                        <p class="text-muted">
                            Vous n'avez pas encore de conversations. 
                            Commencez par demander une séance de soutien !
                        </p>
                        <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Demander une séance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-comments fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">{{ $conversations->count() }}</h5>
                    <p class="card-text text-muted small">Conversations</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-envelope fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">{{ auth()->user()->getUnreadMessagesCount() }}</h5>
                    <p class="card-text text-muted small">Messages non lus</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h5 class="card-title">{{ $conversations->where('user.role', 'tutor')->count() }}</h5>
                    <p class="card-text text-muted small">Tuteurs contactés</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.conversation-item {
    transition: background-color 0.2s ease;
}
</style>
@endsection 