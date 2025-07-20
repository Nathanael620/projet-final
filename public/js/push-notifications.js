class PushNotificationManager {
    constructor() {
        this.notifications = [];
        this.isInitialized = false;
        this.checkInterval = null;
        this.lastCheckTime = new Date();
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.createNotificationContainer();
        this.startPolling();
        this.isInitialized = true;
        
        console.log('PushNotificationManager initialisé');
    }

    createNotificationContainer() {
        // Créer le conteneur de notifications s'il n'existe pas
        if (!document.getElementById('push-notifications-container')) {
            const container = document.createElement('div');
            container.id = 'push-notifications-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                pointer-events: none;
            `;
            document.body.appendChild(container);
        }
    }

    startPolling() {
        // Vérifier les nouvelles notifications toutes les 30 secondes
        this.checkInterval = setInterval(() => {
            this.checkNewNotifications();
        }, 30000);

        // Vérifier immédiatement au chargement
        this.checkNewNotifications();
    }

    async checkNewNotifications() {
        try {
            const response = await fetch('/api/notifications/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    last_check: this.lastCheckTime.toISOString()
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        this.showNotification(notification);
                    });
                }
                this.lastCheckTime = new Date();
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des notifications:', error);
        }
    }

    showNotification(notificationData) {
        const container = document.getElementById('push-notifications-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = 'push-notification';
        notification.style.cssText = `
            background: white;
            border-left: 4px solid var(--bs-${notificationData.data.color || 'primary'});
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            padding: 16px;
            max-width: 400px;
            pointer-events: auto;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
        `;

        const icon = this.getIconForType(notificationData.type);
        const color = notificationData.data.color || 'primary';

        notification.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-3">
                    <i class="${icon} text-${color}" style="font-size: 1.2rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="font-size: 0.9rem; font-weight: 600;">
                        ${notificationData.data.title || 'Nouvelle notification'}
                    </h6>
                    <p class="mb-2" style="font-size: 0.8rem; color: #6c757d; line-height: 1.4;">
                        ${notificationData.data.message || ''}
                    </p>
                    ${notificationData.data.action_text ? `
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-${color}" onclick="pushNotificationManager.handleNotificationAction('${notificationData.data.action_url || ''}')">
                                ${notificationData.data.action_text}
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pushNotificationManager.dismissNotification(this)">
                                Ignorer
                            </button>
                        </div>
                    ` : `
                        <button class="btn btn-sm btn-outline-secondary" onclick="pushNotificationManager.dismissNotification(this)">
                            Fermer
                        </button>
                    `}
                </div>
                <div class="flex-shrink-0 ms-2">
                    <button type="button" class="btn-close btn-close-sm" 
                            onclick="pushNotificationManager.dismissNotification(this)" 
                            style="font-size: 0.7rem;"></button>
                </div>
            </div>
            <div class="progress" style="height: 2px; margin-top: 8px;">
                <div class="progress-bar bg-${color}" style="width: 100%; transition: width 5s linear;"></div>
            </div>
        `;

        container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Auto-suppression après 5 secondes
        setTimeout(() => {
            this.dismissNotification(notification);
        }, 5000);

        // Animation de la barre de progression
        setTimeout(() => {
            const progressBar = notification.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
            }
        }, 100);

        // Marquer comme lue
        this.markAsRead(notificationData.id);
    }

    getIconForType(type) {
        const icons = {
            'session_created_student': 'fas fa-calendar-plus',
            'session_created_tutor': 'fas fa-calendar-check',
            'session_accepted': 'fas fa-check-circle',
            'session_rejected': 'fas fa-times-circle',
            'session_cancelled': 'fas fa-calendar-times',
            'session_reminder_student': 'fas fa-bell',
            'session_reminder_tutor': 'fas fa-bell',
            'session_updated': 'fas fa-edit',
            'session_completed_student': 'fas fa-check-double',
            'session_completed_tutor': 'fas fa-check-double',
            'new_message': 'fas fa-comment',
            'message_edited': 'fas fa-edit',
            'payment': 'fas fa-credit-card',
            'default': 'fas fa-info-circle'
        };

        return icons[type] || icons.default;
    }

    dismissNotification(element) {
        const notification = element.closest('.push-notification');
        if (notification) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    }

    handleNotificationAction(url) {
        if (url) {
            window.location.href = url;
        }
        this.dismissNotification(event.target);
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`/api/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                }
            });
        } catch (error) {
            console.error('Erreur lors du marquage comme lu:', error);
        }
    }

    // Méthode pour forcer la vérification des notifications
    forceCheck() {
        this.checkNewNotifications();
    }

    // Méthode pour arrêter le polling
    stop() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }

    // Méthode pour redémarrer le polling
    restart() {
        this.stop();
        this.startPolling();
    }
}

// Initialiser le gestionnaire de notifications push
let pushNotificationManager;

document.addEventListener('DOMContentLoaded', function() {
    pushNotificationManager = new PushNotificationManager();
});

// Exposer globalement pour les tests
window.pushNotificationManager = pushNotificationManager; 