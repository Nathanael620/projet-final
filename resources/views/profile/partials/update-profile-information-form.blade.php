<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="row">
        <!-- Informations de base -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Nom complet *</label>
                <input type="text" name="name" id="name" 
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" name="email" id="email" 
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="form-text">
                        <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                        Votre email n'est pas vérifié.
                        <button form="send-verification" class="btn btn-link btn-sm p-0 ms-1">
                            Renvoyer l'email de vérification
                        </button>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Téléphone</label>
                <input type="tel" name="phone" id="phone" 
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $user->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="level" class="form-label">Niveau *</label>
                <select name="level" id="level" class="form-select @error('level') is-invalid @enderror" required>
                    <option value="">Sélectionnez votre niveau</option>
                    <option value="beginner" {{ old('level', $user->level) == 'beginner' ? 'selected' : '' }}>Débutant</option>
                    <option value="intermediate" {{ old('level', $user->level) == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                    <option value="advanced" {{ old('level', $user->level) == 'advanced' ? 'selected' : '' }}>Avancé</option>
                </select>
                @error('level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Avatar et Bio -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="avatar" class="form-label">Photo de profil</label>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" 
                                 alt="Avatar actuel" 
                                 class="rounded-circle" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <i class="fas fa-user-circle fa-3x text-muted"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="avatar" id="avatar" 
                               class="form-control @error('avatar') is-invalid @enderror"
                               accept="image/*">
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF (max 2MB)</div>
                        @error('avatar')
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
        </div>
    </div>

    <!-- Compétences (pour les tuteurs) -->
    @if($user->isTutor())
    <div class="mb-3">
        <label class="form-label">Compétences</label>
        <div class="row g-2">
            @php
                $availableSkills = [
                    'mathematics' => 'Mathématiques',
                    'physics' => 'Physique',
                    'chemistry' => 'Chimie',
                    'biology' => 'Biologie',
                    'computer_science' => 'Informatique',
                    'languages' => 'Langues',
                    'literature' => 'Littérature',
                    'history' => 'Histoire',
                    'geography' => 'Géographie',
                    'economics' => 'Économie',
                    'philosophy' => 'Philosophie',
                    'art' => 'Art',
                    'music' => 'Musique',
                    'sports' => 'Sport',
                    'other' => 'Autre'
                ];
                $userSkills = $user->skills ?? [];
            @endphp
            
            @foreach($availableSkills as $key => $label)
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                    <input type="checkbox" name="skills[]" value="{{ $key }}" 
                           class="form-check-input" id="skill_{{ $key }}"
                           {{ in_array($key, $userSkills) ? 'checked' : '' }}>
                    <label class="form-check-label" for="skill_{{ $key }}">
                        {{ $label }}
                    </label>
                </div>
            </div>
            @endforeach
        </div>
        @error('skills')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>
            Sauvegarder les modifications
        </button>
        
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-0 py-2 px-3">
                <i class="fas fa-check me-2"></i>
                Profil mis à jour avec succès !
            </div>
        @endif
    </div>
</form>

<!-- Formulaire pour la vérification email -->
<form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-none">
    @csrf
</form>
