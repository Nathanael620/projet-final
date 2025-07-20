@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Modifier mon avis
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Informations sur la séance -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Séance : {{ $feedback->session->title }}</h6>
                        <p class="mb-1"><strong>Date :</strong> {{ $feedback->session->scheduled_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-1"><strong>Matière :</strong> {{ ucfirst($feedback->session->subject) }}</p>
                        <p class="mb-0"><strong>Vous notez :</strong> {{ $feedback->reviewed->name }}</p>
                    </div>

                    <form method="POST" action="{{ route('feedback.update', $feedback) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Notation par étoiles -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Votre note</label>
                            <div class="rating-stars mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="rating-input" {{ old('rating', $feedback->rating) == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" class="rating-star">
                                        <i class="far fa-star"></i>
                                    </label>
                                @endfor
                            </div>
                            <div class="rating-labels">
                                <small class="text-muted">
                                    <span id="rating-text">Sélectionnez une note</span>
                                </small>
                            </div>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">Votre commentaire (optionnel)</label>
                            <textarea 
                                name="comment" 
                                id="comment" 
                                rows="4" 
                                class="form-control @error('comment') is-invalid @enderror"
                                placeholder="Partagez votre expérience avec cette séance..."
                                maxlength="1000"
                            >{{ old('comment', $feedback->comment) }}</textarea>
                            <div class="form-text">
                                <span id="char-count">0</span>/1000 caractères
                            </div>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Options -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_anonymous" id="is_anonymous" class="form-check-input" value="1" {{ old('is_anonymous', $feedback->is_anonymous) ? 'checked' : '' }}>
                                <label for="is_anonymous" class="form-check-label">
                                    Rendre ce commentaire anonyme
                                </label>
                            </div>
                        </div>

                        <!-- Informations sur la modification -->
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Vous ne pouvez modifier votre avis que dans les 24h suivant sa création.
                                <br>
                                <strong>Créé le :</strong> {{ $feedback->created_at->format('d/m/Y H:i') }}
                                <br>
                                <strong>Dernière modification :</strong> {{ $feedback->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('feedback.my-feedbacks') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars {
    display: flex;
    gap: 5px;
    font-size: 2rem;
}

.rating-input {
    display: none;
}

.rating-star {
    cursor: pointer;
    color: #ddd;
    transition: color 0.2s ease;
}

.rating-star:hover,
.rating-star:hover ~ .rating-star {
    color: #ffc107;
}

.rating-input:checked ~ .rating-star {
    color: #ffc107;
}

.rating-input:checked + .rating-star {
    color: #ffc107;
}

.rating-input:checked ~ .rating-star:before {
    content: '\f005';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

.rating-star i {
    transition: transform 0.2s ease;
}

.rating-star:hover i {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('.rating-input');
    const ratingText = document.getElementById('rating-text');
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    
    const ratingLabels = {
        1: 'Très décevant',
        2: 'Décevant',
        3: 'Correct',
        4: 'Très bien',
        5: 'Excellent'
    };
    
    // Gestion des étoiles
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const rating = this.value;
            ratingText.textContent = ratingLabels[rating] || 'Sélectionnez une note';
        });
    });
    
    // Compteur de caractères
    commentTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 900) {
            charCount.style.color = '#dc3545';
        } else if (length > 800) {
            charCount.style.color = '#ffc107';
        } else {
            charCount.style.color = '#6c757d';
        }
    });
    
    // Initialiser le compteur
    charCount.textContent = commentTextarea.value.length;
    
    // Initialiser le texte de notation si une note est déjà sélectionnée
    const checkedRating = document.querySelector('.rating-input:checked');
    if (checkedRating) {
        ratingText.textContent = ratingLabels[checkedRating.value] || 'Sélectionnez une note';
    }
});
</script>
@endsection 