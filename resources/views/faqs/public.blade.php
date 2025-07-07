@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- En-tête -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Questions Fréquentes
                </h1>
                <p class="lead text-muted">
                    Trouvez rapidement les réponses à vos questions sur Soutiens-moi!
                </p>
            </div>

            <!-- Navigation par catégorie -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="nav nav-pills justify-content-center" id="faq-tabs" role="tablist">
                        @foreach($categories as $key => $label)
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                    id="tab-{{ $key }}" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#content-{{ $key }}" 
                                    type="button" 
                                    role="tab">
                                <i class="fas fa-{{ $key === 'general' ? 'info-circle' : ($key === 'technical' ? 'cogs' : ($key === 'payment' ? 'credit-card' : ($key === 'sessions' ? 'calendar' : 'user'))) }} me-2"></i>
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Contenu par catégorie -->
            <div class="tab-content" id="faq-content">
                @foreach($categories as $key => $label)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                         id="content-{{ $key }}" 
                         role="tabpanel">
                        
                        @if(isset($faqs[$key]) && $faqs[$key]->count() > 0)
                            <div class="accordion" id="accordion-{{ $key }}">
                                @foreach($faqs[$key] as $index => $faq)
                                    <div class="accordion-item border-0 shadow-sm mb-3">
                                        <h2 class="accordion-header" id="heading-{{ $key }}-{{ $index }}">
                                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse-{{ $key }}-{{ $index }}" 
                                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                                    aria-controls="collapse-{{ $key }}-{{ $index }}">
                                                <div class="d-flex align-items-center w-100">
                                                    <span class="fw-semibold">{{ $faq->question }}</span>
                                                    <small class="text-muted ms-auto">
                                                        Par {{ $faq->user->name }}
                                                    </small>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $key }}-{{ $index }}" 
                                             class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                             aria-labelledby="heading-{{ $key }}-{{ $index }}" 
                                             data-bs-parent="#accordion-{{ $key }}">
                                            <div class="accordion-body">
                                                <div class="answer-content">
                                                    {!! nl2br(e($faq->answer)) !!}
                                                </div>
                                                <hr class="my-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $faq->created_at->format('d/m/Y') }}
                                                        @if($faq->updated_at !== $faq->created_at)
                                                            (modifié le {{ $faq->updated_at->format('d/m/Y') }})
                                                        @endif
                                                    </small>
                                                    @auth
                                                        @if($faq->user_id === auth()->id() || auth()->user()->isAdmin())
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="{{ route('faqs.edit', $faq) }}" 
                                                                   class="btn btn-outline-warning btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form method="POST" action="{{ route('faqs.destroy', $faq) }}" 
                                                                      class="d-inline" 
                                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucune question dans cette catégorie</h5>
                                <p class="text-muted">Soyez le premier à ajouter une question !</p>
                                @auth
                                    <a href="{{ route('faqs.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        Ajouter une question
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Se connecter pour ajouter
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="text-center mt-5">
                @auth
                    <a href="{{ route('faqs.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une question
                    </a>
                    <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                        <i class="fas fa-list me-2"></i>
                        Gérer mes questions
                    </a>
                @else
                    <p class="text-muted mb-3">Vous avez une question qui n'est pas dans la liste ?</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Connectez-vous pour contribuer
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
.answer-content {
    line-height: 1.6;
    font-size: 1.1rem;
}

.answer-content p {
    margin-bottom: 1rem;
}

.answer-content ul, .answer-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.answer-content li {
    margin-bottom: 0.5rem;
}

.nav-pills .nav-link {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    margin: 0 0.25rem;
    border: 1px solid #dee2e6;
    background-color: white;
    color: #6c757d;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #212529;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0, 123, 255, 0.25);
}
</style>
@endsection 