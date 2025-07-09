class AvatarManager {
    constructor() {
        this.initializeEventListeners();
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

        // Prévisualisation
        this.showPreview(file);

        // Upload automatique
        this.uploadAvatar(file);
    }

    validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        return allowedTypes.includes(file.type) && file.size <= maxSize;
    }

    showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('avatar-preview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }

    async uploadAvatar(file) {
        const formData = new FormData();
        formData.append('avatar', file);

        try {
            this.showLoading(true);
            
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
                // Mettre à jour l'avatar dans la navigation si présent
                this.updateNavigationAvatar(data.avatar_url);
            } else {
                this.showError('Erreur lors du téléchargement de l\'avatar.');
            }
        } catch (error) {
            this.showError('Erreur de connexion. Veuillez réessayer.');
        } finally {
            this.showLoading(false);
        }
    }

    async handleAvatarRemove(event) {
        event.preventDefault();

        if (!confirm('Êtes-vous sûr de vouloir supprimer votre avatar ?')) {
            return;
        }

        try {
            this.showLoading(true);
            
            const response = await fetch('/avatar/remove', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.resetAvatar();
            } else {
                this.showError('Erreur lors de la suppression de l\'avatar.');
            }
        } catch (error) {
            this.showError('Erreur de connexion. Veuillez réessayer.');
        } finally {
            this.showLoading(false);
        }
    }

    resetAvatar() {
        const preview = document.getElementById('avatar-preview');
        if (preview) {
            preview.src = '/images/default-avatar.png';
        }

        const input = document.getElementById('avatar');
        if (input) {
            input.value = '';
        }
    }

    updateNavigationAvatar(avatarUrl) {
        const navAvatar = document.querySelector('.nav-avatar');
        if (navAvatar) {
            navAvatar.src = avatarUrl;
        }
    }

    openAvatarModal() {
        const modal = new bootstrap.Modal(document.getElementById('avatarModal'));
        modal.show();
    }

    showLoading(show) {
        const loadingSpinner = document.getElementById('avatar-loading');
        if (loadingSpinner) {
            loadingSpinner.style.display = show ? 'block' : 'none';
        }
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'danger');
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Initialiser le gestionnaire d'avatar quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new AvatarManager();
}); 