@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- En-tête avec recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="h3 mb-1">Messages</h1>
                            <p class="text-muted mb-0">Vos conversations avec les autres utilisateurs</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-envelope me-1"></i>
                                {{ $stats['unread_messages'] }} non lus
                            </span>
                            @if($stats['new_contacts_available'] > 0)
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-user-plus me-1"></i>
                                    {{ $stats['new_contacts_available'] }} nouveaux contacts
                                </span>
                            @endif
                            <button class="btn btn-outline-secondary btn-sm" onclick="refreshStats()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Onglets de recherche -->
                    <ul class="nav nav-tabs mb-3" id="searchTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-search" type="button" role="tab">
                                <i class="fas fa-comments me-2"></i>Messages
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-search" type="button" role="tab">
                                <i class="fas fa-user-plus me-2"></i>Nouveaux contacts
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="searchTabContent">
                        <!-- Recherche dans les messages -->
                        <div class="tab-pane fade show active" id="messages-search" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" id="searchInput" class="form-control border-start-0" 
                                               placeholder="Rechercher dans vos messages..." 
                                               autocomplete="off">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select id="filterSelect" class="form-select" onchange="filterConversations()">
                                        <option value="">Toutes les conversations</option>
                                        <option value="unread">Non lues</option>
                                        <option value="tutors">Tuteurs uniquement</option>
                                        <option value="students">Étudiants uniquement</option>
                                        <option value="recent">Récentes (7 jours)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recherche d'utilisateurs -->
                        <div class="tab-pane fade" id="users-search" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user-search text-muted"></i>
                                        </span>
                                        <input type="text" id="userSearchInput" class="form-control border-start-0" 
                                               placeholder="Rechercher un utilisateur..." 
                                               autocomplete="off">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearUserSearch()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="roleFilter" class="form-select" onchange="searchUsers()">
                                        <option value="">Tous les rôles</option>
                                        <option value="tutor">Tuteurs uniquement</option>
                                        <option value="student">Étudiants uniquement</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success w-100" onclick="searchUsers()">
                                        <i class="fas fa-search me-2"></i>Rechercher
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats de recherche de messages -->
    <div id="searchResults" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-search text-primary me-2"></i>
                        Résultats de recherche dans les messages
                        <span id="searchCount" class="badge bg-primary ms-2">0</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div id="searchResultsList"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats de recherche d'utilisateurs -->
    <div id="userSearchResults" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-user-search text-success me-2"></i>
                        Utilisateurs trouvés
                        <span id="userSearchCount" class="badge bg-success ms-2">0</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div id="userSearchResultsList"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des conversations -->
    <div id="conversationsList">
        @if($conversations->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-comments text-primary me-2"></i>
                                    Conversations
                                </h6>
                                <small class="text-muted">{{ $conversations->count() }} conversation(s)</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @foreach($conversations as $conversation)
                                <div class="conversation-item p-3 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }}" 
                                     data-user-id="{{ $conversation['user']->id }}"
                                     data-role="{{ $conversation['user']->role }}"
                                     data-unread="{{ $conversation['unread_count'] }}"
                                     data-last-message-time="{{ $conversation['last_message_time']->timestamp }}">
                                    <div class="d-flex align-items-center">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 me-3">
                                            <div class="position-relative">
                                                @if($conversation['user']->avatar)
                                                    <img src="{{ Storage::url($conversation['user']->avatar) }}" 
                                                         alt="{{ $conversation['user']->name }}"
                                                         class="rounded-circle"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                @endif
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $conversation['unread_count'] }}
                                                    </span>
                                                @endif
                                                <!-- Indicateur en ligne -->
                                                <span class="position-absolute bottom-0 end-0 translate-middle badge rounded-pill bg-success" 
                                                      style="width: 12px; height: 12px; border: 2px solid white;"></span>
                                            </div>
                                        </div>
                                        
                                        <!-- Contenu -->
                                        <div class="flex-grow-1 me-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="mb-0">
                                                    {{ $conversation['user']->name }}
                                                    @if($conversation['user']->isTutor())
                                                        <span class="badge bg-success ms-1">Tuteur</span>
                                                    @else
                                                        <span class="badge bg-primary ms-1">Étudiant</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $conversation['last_message_time']->diffForHumans() }}
                                                </small>
                                            </div>
                                            
                                            <p class="text-muted mb-1 small">
                                                @if($conversation['last_message']->sender_id === auth()->id())
                                                    <i class="fas fa-reply text-primary me-1"></i>
                                                @else
                                                    <i class="fas fa-arrow-right text-success me-1"></i>
                                                @endif
                                                {{ Str::limit($conversation['last_message']->content, 50) }}
                                            </p>
                                            
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted me-3">
                                                    <i class="fas fa-{{ $conversation['user']->isTutor() ? 'user-tie' : 'user-graduate' }} me-1"></i>
                                                    {{ $conversation['user']->getSkillsString() }}
                                                </small>
                                                @if($conversation['user']->isTutor())
                                                    <small class="text-muted">
                                                        {!! $conversation['user']->getRatingStars() !!}
                                                        <span class="ms-1">({{ $conversation['user']->rating ?? 0 }})</span>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex-shrink-0">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('messages.show', $conversation['user']) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-comments me-1"></i>
                                                    Ouvrir
                                                </a>
                                                @if($conversation['unread_count'] > 0)
                                                    <button class="btn btn-outline-success btn-sm" 
                                                            onclick="markAsRead({{ $conversation['user']->id }})"
                                                            title="Marquer comme lu">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Nouveaux contacts disponibles -->
    @if($newContacts->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-user-plus text-success me-2"></i>
                                Nouveaux contacts
                            </h6>
                            <small class="text-muted">{{ $newContacts->count() }} contact(s) disponible(s)</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            @foreach($newContacts->take(6) as $contact)
                                <div class="col-md-6 col-lg-4">
                                    <div class="contact-item p-3 border-bottom border-end">
                                        <div class="d-flex align-items-center">
                                            <!-- Avatar -->
                                            <div class="flex-shrink-0 me-3">
                                                @if($contact->avatar)
                                                    <img src="{{ Storage::url($contact->avatar) }}" 
                                                         alt="{{ $contact->name }}"
                                                         class="rounded-circle"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                @endif
                                            </div>
                                            
                                            <!-- Infos -->
                                            <div class="flex-grow-1 me-2">
                                                <h6 class="mb-1 small">
                                                    {{ $contact->name }}
                                                    @if($contact->isTutor())
                                                        <span class="badge bg-success ms-1">Tuteur</span>
                                                    @else
                                                        <span class="badge bg-primary ms-1">Étudiant</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted d-block">
                                                    {{ Str::limit($contact->getSkillsString(), 30) }}
                                                </small>
                                                @if($contact->isTutor())
                                                    <small class="text-muted">
                                                        {!! $contact->getRatingStars() !!}
                                                        <span class="ms-1">({{ $contact->rating ?? 0 }})</span>
                                                    </small>
                                                @endif
                                            </div>
                                            
                                            <!-- Action -->
                                            <div class="flex-shrink-0">
                                                <a href="{{ route('messages.show', $contact) }}" 
                                                   class="btn btn-outline-success btn-sm"
                                                   title="Commencer une conversation">
                                                    <i class="fas fa-comment"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($newContacts->count() > 6)
                            <div class="p-3 text-center">
                                <small class="text-muted">
                                    Et {{ $newContacts->count() - 6 }} autres contacts disponibles...
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($conversations->count() === 0 && $newContacts->count() === 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune conversation</h5>
                        <p class="text-muted">
                            Vous n'avez pas encore de conversations. 
                            Commencez par demander une séance de soutien !
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('sessions.create') }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-2"></i>
                                Demander une séance
                            </a>
                            <a href="{{ route('tutors.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>
                                Trouver un tuteur
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Statistiques -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-comments fa-2x text-primary mb-2"></i>
                    <h5 class="card-title" id="totalConversations">{{ $stats['total_conversations'] }}</h5>
                    <p class="card-text text-muted small">Conversations</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-envelope fa-2x text-warning mb-2"></i>
                    <h5 class="card-title" id="unreadMessages">{{ $stats['unread_messages'] }}</h5>
                    <p class="card-text text-muted small">Messages non lus</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-user-tie fa-2x text-success mb-2"></i>
                    <h5 class="card-title" id="tutorsContacted">{{ $stats['tutors_contacted'] }}</h5>
                    <p class="card-text text-muted small">Tuteurs contactés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-user-graduate fa-2x text-info mb-2"></i>
                    <h5 class="card-title" id="studentsContacted">{{ $stats['students_contacted'] }}</h5>
                    <p class="card-text text-muted small">Étudiants contactés</p>
                </div>
            </div>
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

@push('styles')
<style>
.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #495057;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
    color: #007bff;
    background: transparent;
}

