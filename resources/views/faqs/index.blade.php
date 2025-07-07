@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-question-circle text-primary me-2"></i>
                        Questions Fréquentes
                    </h1>
                    <p class="text-muted mb-0">Gérez les questions et réponses de la communauté</p>
                </div>
                <a href="{{ route('faqs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Nouvelle Question
                </a>
            </div>

            <!-- Filtres -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('faqs.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Catégorie</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">Toutes les catégories</option>
                                <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                                <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                                <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                                <option value="sessions" {{ request('category') === 'sessions' ? 'selected' : '' }}>Séances</option>
                                <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Rechercher une question..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search me-1"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des FAQ -->
            @if($faqs->count() > 0)
                <div class="row">
                    @foreach($faqs as $faq)
                        <div class="col-12 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <h5 class="card-title mb-0 me-2">{{ $faq->question }}</h5>
                                                <span class="badge bg-{{ $faq->getCategoryColor() }}">{{ $faq->getCategoryLabel() }}</span>
                                                @if($faq->is_public)
                                                    <span class="badge bg-success ms-1">Public</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">Privé</span>
                                                @endif
                                            </div>
                                            
                                            <p class="card-text text-muted mb-2">
                                                {{ Str::limit($faq->answer, 150) }}
                                            </p>
                                            
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $faq->user->name }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $faq->created_at->format('d/m/Y H:i') }}
                                                @if($faq->updated_at !== $faq->created_at)
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-edit me-1"></i>
                                                    Modifié le {{ $faq->updated_at->format('d/m/Y') }}
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex-shrink-0 ms-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('faqs.show', $faq) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($faq->user_id === auth()->id() || auth()->user()->isAdmin())
                                                    <a href="{{ route('faqs.edit', $faq) }}" class="btn btn-outline-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('faqs.destroy', $faq) }}" 
                                                          class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $faqs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune question trouvée</h4>
                    <p class="text-muted">Commencez par ajouter votre première question !</p>
                    <a href="{{ route('faqs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Créer une question
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 