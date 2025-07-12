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
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="generate-ai-answer">
                                    <i class="fas fa-magic me-1"></i>
                                    Générer avec l'IA
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="improve-answer">
                                    <i class="fas fa-wand-magic-sparkles me-1"></i>
                                    Améliorer la réponse
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" id="find-similar">
                                    <i class="fas fa-search me-1"></i>
                                    Questions similaires
                                </button>
                            </div>
                            <textarea name="answer" id="answer" rows="6" 
                                      class="form-control @error('answer') is-invalid @enderror" 
                                      required maxlength="2000">{{ old('answer') }}</textarea>
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Fournissez une réponse détaillée et utile.
                                <span id="char-count">0/2000</span>
                            </div>
                        </div>

                        <!-- Zone de suggestions IA -->
                        <div id="ai-suggestions" class="mb-3" style="display: none;">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    Suggestions IA
                                </div>
                                <div class="card-body">
                                    <div id="suggestions-content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Zone de questions similaires -->
                        <div id="similar-questions" class="mb-3" style="display: none;">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <i class="fas fa-search me-2"></i>
                                    Questions similaires trouvées
                                </div>
                                <div class="card-body">
                                    <div id="similar-content"></div>
                                </div>
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

<!-- Modal de chargement IA -->
<div class="modal fade" id="aiLoadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <h6>L'IA réfléchit...</h6>
                <p class="text-muted mb-0">Génération de la réponse en cours</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionInput = document.getElementById('question');
    const answerInput = document.getElementById('answer');
    const categorySelect = document.getElementById('category');
    const generateBtn = document.getElementById('generate-ai-answer');
    const improveBtn = document.getElementById('improve-answer');
    const findSimilarBtn = document.getElementById('find-similar');
    const charCount = document.getElementById('char-count');
    const aiSuggestions = document.getElementById('ai-suggestions');
    const similarQuestions = document.getElementById('similar-questions');
    
    // Compteur de caractères
    answerInput.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count}/2000`;
        charCount.className = count > 1800 ? 'text-danger' : count > 1500 ? 'text-warning' : 'text-muted';
    });
    
    // Générer une réponse IA
    generateBtn.addEventListener('click', function() {
        const question = questionInput.value.trim();
        const category = categorySelect.value;
        
        if (!question) {
            alert('Veuillez d\'abord saisir une question.');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Génération...';
        
        fetch('{{ route("faqs.generate-ai-answer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                question: question,
                category: category
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                answerInput.value = data.answer;
                answerInput.dispatchEvent(new Event('input')); // Déclencher le compteur
                
                // Afficher les suggestions
                if (data.suggestions) {
                    showSuggestions(data.suggestions);
                }
            } else {
                alert(data.message || 'Erreur lors de la génération.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de connexion. Veuillez réessayer.');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-magic me-1"></i>Générer avec l\'IA';
        });
    });
    
    // Améliorer la réponse
    improveBtn.addEventListener('click', function() {
        const question = questionInput.value.trim();
        const answer = answerInput.value.trim();
        
        if (!question || !answer) {
            alert('Veuillez saisir une question et une réponse.');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Analyse...';
        
        fetch('{{ route("faqs.improve-answer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                question: question,
                answer: answer
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuggestions(data.improvements);
            } else {
                alert(data.message || 'Erreur lors de l\'analyse.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de connexion. Veuillez réessayer.');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-wand-magic-sparkles me-1"></i>Améliorer la réponse';
        });
    });
    
    // Trouver des questions similaires
    findSimilarBtn.addEventListener('click', function() {
        const question = questionInput.value.trim();
        
        if (!question) {
            alert('Veuillez d\'abord saisir une question.');
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Recherche...';
        
        fetch('{{ route("faqs.find-similar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                question: question
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSimilarQuestions(data.results);
            } else {
                alert(data.message || 'Erreur lors de la recherche.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de connexion. Veuillez réessayer.');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-search me-1"></i>Questions similaires';
        });
    });
    
    function showSuggestions(suggestions) {
        const content = document.getElementById('suggestions-content');
        let html = '';
        
        if (suggestions.suggestions && suggestions.suggestions.length > 0) {
            html += '<h6>Suggestions :</h6><ul>';
            suggestions.suggestions.forEach(suggestion => {
                html += `<li>${suggestion}</li>`;
            });
            html += '</ul>';
        }
        
        if (suggestions.improvements && suggestions.improvements.length > 0) {
            html += '<h6>Améliorations suggérées :</h6><ul>';
            suggestions.improvements.forEach(improvement => {
                html += `<li>${improvement}</li>`;
            });
            html += '</ul>';
        }
        
        if (suggestions.score !== undefined) {
            html += `<p><strong>Score de qualité :</strong> ${suggestions.score}/10</p>`;
        }
        
        content.innerHTML = html;
        aiSuggestions.style.display = 'block';
    }
    
    function showSimilarQuestions(results) {
        const content = document.getElementById('similar-content');
        
        if (results.length === 0) {
            content.innerHTML = '<p class="text-muted">Aucune question similaire trouvée.</p>';
        } else {
            let html = '<div class="list-group">';
            results.forEach(faq => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${faq.question}</h6>
                                <p class="mb-1 small text-muted">${faq.answer}</p>
                                <div class="mt-2">
                                    <span class="badge bg-secondary">${faq.category}</span>
                                    <span class="badge bg-success">${faq.votes} votes</span>
                                </div>
                                </div>
                            <a href="${faq.url}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                </div>
            </div>
        `;
            });
            html += '</div>';
            content.innerHTML = html;
        }
        
        similarQuestions.style.display = 'block';
    }
});
</script>
@endpush
@endsection 