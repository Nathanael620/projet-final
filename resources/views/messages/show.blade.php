@extends('layouts.app')

@section('content')
@if(!$otherUser)
    <div class="container py-4">
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Erreur</h5>
            <p>Utilisateur non trouvé ou accès non autorisé.</p>
            <a href="{{ route('messages.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour aux messages
            </a>
        </div>
    </div>
@else
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <!-- En-tête de la conversation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            @if($otherUser->avatar)
                                <img src="{{ Storage::url($otherUser->avatar) }}" 
                                     alt="{{ $otherUser->name }}"
                                     class="rounded-circle"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                            <i class="fas fa-user-circle fa-3x text-muted"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">
                                {{ $otherUser->name }}
                                @if($otherUser->isTutor())
                                    <span class="badge bg-success ms-2">Tuteur</span>
                                @else
                                    <span class="badge bg-primary ms-2">Étudiant</span>
                                @endif
                                <!-- Indicateur en ligne -->
                                <span class="badge bg-success ms-2" style="width: 8px; height: 8px; border-radius: 50%;"></span>
                            </h5>
                            <p class="text-muted mb-1">{{ $otherUser->email }}</p>
                            @if($otherUser->isTutor())
                                <div class="d-flex align-items-center">
                                    {!! $otherUser->getRatingStars() !!}
                                    <small class="text-muted ms-2">({{ $otherUser->rating ?? 0 }}) - {{ $otherUser->total_sessions ?? 0 }} séances</small>
                                </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-secondary btn-sm" onclick="refreshMessages()" title="Actualiser">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Conversation
                    </h6>
                        <small class="text-muted" id="messageCount">{{ $messages->count() }} message(s)</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="messages-container" style="height: 500px; overflow-y: auto; padding: 1.5rem;">
                    @if($messages->count() > 0)
                        @foreach($messages as $message)
                                @include('messages.partials.message', ['message' => $message])
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Aucun message pour le moment</p>
                            <p class="text-muted small">Commencez la conversation !</p>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            <!-- Formulaire d'envoi -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="message-form" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                        <div class="input-group">
                            <textarea name="content" id="message-content" class="form-control" 
                                      rows="3" placeholder="Tapez votre message..." required></textarea>
                                <button type="submit" class="btn btn-primary" id="sendButton">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            </div>
                        </div>
                        
                        <!-- Zone de fichiers -->
                        <div class="d-flex align-items-center gap-2">
                            <label for="file-input" class="btn btn-outline-secondary btn-sm mb-0" title="Joindre un fichier">
                                <i class="fas fa-paperclip"></i>
                            </label>
                            <input type="file" id="file-input" name="file" style="display: none;" 
                                   accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                            
                            <div id="file-preview" class="flex-grow-1" style="display: none;">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <i class="fas fa-file me-2"></i>
                                    <span id="file-name" class="flex-grow-1"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <small class="text-muted">Appuyez sur Ctrl+Entrée pour envoyer</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Profil de l'utilisateur -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Profil
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($otherUser->avatar)
                            <img src="{{ Storage::url($otherUser->avatar) }}" 
                                 alt="{{ $otherUser->name }}"
                                 class="rounded-circle mb-2"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <i class="fas fa-user-circle fa-4x text-muted mb-2"></i>
                        @endif
                    </div>
                    
                    <h6 class="text-center mb-2">{{ $otherUser->name }}</h6>
                    <p class="text-muted text-center small mb-3">{{ $otherUser->email }}</p>
                    
                    @if($otherUser->phone)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-phone me-2"></i>{{ $otherUser->canViewPhone(auth()->user()) ? $otherUser->phone : $otherUser->getMaskedPhone() }}
                        </small>
                    </div>
                    @endif
                    
                    @if($otherUser->bio)
                    <div class="mb-3">
                        <small class="text-muted">{{ Str::limit($otherUser->bio, 100) }}</small>
                    </div>
                    @endif
                    
                    @if($otherUser->skills)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Compétences :</small>
                        @foreach(array_slice($otherUser->skills, 0, 3) as $skill)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @if($otherUser->isTutor())
                    <div class="text-center">
                        <div class="mb-2">
                            <strong class="text-success">{{ number_format($otherUser->hourly_rate ?? 20, 2) }}€</strong>
                            <small class="text-muted">/heure</small>
                        </div>
                        <a href="{{ route('tutors.show', $otherUser) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>
                            Voir le profil complet
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($otherUser->isTutor() && auth()->user()->isStudent())
                            <a href="{{ route('sessions.create', ['tutor_id' => $otherUser->id]) }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Demander une séance
                            </a>
                        @endif
                        
                        @if(auth()->user()->isTutor() && $otherUser->isStudent())
                            <a href="{{ route('sessions.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Proposer une séance
                            </a>
                        @endif
                        
                        <button class="btn btn-outline-info" onclick="shareContact()">
                            <i class="fas fa-share me-2"></i>
                            Partager mes coordonnées
                        </button>
                        
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour aux messages
                        </a>
                    </div>
                </div>
            </div>

            <!-- Séances en commun -->
            @if($commonSessions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-calendar-check text-success me-2"></i>
                        Séances en commun
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($commonSessions->take(3) as $session)
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0 me-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="d-block">{{ Str::limit($session->title, 25) }}</small>
                            <small class="text-muted">{{ $session->scheduled_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge {{ $session->getStatusBadgeClass() }}">{{ $session->getStatusText() }}</span>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($commonSessions->count() > 3)
                    <div class="text-center mt-2">
                        <small class="text-muted">+{{ $commonSessions->count() - 3 }} autres séances</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour les images -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let lastMessageId = {{ $messages->count() > 0 ? $messages->last()->id : 0 }};
let isTyping = false;
let typingTimeout;

// Variables globales pour éviter les doublons
let isInitialized = false;
let messageForm = null;
let messageContent = null;
let fileInput = null;
let messagesContainer = null;
let isSending = false; // Protection contre l'envoi multiple

document.addEventListener('DOMContentLoaded', function() {
    if (isInitialized) return; // Éviter l'initialisation multiple
    isInitialized = true;
    
    messagesContainer = document.getElementById('messages-container');
    messageForm = document.getElementById('message-form');
    messageContent = document.getElementById('message-content');
    fileInput = document.getElementById('file-input');
    
    if (!messageForm || !messageContent || !fileInput || !messagesContainer) {
        console.error('Éléments de formulaire non trouvés');
        return;
    }
    
    // Scroll vers le bas des messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Scroll initial
    scrollToBottom();
    
    // Gestion de l'envoi du formulaire (avec vérification de doublon)
    if (!messageForm.hasAttribute('data-event-attached')) {
        messageForm.setAttribute('data-event-attached', 'true');
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    
    // Gestion des fichiers
    if (!fileInput.hasAttribute('data-event-attached')) {
        fileInput.setAttribute('data-event-attached', 'true');
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showFilePreview(file);
            }
        });
    }
    
    // Raccourci clavier Ctrl+Entrée
    if (!messageContent.hasAttribute('data-keydown-attached')) {
        messageContent.setAttribute('data-keydown-attached', 'true');
        messageContent.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    // Auto-resize du textarea
    if (!messageContent.hasAttribute('data-input-attached')) {
        messageContent.setAttribute('data-input-attached', 'true');
        messageContent.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
    
    // Actualisation automatique des messages (avec intervalle plus long)
    let checkInterval = setInterval(checkNewMessages, 10000); // 10 secondes au lieu de 5
    
    // Fonction pour arrêter l'actualisation automatique
    window.stopAutoRefresh = function() {
        if (checkInterval) {
            clearInterval(checkInterval);
            checkInterval = null;
        }
    };
    
    // Fonction pour redémarrer l'actualisation automatique
    window.startAutoRefresh = function() {
        if (!checkInterval) {
            checkInterval = setInterval(checkNewMessages, 10000);
        }
    };
});

// Envoyer un message
function sendMessage() {
    // Protection contre l'envoi multiple
    if (isSending) {
        console.log('Message en cours d\'envoi, ignoré');
        return;
    }
    
    const messageContent = document.getElementById('message-content');
    const fileInput = document.getElementById('file-input');
    const sendButton = document.getElementById('sendButton');
    const content = messageContent.value.trim();
    const file = fileInput.files[0];
    
    if (!content && !file) {
        alert('Veuillez saisir un message ou sélectionner un fichier');
        return;
    }
    
    isSending = true;
    
    // Arrêter l'actualisation automatique pendant l'envoi
    if (window.stopAutoRefresh) {
        window.stopAutoRefresh();
    }
    
    // Désactiver le bouton pendant l'envoi
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    const formData = new FormData();
    formData.append('content', content);
    formData.append('_token', '{{ csrf_token() }}');
    if (file) {
        formData.append('file', file);
    }
    
    fetch('{{ route("messages.store", $otherUser->id ?? 0) }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Ajouter le message à l'interface
            const messagesContainer = document.getElementById('messages-container');
            messagesContainer.insertAdjacentHTML('beforeend', data.html);
            
            // Mettre à jour le compteur
            updateMessageCount();
            
            // Scroll vers le bas
            scrollToBottom();
            
            // Réinitialiser le formulaire
            messageContent.value = '';
            messageContent.style.height = 'auto';
            removeFile();
            
            // Mettre à jour lastMessageId
            lastMessageId = data.message.id;
        } else {
            // Afficher l'erreur du serveur
            alert(data.message || 'Erreur lors de l\'envoi du message');
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi:', error);
        alert('Erreur lors de l\'envoi du message: ' + error.message);
    })
    .finally(() => {
        // Réactiver le bouton et la protection
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        isSending = false;
        
        // Redémarrer l'actualisation automatique
        if (window.startAutoRefresh) {
            window.startAutoRefresh();
        }
    });
}

// Vérifier les nouveaux messages
function checkNewMessages() {
    // Ne pas vérifier si on est en train d'envoyer un message
    if (isSending) {
        return;
    }
    
    fetch(`{{ route('messages.new', $otherUser->id ?? 0) }}?last_message_id=${lastMessageId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.count > 0) {
            // Vérifier qu'on n'a pas déjà ces messages
            const existingMessages = document.querySelectorAll('.message-item');
            const newMessageIds = data.messages.map(m => m.id);
            
            // Filtrer les messages qui n'existent pas déjà
            const trulyNewMessages = data.messages.filter(message => {
                return !document.querySelector(`[data-message-id="${message.id}"]`);
            });
            
            if (trulyNewMessages.length > 0) {
                // Ajouter seulement les nouveaux messages
                const messagesContainer = document.getElementById('messages-container');
                messagesContainer.insertAdjacentHTML('beforeend', data.html);
                
                // Mettre à jour le compteur
                updateMessageCount();
                
                // Scroll vers le bas si on est déjà en bas
                const isAtBottom = messagesContainer.scrollTop + messagesContainer.clientHeight >= messagesContainer.scrollHeight - 10;
                if (isAtBottom) {
                    scrollToBottom();
                }
                
                // Mettre à jour lastMessageId
                if (data.messages.length > 0) {
                    lastMessageId = data.messages[data.messages.length - 1].id;
                }
                
                // Notification sonore (optionnel)
                playNotificationSound();
            }
        }
    })
    .catch(error => {
        console.error('Erreur lors de la vérification des nouveaux messages:', error);
    });
}

// Actualiser les messages
function refreshMessages() {
    location.reload();
}

// Afficher l'aperçu du fichier
function showFilePreview(file) {
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    
    fileName.textContent = file.name;
    filePreview.style.display = 'block';
}

// Supprimer le fichier
function removeFile() {
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    
    fileInput.value = '';
    filePreview.style.display = 'none';
}

// Mettre à jour le compteur de messages
function updateMessageCount() {
    const messageCount = document.getElementById('messageCount');
    const messages = document.querySelectorAll('.message-item');
    messageCount.textContent = `${messages.length} message(s)`;
}

// Scroll vers le bas
function scrollToBottom() {
    const messagesContainer = document.getElementById('messages-container');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Ouvrir le modal d'image
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Supprimer un message
function deleteMessage(messageId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
        fetch(`/messages/${messageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.remove();
                    updateMessageCount();
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
        });
    }
}

// Modifier un message
function editMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const contentElement = messageElement.querySelector('.message-content');
    const currentContent = contentElement.textContent.trim();
    
    // Créer un formulaire d'édition
    const editForm = document.createElement('div');
    editForm.className = 'edit-form mt-2';
    editForm.innerHTML = `
        <div class="input-group">
            <textarea class="form-control form-control-sm" rows="2" maxlength="1000">${currentContent}</textarea>
            <button class="btn btn-sm btn-success" onclick="saveEdit(${messageId}, this)">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn btn-sm btn-secondary" onclick="cancelEdit(${messageId})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Masquer le contenu original et afficher le formulaire
    contentElement.style.display = 'none';
    messageElement.appendChild(editForm);
    
    // Focus sur le textarea
    const textarea = editForm.querySelector('textarea');
    textarea.focus();
    textarea.select();
}

// Sauvegarder la modification
function saveEdit(messageId, button) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const editForm = messageElement.querySelector('.edit-form');
    const textarea = editForm.querySelector('textarea');
    const newContent = textarea.value.trim();
    
    if (!newContent) {
        alert('Le message ne peut pas être vide');
        return;
    }
    
    // Désactiver le bouton pendant l'envoi
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch(`/messages/${messageId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            content: newContent
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remplacer le contenu du message
            const contentElement = messageElement.querySelector('.message-content');
            contentElement.innerHTML = data.html;
            contentElement.style.display = 'block';
            
            // Supprimer le formulaire d'édition
            editForm.remove();
            
            // Mettre à jour le compteur
            updateMessageCount();
        } else {
            alert(data.message || 'Erreur lors de la modification');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la modification:', error);
        alert('Erreur lors de la modification du message');
    })
    .finally(() => {
        // Réactiver le bouton
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check"></i>';
    });
}

