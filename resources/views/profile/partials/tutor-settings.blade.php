<div class="row">
    <!-- Tarif horaire -->
    <div class="col-md-6">
        <div class="mb-3">
            <label for="hourly_rate" class="form-label">Tarif horaire (€)</label>
            <div class="input-group">
                <input type="number" name="hourly_rate" id="hourly_rate" 
                       class="form-control @error('hourly_rate') is-invalid @enderror"
                       value="{{ old('hourly_rate', $user->hourly_rate ?? 20) }}" 
                       min="5" max="200" step="0.50">
                <span class="input-group-text">€/heure</span>
            </div>
            <div class="form-text">Tarif recommandé : 15-50€/heure</div>
            @error('hourly_rate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Disponibilité -->
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Statut de disponibilité</label>
            <div class="d-flex align-items-center">
                <div class="form-check form-switch me-3">
                    <input type="checkbox" name="is_available" id="is_available" 
                           class="form-check-input" 
                           {{ $user->is_available ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_available">
                        Disponible pour les séances
                    </label>
                </div>
                <span class="badge {{ $user->is_available ? 'bg-success' : 'bg-danger' }}">
                    {{ $user->is_available ? 'Disponible' : 'Indisponible' }}
                </span>
            </div>
            <div class="form-text">Les étudiants pourront vous contacter si vous êtes disponible</div>
        </div>
    </div>
</div>

<!-- Statistiques du tuteur -->
<div class="row mb-3">
    <div class="col-12">
        <h6 class="text-muted mb-3">Vos statistiques</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body text-center">
                        <h5 class="text-primary mb-1">{{ $user->tutorSessions()->count() }}</h5>
                        <small class="text-muted">Séances totales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body text-center">
                        <h5 class="text-success mb-1">{{ $user->tutorSessions()->where('status', 'completed')->count() }}</h5>
                        <small class="text-muted">Séances terminées</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body text-center">
                        <h5 class="text-warning mb-1">{{ number_format($user->rating ?? 0, 1) }}</h5>
                        <small class="text-muted">Note moyenne</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body text-center">
                        <h5 class="text-info mb-1">{{ number_format($user->tutorSessions()->where('status', 'completed')->sum('price'), 2) }}€</h5>
                        <small class="text-muted">Gains totaux</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-2"></i>
        Sauvegarder les paramètres
    </button>
    
    <a href="{{ route('sessions.index') }}" class="btn btn-outline-info">
        <i class="fas fa-calendar me-2"></i>
        Voir mes séances
    </a>
    
    <a href="{{ route('tutors.show', $user) }}" class="btn btn-outline-secondary">
        <i class="fas fa-eye me-2"></i>
        Voir mon profil public
    </a>
</div> 