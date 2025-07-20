class AvatarManager {
    constructor() {
        this.initializeEventListeners();
        this.currentFile = null;
    }

    initializeEventListeners() {
        // Gestion du changement d'avatar
        const avatarInput = document.getElementById('avatar');
        if (avatarInput) {
            avatarInput.addEventListener('change', (e) => this.handleAvatarChange(e));
        }

        // Gestion de la suppression d'avatar
        const removeAvatarBtn = document.querySelector('.remove-avatar-btn');
        if (removeAvatarBtn) {
            removeAvatarBtn.addEventListener('click', (e) => this.handleAvatarRemove(e));
        }

        // Prévisualisation de l'avatar
        const avatarPreview = document.getElementById('avatar-preview');
        if (avatarPreview) {
            avatarPreview.addEventListener('click', () => this.openAvatarModal());
        }
    }

    handleAvatarChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validation du fichier
        if (!this.validateFile(file)) {
            this.showError('Format de fichier non supporté. Utilisez JPG, PNG ou GIF (max 2MB).');
            return;
        }

        this.currentFile = file;
        this.uploadAvatar(file);
    }

    validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        return allowedTypes.includes(file.type) && file.size <= maxSize;
    }

    async uploadAvatar(file) {
        try {
            this.showLoading(true);

            const formData = new FormData();
            formData.append('avatar', file);

            const response = await fetch('/avatar/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.updateAvatarDisplay(data.avatar_url);
                this.resetAvatarInput();
            } else {
                this.showError(data.message || 'Erreur lors de l\'upload');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showError('Erreur de connexion. Veuillez réessayer.');
        } finally {
            this.showLoading(false);
        }
    }

    async handleAvatarRemove(event) {
        event.preventDefault();

        if (!confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
            return;
        }

        try {
            this.showLoading(true);

            const response = await fetch('/avatar/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.updateAvatarDisplay(data.default_avatar_url);
                this.resetAvatarInput();
                
                // Masquer le bouton de suppression
                const removeBtn = document.querySelector('.remove-avatar-btn');
                if (removeBtn) {
                    removeBtn.style.display = 'none';
                }
            } else {
                this.showError(data.message || 'Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Remove error:', error);
            this.showError('Erreur de connexion. Veuillez réessayer.');
        } finally {
            this.showLoading(false);
        }
    }

    updateAvatarDisplay(avatarUrl) {
        const avatarPreview = document.getElementById('avatar-preview');
        if (avatarPreview) {
            avatarPreview.src = avatarUrl;
            avatarPreview.style.display = 'block';
        }

        // Mettre à jour tous les avatars sur la page
        const allAvatars = document.querySelectorAll('img[data-avatar]');
        allAvatars.forEach(avatar => {
            avatar.src = avatarUrl;
        });
    }

    resetAvatarInput() {
        const avatarInput = document.getElementById('avatar');
        if (avatarInput) {
            avatarInput.value = '';
        }
    }

    showLoading(show) {
        const loadingElement = document.querySelector('.avatar-loading');
        if (loadingElement) {
            loadingElement.style.display = show ? 'block' : 'none';
        }

        const avatarBtns = document.querySelectorAll('.avatar-btn');
        avatarBtns.forEach(btn => {
            btn.disabled = show;
        });
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        // Créer une notification Bootstrap
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-suppression après 5 secondes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    openAvatarModal() {
        const avatarPreview = document.getElementById('avatar-preview');
        if (!avatarPreview || !avatarPreview.src) return;

        // Créer un modal simple pour afficher l'avatar en grand
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Photo de profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${avatarPreview.src}" class="img-fluid rounded" style="max-width: 100%;">
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', function() {
    new AvatarManager();
}); 