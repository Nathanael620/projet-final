@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- En-tête -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('faqs.show', $faq) }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Modifier la Question
                    </h1>
                    <p class="text-muted mb-0">Modifiez les détails de cette question</p>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('faqs.update', $faq) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="question" class="form-label">
                                Question <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="question" id="question" 
                                   class="form-control @error('question') is-invalid @enderror" 
                                   value="{{ old('question', $faq->question) }}" required maxlength="255">
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="category" 
                                    class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="general" {{ old('category', $faq->category) === 'general' ? 'selected' : '' }}>
                                    Général
                                </option>
                                <option value="technical" {{ old('category', $faq->category) === 'technical' ? 'selected' : '' }}>
                                    Technique
                                </option>
                                <option value="payment" {{ old('category', $faq->category) === 'payment' ? 'selected' : '' }}>
                                    Paiement
                                </option>
                                <option value="sessions" {{ old('category', $faq->category) === 'sessions' ? 'selected' : '' }}>
                                    Séances
                                </option>
                                <option value="account" {{ old('category', $faq->category) === 'account' ? 'selected' : '' }}>
                                    Compte
                                </option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="answer" class="form-label">
                                Réponse <span class="text-danger">*</span>
                            </label>
                            <textarea name="answer" id="answer" rows="6" 
                                      class="form-control @error('answer') is-invalid @enderror" 
                                      required maxlength="2000">{{ old('answer', $faq->answer) }}</textarea>
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Fournissez une réponse détaillée et utile. 
                                Vous pouvez utiliser du formatage Markdown.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_public" id="is_public" 
                                       class="form-check-input" value="1" 
                                       {{ old('is_public', $faq->is_public) ? 'checked' : '' }}>
                                <label for="is_public" class="form-check-label">
                                    Rendre cette question publique
                                </label>
                                <div class="form-text">
                                    Les questions publiques sont visibles par tous les utilisateurs
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>
                                Mettre à jour
                            </button>
                            <a href="{{ route('faqs.show', $faq) }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informations sur cette question
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Auteur :</strong> {{ $faq->user->name }}</p>
                            <p class="mb-1"><strong>Créée le :</strong> {{ $faq->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Dernière modification :</strong> {{ $faq->updated_at->format('d/m/Y à H:i') }}</p>
                            <p class="mb-0"><strong>Statut :</strong> 
                                @if($faq->is_public)
                                    <span class="badge bg-success">Public</span>
                                @else
                                    <span class="badge bg-secondary">Privé</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 