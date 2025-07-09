<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="update_password_current_password" class="form-label">Mot de passe actuel *</label>
                <input type="password" name="current_password" id="update_password_current_password" 
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                       autocomplete="current-password" required>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="update_password_password" class="form-label">Nouveau mot de passe *</label>
                <input type="password" name="password" id="update_password_password" 
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                       autocomplete="new-password" required>
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Le mot de passe doit contenir au moins 8 caractères
                </div>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label">Confirmer le nouveau mot de passe *</label>
                <input type="password" name="password_confirmation" id="update_password_password_confirmation" 
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                       autocomplete="new-password" required>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Conseils de sécurité -->
    <div class="alert alert-info mb-3">
        <h6 class="alert-heading">
            <i class="fas fa-shield-alt me-2"></i>
            Conseils pour un mot de passe sécurisé
        </h6>
        <ul class="mb-0 small">
            <li>Utilisez au moins 8 caractères</li>
            <li>Combinez lettres majuscules, minuscules, chiffres et symboles</li>
            <li>Évitez les informations personnelles (nom, date de naissance, etc.)</li>
            <li>N'utilisez pas le même mot de passe que sur d'autres sites</li>
        </ul>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-key me-2"></i>
            Changer le mot de passe
        </button>
        
        @if (session('status') === 'password-updated')
            <div class="alert alert-success mb-0 py-2 px-3">
                <i class="fas fa-check me-2"></i>
                Mot de passe mis à jour avec succès !
            </div>
        @endif
    </div>
</form>
