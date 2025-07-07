@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- En-tête -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Nouvelle Question
                    </h1>
                    <p class="text-muted mb-0">Ajoutez une nouvelle question à la FAQ</p>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('faqs.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="question" class="form-label">
                                Question <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="question" id="question" 
                                   class="form-control @error('question') is-invalid @enderror" 
                                   value="{{ old('question') }}" required maxlength="255">
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formulez une question claire et concise</div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="category" 
                                    class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>
                                    Général
                                </option>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>
                                    Technique
                                </option>
                                <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>
                                    Paiement
                                </option>
                                <option value="sessions" {{ old('category') === 'sessions' ? 'selected' : '' }}>
                                    Séances
                                </option>
                                <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>
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
                                      required maxlength="2000">{{ old('answer') }}</textarea>
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
                                       {{ old('is_public') ? 'checked' : '' }}>
                                <label for="is_public" class="form-check-label">
                                    Rendre cette question publique
                                </label>
                                <div class="form-text">
                                    Les questions publiques sont visibles par tous les utilisateurs
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Créer la question
                            </button>
                            <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Conseils -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Conseils pour une bonne FAQ
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Formulez des questions claires et spécifiques</li>
                        <li>Donnez des réponses complètes et utiles</li>
                        <li>Utilisez des exemples quand c'est possible</li>
                        <li>Choisissez la bonne catégorie pour faciliter la recherche</li>
                        <li>Rendez publiques les questions d'intérêt général</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 