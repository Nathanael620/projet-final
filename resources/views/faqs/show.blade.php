@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Détails de la Question
                        </h1>
                        <p class="text-muted mb-0">Question et réponse détaillées</p>
                    </div>
                </div>
                
                @if($faq->user_id === auth()->id() || auth()->user()->isAdmin())
                    <div class="btn-group" role="group">
                        <a href="{{ route('faqs.edit', $faq) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('faqs.destroy', $faq) }}" 
                              class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Question -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-question me-2"></i>
                            {{ $faq->question }}
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark">{{ $faq->getCategoryLabel() }}</span>
                            @if($faq->is_public)
                                <span class="badge bg-success ms-1">Public</span>
                            @else
                                <span class="badge bg-secondary ms-1">Privé</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="answer-content">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                Par {{ $faq->user->name }}
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Créé le {{ $faq->created_at->format('d/m/Y à H:i') }}
                                @if($faq->updated_at !== $faq->created_at)
                                    <br>
                                    <i class="fas fa-edit me-1"></i>
                                    Modifié le {{ $faq->updated_at->format('d/m/Y à H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2">
                <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i>
                    Retour à la liste
                </a>
                @if($faq->user_id === auth()->id() || auth()->user()->isAdmin())
                    <a href="{{ route('faqs.edit', $faq) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        Modifier
                    </a>
                @endif
                <a href="{{ route('faqs.public') }}" class="btn btn-outline-primary">
                    <i class="fas fa-globe me-2"></i>
                    FAQ Publique
                </a>
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
</style>
@endsection 