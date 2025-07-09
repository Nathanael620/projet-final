<div class="row">
    <div class="col-md-6">
        <div class="alert alert-danger">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Attention : Suppression définitive du compte
            </h6>
            <p class="mb-3">
                Cette action est irréversible. Une fois votre compte supprimé, toutes vos données seront définitivement perdues :
            </p>
            <ul class="mb-3">
                <li>Toutes vos séances de tutorat</li>
                <li>Vos messages et conversations</li>
                <li>Votre profil et vos informations personnelles</li>
                <li>Vos évaluations et commentaires</li>
            </ul>
            <p class="mb-0">
                <strong>Assurez-vous de sauvegarder toutes les informations importantes avant de procéder.</strong>
            </p>
        </div>

        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="fas fa-trash me-2"></i>
            Supprimer mon compte
        </button>
    </div>

    <div class="col-md-6">
        <div class="alert alert-warning">
            <h6 class="alert-heading">
                <i class="fas fa-pause-circle me-2"></i>
                Alternative : Désactiver temporairement
            </h6>
            <p class="mb-3">
                Si vous souhaitez faire une pause, vous pouvez désactiver votre compte temporairement :
            </p>
            <ul class="mb-3">
                <li>Vos données sont conservées</li>
                <li>Vous pouvez réactiver à tout moment</li>
                <li>Votre profil devient invisible</li>
                <li>Vous ne recevez plus de notifications</li>
            </ul>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#deactivateAccountModal">
                <i class="fas fa-pause me-2"></i>
                Désactiver temporairement
            </button>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">Êtes-vous absolument sûr ?</h6>
                        <p class="mb-0">Cette action ne peut pas être annulée. Toutes vos données seront définitivement supprimées.</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Confirmez votre mot de passe</label>
                        <input type="password" name="password" id="password" 
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Entrez votre mot de passe pour confirmer" required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirmDelete" name="confirmation" value="1" required>
                        <label class="form-check-label" for="confirmDelete">
                            Je comprends que cette action est irréversible et que toutes mes données seront supprimées
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-danger" id="deleteButton" disabled>
                        <i class="fas fa-trash me-2"></i>
                        Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de désactivation -->
<div class="modal fade" id="deactivateAccountModal" tabindex="-1" aria-labelledby="deactivateAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-warning" id="deactivateAccountModalLabel">
                    <i class="fas fa-pause-circle me-2"></i>
                    Désactiver le compte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.deactivate') }}">
                @csrf
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Désactivation temporaire</h6>
                        <p class="mb-0">Votre compte sera désactivé mais vos données seront conservées. Vous pourrez le réactiver plus tard.</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deactivatePassword" class="form-label">Confirmez votre mot de passe</label>
                        <input type="password" name="password" id="deactivatePassword" 
                               class="form-control @error('password', 'deactivation') is-invalid @enderror"
                               placeholder="Entrez votre mot de passe pour confirmer" required>
                        @error('password', 'deactivation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="deactivationReason" class="form-label">Raison (optionnel)</label>
                        <textarea name="reason" id="deactivationReason" rows="3" 
                                  class="form-control"
                                  placeholder="Pourquoi désactivez-vous votre compte ?"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-pause me-2"></i>
                        Désactiver le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmDelete');
    const deleteButton = document.getElementById('deleteButton');
    
    if (confirmCheckbox && deleteButton) {
        confirmCheckbox.addEventListener('change', function() {
            deleteButton.disabled = !this.checked;
        });
    }
});
</script>