// Annuler la modification
function cancelEdit(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const contentElement = messageElement.querySelector('.message-content');
    const editForm = messageElement.querySelector('.edit-form');
    
    // Afficher le contenu original
    contentElement.style.display = 'block';
    
    // Supprimer le formulaire d'édition
    editForm.remove();
}

// Partager les coordonnées
function shareContact() {
    const contactInfo = `Nom: {{ auth()->user()->name }}\nEmail: {{ auth()->user()->email }}`;
    if (navigator.share) {
        navigator.share({
            title: 'Mes coordonnées',
            text: contactInfo
        });
    } else {
        // Fallback pour les navigateurs qui ne supportent pas l'API Share
        navigator.clipboard.writeText(contactInfo).then(() => {
            alert('Coordonnées copiées dans le presse-papiers !');
        });
    }
}

// Jouer un son de notification
function playNotificationSound() {
    // Créer un élément audio simple
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
    audio.volume = 0.3;
    audio.play().catch(() => {
        // Ignorer les erreurs si l'audio ne peut pas être joué
    });
}
</script>
@endpush

<style>
.message-item .bg-primary {
    background-color: #007bff !important;
}

.message-item .bg-light {
    background-color: #f8f9fa !important;
}

#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#messages-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.message-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.message-item:hover .message-actions {
    opacity: 1;
}

#message-content:focus {
    box-shadow: none;
    border-color: #007bff;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-primary {
    background-color: #007bff !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

#file-preview {
    transition: all 0.3s ease;
}

.conversation-header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white;
}
</style>
@endif
@endsection 