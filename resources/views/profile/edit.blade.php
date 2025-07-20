@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- En-tête du profil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Section Avatar (gérée par JavaScript) -->
                    <div class="row align-items-center mb-4">
                            <div class="col-md-3 text-center">
                                <div class="position-relative mb-3">
                                <div class="avatar-container">
                                    <img id="avatar-preview"
                                         src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3B82F6&color=fff&size=120&bold=true' }}"
                                         alt="{{ $user->name }}"
                                         class="rounded-circle border shadow-sm"
                                         style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;"
                                         title="Cliquez pour voir en grand">

                                    <!-- Indicateur de chargement -->
                                    <div class="avatar-loading position-absolute top-50 start-50 translate-middle" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                    </div>

                                    <!-- Badge de rôle -->
                                    <div class="position-absolute bottom-0 end-0">
                                        <span class="badge bg-{{ $user->isTutor() ? 'success' : 'primary' }} fs-6">
                                            {{ $user->isTutor() ? 'Tuteur' : 'Étudiant' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Zone de téléchargement d'avatar -->
                                <div class="mt-3">
                                    <div class="d-grid gap-2">
                                        <label for="avatar" class="btn btn-outline-primary btn-sm avatar-btn">
                                            <i class="fas fa-camera me-2"></i>Changer la photo
                                        </label>
                                        @if($user->avatar)
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-avatar-btn avatar-btn">
                                                <i class="fas fa-trash me-2"></i>Supprimer
                                            </button>
                                        @endif
                                    </div>
                                    <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                </div>
                            </div>
                        </div>

                            <div class="col-md-9">
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">{{ ucfirst($user->level) }}</p>
                            @if($user->bio)
                                <p class="text-muted">{{ Str::limit($user->bio, 150) }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Formulaire des informations de profil -->
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet</label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Adresse email</label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                        </div>
                                    </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Niveau</label>
                                    <select name="level" id="level" 
                                            class="form-select @error('level') is-invalid @enderror" required>
                                        <option value="">Sélectionnez votre niveau</option>
                                        <option value="beginner" {{ old('level', $user->level) == 'beginner' ? 'selected' : '' }}>
                                            Débutant
                                        </option>
                                        <option value="intermediate" {{ old('level', $user->level) == 'intermediate' ? 'selected' : '' }}>
                                            Intermédiaire
                                        </option>
                                        <option value="advanced" {{ old('level', $user->level) == 'advanced' ? 'selected' : '' }}>
                                            Avancé
                                        </option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                        </div>
                                    </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea name="bio" id="bio" rows="4" 
                                      class="form-control @error('bio') is-invalid @enderror"
                                      placeholder="Parlez-nous un peu de vous...">{{ old('bio', $user->bio) }}</textarea>
                            <div class="form-text">Maximum 1000 caractères</div>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Compétences (pour les tuteurs) -->
                                            @if($user->isTutor())
                            <div class="mb-3">
                                <label class="form-label">Compétences</label>
                                <div class="row">
                                    @foreach(config('profile.skills') as $key => $skill)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" name="skills[]" 
                                                       value="{{ $key }}" 
                                                       class="form-check-input"
                                                       id="skill_{{ $key }}"
                                                       {{ in_array($key, old('skills', $user->skills ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="skill_{{ $key }}">
                                                    {{ $skill }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hourly_rate" class="form-label">Tarif horaire (€)</label>
                                        <input type="number" name="hourly_rate" id="hourly_rate" 
                                               class="form-control @error('hourly_rate') is-invalid @enderror"
                                               value="{{ old('hourly_rate', $user->hourly_rate) }}"
                                               min="5" max="200" step="0.50">
                                        @error('hourly_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Disponibilité</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_available" 
                                                   class="form-check-input" id="is_available"
                                                   {{ old('is_available', $user->is_available) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_available">
                                                Disponible pour les séances
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques du profil -->
    @include('components.profile-stats', ['stats' => $stats])

    <!-- Autres sections du profil -->
    <div class="row">
        <div class="col-md-6">
                    @include('profile.partials.update-password-form')
        </div>
        <div class="col-md-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>

<!-- Meta tags pour JavaScript -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ $user->id }}">

<!-- Scripts -->
@push('scripts')
<script src="{{ asset('js/avatar-manager.js') }}"></script>
@endpush

<!-- Styles CSS personnalisés -->
@push('styles')
<style>
.avatar-container {
    position: relative;
    display: inline-block;
}

.avatar-container img:hover {
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.avatar-loading {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    padding: 10px;
}

.form-check-input:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.btn-outline-primary:hover {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.btn-outline-danger:hover {
    background-color: #EF4444;
    border-color: #EF4444;
}
</style>
@endpush
@endsection