.nav-tabs .nav-link.active i {
    color: #007bff;
}

#userSearchResults .btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

#userSearchResults .btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.contact-item:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}
</style>
@endpush

@push('scripts')
<script>
let searchTimeout;
let lastMessageId = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Recherche en temps réel dans les messages
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => searchMessages(query), 500);
        } else {
            hideSearchResults();
        }
    });
    
    // Recherche d'utilisateurs
    const userSearchInput = document.getElementById('userSearchInput');
    userSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => searchUsers(), 500);
        } else {
            hideUserSearchResults();
        }
    });
    
    // Actualisation automatique des statistiques
    setInterval(refreshStats, 30000); // Toutes les 30 secondes
});

// Recherche de messages
function searchMessages(query) {
    fetch('{{ route("messages.search") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ query: query })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySearchResults(data.results, query);
        }
    })
    .catch(error => {
        console.error('Erreur de recherche:', error);
    });
}

// Afficher les résultats de recherche
function displaySearchResults(results, query) {
    const searchResults = document.getElementById('searchResults');
    const searchResultsList = document.getElementById('searchResultsList');
    const searchCount = document.getElementById('searchCount');
    
    searchCount.textContent = results.length;
    
    let html = '';
    results.forEach(result => {
        html += `
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0">
                                ${result.other_user.name}
                                <span class="badge ${result.other_user.role === 'tutor' ? 'bg-success' : 'bg-primary'} ms-1">
                                    ${result.other_user.role === 'tutor' ? 'Tuteur' : 'Étudiant'}
                                </span>
                            </h6>
                            <small class="text-muted">
                                ${new Date(result.message.created_at).toLocaleDateString()}
                            </small>
                        </div>
                        <p class="text-muted mb-1 small">
                            <i class="fas fa-${result.is_sent_by_me ? 'reply text-primary' : 'arrow-right text-success'} me-1"></i>
                            ${result.highlighted_content}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="/messages/${result.other_user.id}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-comments me-1"></i>
                            Ouvrir
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    searchResultsList.innerHTML = html;
    searchResults.style.display = 'block';
}

// Masquer les résultats de recherche
function hideSearchResults() {
    document.getElementById('searchResults').style.display = 'none';
}

// Effacer la recherche
function clearSearch() {
    document.getElementById('searchInput').value = '';
    hideSearchResults();
}

// Filtrer les conversations
function filterConversations() {
    const filter = document.getElementById('filterSelect').value;
    const conversations = document.querySelectorAll('.conversation-item');
    
    conversations.forEach(conversation => {
        let show = true;
        
        switch(filter) {
            case 'unread':
                show = conversation.dataset.unread > 0;
                break;
            case 'tutors':
                show = conversation.dataset.role === 'tutor';
                break;
            case 'students':
                show = conversation.dataset.role === 'student';
                break;
            case 'recent':
                const lastMessageTime = parseInt(conversation.dataset.lastMessageTime);
                const weekAgo = Math.floor(Date.now() / 1000) - (7 * 24 * 60 * 60);
                show = lastMessageTime > weekAgo;
                break;
        }
        
        conversation.style.display = show ? 'block' : 'none';
    });
}

// Marquer comme lu
function markAsRead(userId) {
    fetch(`/messages/${userId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'interface
            const conversation = document.querySelector(`[data-user-id="${userId}"]`);
            const badge = conversation.querySelector('.badge.bg-danger');
            if (badge) {
                badge.remove();
            }
            conversation.dataset.unread = '0';
            
            // Mettre à jour les statistiques
            document.getElementById('unreadMessages').textContent = data.unread_count;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Recherche d'utilisateurs
function searchUsers() {
    const query = document.getElementById('userSearchInput').value.trim();
    const role = document.getElementById('roleFilter').value;
    
    if (query.length < 2) {
        hideUserSearchResults();
        return;
    }
    
    fetch('{{ route("messages.search-users") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            query: query,
            role: role
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayUserSearchResults(data.results);
        }
    })
    .catch(error => {
        console.error('Erreur de recherche d\'utilisateurs:', error);
    });
}

// Afficher les résultats de recherche d'utilisateurs
function displayUserSearchResults(results) {
    const userSearchResults = document.getElementById('userSearchResults');
    const userSearchResultsList = document.getElementById('userSearchResultsList');
    const userSearchCount = document.getElementById('userSearchCount');
    
    userSearchCount.textContent = results.length;
    
    let html = '';
    results.forEach(user => {
        html += `
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        ${user.avatar_url ? 
                            `<img src="${user.avatar_url}" alt="${user.name}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">` :
                            `<i class="fas fa-user-circle fa-2x text-muted"></i>`
                        }
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0">
                                ${user.name}
                                <span class="badge ${user.role === 'tutor' ? 'bg-success' : 'bg-primary'} ms-1">
                                    ${user.role === 'tutor' ? 'Tuteur' : 'Étudiant'}
                                </span>
                            </h6>
                        </div>
                        <p class="text-muted mb-1 small">${user.email}</p>
                        <p class="text-muted mb-1 small">${user.skills_string}</p>
                        ${user.role === 'tutor' ? `
                            <div class="mb-1">
                                <span class="text-warning">${user.rating_stars}</span>
                                <small class="text-muted ms-1">(${user.rating || 0}) - ${user.total_sessions || 0} séances</small>
                            </div>
                        ` : ''}
                    </div>
                    <div class="flex-shrink-0">
                        <a href="${user.conversation_url}" class="btn btn-success btn-sm">
                            <i class="fas fa-comment me-1"></i>
                            Commencer
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    userSearchResultsList.innerHTML = html;
    userSearchResults.style.display = 'block';
}

// Masquer les résultats de recherche d'utilisateurs
function hideUserSearchResults() {
    document.getElementById('userSearchResults').style.display = 'none';
}

// Effacer la recherche d'utilisateurs
function clearUserSearch() {
    document.getElementById('userSearchInput').value = '';
    hideUserSearchResults();
}

// Actualiser les statistiques
function refreshStats() {
    fetch('{{ route("messages.stats") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalConversations').textContent = data.stats.total_conversations;
            document.getElementById('unreadMessages').textContent = data.stats.unread_messages;
            document.getElementById('tutorsContacted').textContent = data.stats.tutors_contacted;
            document.getElementById('studentsContacted').textContent = data.stats.students_contacted;
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'actualisation des statistiques:', error);
    });
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
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
        });
    }
}

// Modifier un message (fonctionnalité future)
function editMessage(messageId) {
    alert('Fonctionnalité de modification en cours de développement');
}
</script>
@endpush

<style>
.conversation-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.conversation-item {
    transition: background-color 0.2s ease;
}

#searchResults .border-bottom:hover {
    background-color: #f8f9fa;
}

.message-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.message-item:hover .message-actions {
    opacity: 1;
}

#searchInput:focus {
    box-shadow: none;
    border-color: #007bff;
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
</style>
@endsection 