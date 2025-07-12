<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
                @if($unreadCount > 0)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $unreadCount }}
                    </span>
                @endif
            </h2>
            <div class="flex space-x-2">
                @if($unreadCount > 0)
                    <button id="markAllReadBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-check-double mr-2"></i>
                        Tout marquer comme lu
                    </button>
                @endif
                <button id="deleteReadBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-trash mr-2"></i>
                    Supprimer les lues
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bell text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-600">Total</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $notifications->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope text-red-600 text-2xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-600">Non lues</p>
                                    <p class="text-2xl font-bold text-red-900">{{ $unreadCount }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-600">Lues</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $notifications->count() - $unreadCount }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-600">Aujourd'hui</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $notifications->where('created_at', '>=', now()->startOfDay())->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-wrap gap-4">
                        <button class="filter-btn active" data-filter="all">
                            Toutes ({{ $notifications->count() }})
                        </button>
                        <button class="filter-btn" data-filter="unread">
                            Non lues ({{ $unreadCount }})
                        </button>
                        <button class="filter-btn" data-filter="read">
                            Lues ({{ $notifications->count() - $unreadCount }})
                        </button>
                        @foreach($unreadCountByType as $type => $count)
                            <button class="filter-btn" data-filter="type-{{ $type }}">
                                {{ ucfirst(str_replace('_', ' ', $type)) }} ({{ $count }})
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Liste des notifications -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($notifications->count() > 0)
                        <div id="notificationsList" class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="notification-item border rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-white border-l-4 border-l-blue-500' }}" 
                                     data-id="{{ $notification->id }}" 
                                     data-type="{{ $notification->type }}"
                                     data-read="{{ $notification->read_at ? 'true' : 'false' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3 flex-1">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-{{ $notification->data['color'] ?? 'blue' }}-100 flex items-center justify-center">
                                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-{{ $notification->data['color'] ?? 'blue' }}-600"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="text-sm font-medium text-gray-900">
                                                        {{ $notification->data['title'] ?? 'Notification' }}
                                                    </h4>
                                                    @if(!$notification->read_at)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            Nouveau
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $notification->data['message'] ?? 'Aucun message' }}
                                                </p>
                                                <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                    <span>{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if(!$notification->read_at)
                                                <button class="mark-read-btn text-blue-600 hover:text-blue-800" data-id="{{ $notification->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="delete-notification-btn text-red-600 hover:text-red-800" data-id="{{ $notification->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if(isset($notification->data['conversation_url']) || isset($notification->data['session_url']))
                                        <div class="mt-3">
                                            <a href="{{ $notification->data['conversation_url'] ?? $notification->data['session_url'] }}" 
                                               class="text-sm text-blue-600 hover:text-blue-800">
                                                Voir les détails <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune notification</h3>
                            <p class="text-gray-600">Vous n'avez pas encore reçu de notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Marquer une notification comme lue
            document.querySelectorAll('.mark-read-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    markAsRead(notificationId);
                });
            });

            // Supprimer une notification
            document.querySelectorAll('.delete-notification-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    deleteNotification(notificationId);
                });
            });

            // Marquer toutes comme lues
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', markAllAsRead);
            }

            // Supprimer les notifications lues
            const deleteReadBtn = document.getElementById('deleteReadBtn');
            if (deleteReadBtn) {
                deleteReadBtn.addEventListener('click', deleteReadNotifications);
            }

            // Filtres
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    applyFilter(filter);
                    
                    // Mettre à jour les boutons actifs
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            function markAsRead(notificationId) {
                fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                        if (notificationItem) {
                            notificationItem.classList.remove('border-l-blue-500', 'bg-white');
                            notificationItem.classList.add('bg-gray-50');
                            notificationItem.dataset.read = 'true';
                            
                            // Supprimer le bouton "marquer comme lu"
                            const markReadBtn = notificationItem.querySelector('.mark-read-btn');
                            if (markReadBtn) markReadBtn.remove();
                            
                            // Supprimer le badge "Nouveau"
                            const newBadge = notificationItem.querySelector('.bg-blue-100');
                            if (newBadge) newBadge.remove();
                        }
                        
                        updateNotificationCount(data.unread_count);
                        showToast(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors du marquage de la notification', 'error');
                });
            }

            function deleteNotification(notificationId) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                    return;
                }

                fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                        if (notificationItem) {
                            notificationItem.remove();
                        }
                        
                        updateNotificationCount(data.unread_count);
                        showToast(data.message, 'success');
                        
                        // Vérifier s'il reste des notifications
                        if (document.querySelectorAll('.notification-item').length === 0) {
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors de la suppression', 'error');
                });
            }

            function markAllAsRead() {
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.remove('border-l-blue-500', 'bg-white');
                            item.classList.add('bg-gray-50');
                            item.dataset.read = 'true';
                            
                            const markReadBtn = item.querySelector('.mark-read-btn');
                            if (markReadBtn) markReadBtn.remove();
                            
                            const newBadge = item.querySelector('.bg-blue-100');
                            if (newBadge) newBadge.remove();
                        });
                        
                        updateNotificationCount(0);
                        showToast(data.message, 'success');
                        
                        // Masquer le bouton "marquer toutes comme lues"
                        if (markAllReadBtn) {
                            markAllReadBtn.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors du marquage', 'error');
                });
            }

            function deleteReadNotifications() {
                if (!confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications lues ?')) {
                    return;
                }

                fetch('/notifications/delete-read', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item[data-read="true"]').forEach(item => {
                            item.remove();
                        });
                        
                        showToast(data.message, 'success');
                        
                        // Vérifier s'il reste des notifications
                        if (document.querySelectorAll('.notification-item').length === 0) {
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors de la suppression', 'error');
                });
            }

            function applyFilter(filter) {
                const notifications = document.querySelectorAll('.notification-item');
                
                notifications.forEach(item => {
                    let show = false;
                    
                    switch(filter) {
                        case 'all':
                            show = true;
                            break;
                        case 'unread':
                            show = item.dataset.read === 'false';
                            break;
                        case 'read':
                            show = item.dataset.read === 'true';
                            break;
                        default:
                            if (filter.startsWith('type-')) {
                                const type = filter.replace('type-', '');
                                show = item.dataset.type === type;
                            }
                            break;
                    }
                    
                    item.style.display = show ? 'block' : 'none';
                });
            }

            function updateNotificationCount(count) {
                // Mettre à jour le compteur dans l'en-tête
                const headerBadge = document.querySelector('.bg-red-100');
                if (headerBadge) {
                    if (count > 0) {
                        headerBadge.textContent = count;
                    } else {
                        headerBadge.remove();
                    }
                }
                
                // Mettre à jour les statistiques
                const unreadStats = document.querySelector('.bg-red-50 .text-red-900');
                if (unreadStats) {
                    unreadStats.textContent = count;
                }
            }

            function showToast(message, type = 'info') {
                // Créer un toast simple
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                    type === 'success' ? 'bg-green-500 text-white' : 
                    type === 'error' ? 'bg-red-500 text-white' : 
                    'bg-blue-500 text-white'
                }`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .filter-btn {
            @apply px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200;
        }
        
        .filter-btn.active {
            @apply bg-blue-100 text-blue-700;
        }
        
        .filter-btn:not(.active) {
            @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
        }
        
        .notification-item {
            transition: all 0.2s ease-in-out;
        }
        
        .notification-item:hover {
            @apply shadow-md;
        }
    </style>
    @endpush
</x-app-layout> 