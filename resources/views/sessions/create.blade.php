@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Demander une séance de soutien
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sessions.store') }}">
                        @csrf
                        
                        <!-- Sélection du tuteur -->
                        <div class="mb-4">
                            <label for="tutor_id" class="form-label">Choisir un tuteur *</label>
                            <select name="tutor_id" id="tutor_id" class="form-select @error('tutor_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un tuteur</option>
                                @foreach($tutors as $tutor)
                                    <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>
                                        {{ $tutor->name }} - {{ $tutor->getSkillsString() }} 
                                        ({{ $tutor->hourly_rate ?? 20 }}€/h)
                                        {!! $tutor->getRatingStars() !!}
                                    </option>
                                @endforeach
                            </select>
                            @error('tutor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informations de base -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titre de la séance *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Matière *</label>
                                    <select name="subject" id="subject" class="form-select @error('subject') is-invalid @enderror" required>
                                        <option value="">Choisir une matière</option>
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
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description de vos besoins *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required 
                                      placeholder="Décrivez ce sur quoi vous souhaitez travailler, vos difficultés, etc.">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Niveau et type -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Niveau *</label>
                                    <select name="level" id="level" class="form-select @error('level') is-invalid @enderror" required>
                                        <option value="">Choisir un niveau</option>
                                        <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Débutant</option>
                                        <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                                        <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Avancé</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type de séance *</label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">Choisir un type</option>
                                        <option value="online" {{ old('type') == 'online' ? 'selected' : '' }}>En ligne</option>
                                        <option value="in_person" {{ old('type') == 'in_person' ? 'selected' : '' }}>En présentiel</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Durée *</label>
                                    <select name="duration_minutes" id="duration_minutes" class="form-select @error('duration_minutes') is-invalid @enderror" required>
                                        <option value="">Choisir une durée</option>
                                        <option value="30" {{ old('duration_minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                                        <option value="60" {{ old('duration_minutes') == '60' ? 'selected' : '' }}>1 heure</option>
                                        <option value="90" {{ old('duration_minutes') == '90' ? 'selected' : '' }}>1h30</option>
                                        <option value="120" {{ old('duration_minutes') == '120' ? 'selected' : '' }}>2 heures</option>
                                        <option value="180" {{ old('duration_minutes') == '180' ? 'selected' : '' }}>3 heures</option>
                                        <option value="240" {{ old('duration_minutes') == '240' ? 'selected' : '' }}>4 heures</option>
                                    </select>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Date et heure -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_at" class="form-label">Date et heure *</label>
                                    <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                           id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}" required>
                                    @error('scheduled_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="location-group" style="display: none;">
                                    <label for="location" class="form-label">Lieu de rendez-vous *</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                           id="location" name="location" value="{{ old('location') }}" 
                                           placeholder="Adresse ou lieu de rencontre">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informations sur le prix -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information :</strong> Le prix sera calculé automatiquement selon le tarif horaire du tuteur sélectionné.
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                Demander la séance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const locationGroup = document.getElementById('location-group');
    const locationInput = document.getElementById('location');
    
    if (this.value === 'in_person') {
        locationGroup.style.display = 'block';
        locationInput.required = true;
    } else {
        locationGroup.style.display = 'none';
        locationInput.required = false;
    }
});

// Déclencher l'événement au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('type').dispatchEvent(new Event('change'));
});
</script>
@endsection 