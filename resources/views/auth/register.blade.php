@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-4 text-center">Inscription</h3>
    <form method="POST" action="{{ route('register') }}">
        @csrf

                        <!-- Informations de base -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet *</label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Adresse e-mail *</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username">
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
                                    <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Rôle *</label>
                                    <select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required>
                                        <option value="">Choisir un rôle</option>
                                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Étudiant</option>
                                        <option value="tutor" {{ old('role') == 'tutor' ? 'selected' : '' }}>Tuteur</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Niveau *</label>
                                    <select id="level" class="form-select @error('level') is-invalid @enderror" name="level" required>
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
                            <div class="col-md-6" id="hourly-rate-group" style="display: none;">
                                <div class="mb-3">
                                    <label for="hourly_rate" class="form-label">Tarif horaire (€)</label>
                                    <input id="hourly_rate" type="number" step="0.01" min="0" max="1000" class="form-control @error('hourly_rate') is-invalid @enderror" name="hourly_rate" value="{{ old('hourly_rate') }}">
                                    @error('hourly_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="skills" class="form-label">Compétences (séparées par des virgules)</label>
                            <input id="skills" type="text" class="form-control @error('skills') is-invalid @enderror" name="skills" value="{{ old('skills') }}" placeholder="Ex: Java, Python, Mathématiques, Anglais">
                            <div class="form-text">Exemples: Java, Python, Mathématiques, Anglais, Réseaux, etc.</div>
                            @error('skills')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea id="bio" class="form-control @error('bio') is-invalid @enderror" name="bio" rows="3" placeholder="Parlez-nous un peu de vous...">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
        </div>

                        <!-- Mot de passe -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                                    <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="{{ route('login') }}" class="small">Déjà inscrit ?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const hourlyRateGroup = document.getElementById('hourly-rate-group');
    if (this.value === 'tutor') {
        hourlyRateGroup.style.display = 'block';
    } else {
        hourlyRateGroup.style.display = 'none';
    }
});
</script>
@endsection
