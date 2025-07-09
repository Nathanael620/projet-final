@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                        Demander une séance de soutien
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sessions.store') }}">
                        @csrf
                        
                        <!-- Sélection du tuteur -->
                        <div class="mb-4">
                            <label for="tutor_id" class="form-label">Choisir un tuteur</label>
                            <select name="tutor_id" id="tutor_id" class="form-select @error('tutor_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez un tuteur</option>
                                @foreach($tutors as $tutor)
                                    <option value="{{ $tutor->id }}" 
                                            {{ old('tutor_id', request('tutor_id')) == $tutor->id ? 'selected' : '' }}>
                                        {{ $tutor->name }} - {{ ucfirst($tutor->level) }} 
                                        ({{ number_format($tutor->hourly_rate ?? 20, 2) }}€/h)
                                        - Note: {{ number_format($tutor->rating ?? 0, 1) }}/5
                                    </option>
                                @endforeach
                            </select>
                            @error('tutor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Titre de la séance -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre de la séance</label>
                            <input type="text" name="title" id="title" 
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            <div class="form-text">Décrivez ce que vous souhaitez travailler pendant cette séance.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Matière -->
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">Matière</label>
                                <select name="subject" id="subject" class="form-select @error('subject') is-invalid @enderror" required>
                                    <option value="">Sélectionnez une matière</option>
                                    <option value="mathematics" {{ old('subject') == 'mathematics' ? 'selected' : '' }}>Mathématiques</option>
                                    <option value="physics" {{ old('subject') == 'physics' ? 'selected' : '' }}>Physique</option>
                                    <option value="chemistry" {{ old('subject') == 'chemistry' ? 'selected' : '' }}>Chimie</option>
                                    <option value="biology" {{ old('subject') == 'biology' ? 'selected' : '' }}>Biologie</option>
                                    <option value="computer_science" {{ old('subject') == 'computer_science' ? 'selected' : '' }}>Informatique</option>
                                    <option value="languages" {{ old('subject') == 'languages' ? 'selected' : '' }}>Langues</option>
                                    <option value="literature" {{ old('subject') == 'literature' ? 'selected' : '' }}>Littérature</option>
                                    <option value="history" {{ old('subject') == 'history' ? 'selected' : '' }}>Histoire</option>
                                    <option value="geography" {{ old('subject') == 'geography' ? 'selected' : '' }}>Géographie</option>
                                    <option value="economics" {{ old('subject') == 'economics' ? 'selected' : '' }}>Économie</option>
                                    <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Niveau -->
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Niveau</label>
                                <select name="level" id="level" class="form-select @error('level') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un niveau</option>
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Débutant</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Avancé</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type de séance -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type de séance</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="online" {{ old('type') == 'online' ? 'selected' : '' }}>En ligne</option>
                                    <option value="in_person" {{ old('type') == 'in_person' ? 'selected' : '' }}>En présentiel</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Durée -->
                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">Durée (minutes)</label>
                                <select name="duration_minutes" id="duration_minutes" class="form-select @error('duration_minutes') is-invalid @enderror" required>
                                    <option value="">Sélectionnez une durée</option>
                                    <option value="30" {{ old('duration_minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                                    <option value="60" {{ old('duration_minutes') == '60' ? 'selected' : '' }}>1 heure</option>
                                    <option value="90" {{ old('duration_minutes') == '90' ? 'selected' : '' }}>1h30</option>
                                    <option value="120" {{ old('duration_minutes') == '120' ? 'selected' : '' }}>2 heures</option>
                                    <option value="180" {{ old('duration_minutes') == '180' ? 'selected' : '' }}>3 heures</option>
                                </select>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date et heure -->
                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Date et heure de la séance</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                                   class="form-control @error('scheduled_at') is-invalid @enderror"
                                   value="{{ old('scheduled_at') }}" required>
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lieu (conditionnel) -->
                        <div class="mb-4" id="location_field" style="display: none;">
                            <label for="location" class="form-label">Lieu de la séance</label>
                            <input type="text" name="location" id="location" 
                                   class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location') }}" placeholder="Adresse ou lieu de rendez-vous">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Résumé du coût -->
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Coût estimé :</span>
                                <strong id="estimated_cost">-- €</strong>
                            </div>
                            <small class="text-muted">Le coût final sera calculé en fonction du tuteur sélectionné et de la durée.</small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-2"></i>
                                Demander la séance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tutorSelect = document.getElementById('tutor_id');
    const durationSelect = document.getElementById('duration_minutes');
    const typeSelect = document.getElementById('type');
    const locationField = document.getElementById('location_field');
    const locationInput = document.getElementById('location');
    const estimatedCost = document.getElementById('estimated_cost');
    
    // Afficher/masquer le champ lieu selon le type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'in_person') {
            locationField.style.display = 'block';
            locationInput.required = true;
        } else {
            locationField.style.display = 'none';
            locationInput.required = false;
        }
    });
    
    // Calculer le coût estimé
    function calculateCost() {
        const tutorId = tutorSelect.value;
        const duration = durationSelect.value;
        
        if (tutorId && duration) {
            const selectedOption = tutorSelect.options[tutorSelect.selectedIndex];
            const hourlyRate = parseFloat(selectedOption.text.match(/\(([\d.]+)€\/h\)/)[1]);
            const cost = (hourlyRate * duration) / 60;
            estimatedCost.textContent = cost.toFixed(2) + ' €';
        } else {
            estimatedCost.textContent = '-- €';
        }
    }
    
    tutorSelect.addEventListener('change', calculateCost);
    durationSelect.addEventListener('change', calculateCost);
    
    // Initialiser l'affichage du champ lieu
    if (typeSelect.value === 'in_person') {
        locationField.style.display = 'block';
        locationInput.required = true;
    }
});
</script>
@endpush
@endsection 