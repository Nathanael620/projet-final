@extends('layouts.app')

@section('title', 'Chatbot FAQ')

@section('content')
@auth
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-robot fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">Assistant FAQ IA</h4>
                            <small class="opacity-75">Posez vos questions et obtenez des réponses intelligentes</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Zone de chat -->
                        <div class="col-md-8">
                            <div class="chat-container" id="chatContainer" style="height: 500px; overflow-y: auto; padding: 1.5rem;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                                    <h5>Bienvenue {{ auth()->user()->name }} !</h5>
                                    <p>Posez votre question et je vous aiderai à trouver la réponse la plus appropriée.</p>
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-search text-primary mb-2"></i>
                                                    <h6>Recherche intelligente</h6>
                                                    <small>Trouvez rapidement les réponses</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-lightbulb text-warning mb-2"></i>
                                                    <h6>Suggestions IA</h6>
                                                    <small>Obtenez des recommandations</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-top p-3">
                                <form id="chatForm" class="d-flex gap-2">
                                    <input type="text" id="questionInput" class="form-control" 
                                           placeholder="Tapez votre question..." required maxlength="500">
                                    <button type="submit" class="btn btn-primary" id="sendButton">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Sidebar avec FAQ populaires et suggestions -->
                        <div class="col-md-4 border-start">
                            <div class="p-3">
                                <h6 class="mb-3">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Questions populaires
                                </h6>
                                <div class="mb-4">
                                    @foreach($popularFaqs as $faq)
                                    <div class="mb-2">
                                        <button class="btn btn-outline-secondary btn-sm w-100 text-start" 
                                                onclick="askQuestion('{{ addslashes($faq->question) }}')"
                                                title="{{ $faq->question }}">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $faq->getCategoryIcon() }} me-2 text-muted"></i>
                                                <div class="text-truncate">{{ Str::limit($faq->question, 40) }}</div>
                                            </div>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <hr>
                                
                                <h6 class="mb-3">
                                    <i class="fas fa-tags text-info me-2"></i>
                                    Catégories
                                </h6>
                                <div class="mb-3">
                                    @foreach($categories as $key => $label)
                                    <button class="btn btn-outline-primary btn-sm me-1 mb-1" 
                                            onclick="filterByCategory('{{ $key }}')">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>
                                
                                <hr>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Powered by AI
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                    <h4>Accès restreint</h4>
                    <p class="text-muted">Vous devez être connecté pour utiliser le chatbot IA.</p>
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            S'inscrire
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const questionInput = document.getElementById('questionInput');
    const chatContainer = document.getElementById('chatContainer');
    const sendButton = document.getElementById('sendButton');
    
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const question = questionInput.value.trim();
        if (!question) return;
        
        // Log de débogage
        console.log('Sending question:', question);
        console.log('CSRF token:', '{{ csrf_token() }}');
        
        // Désactiver le bouton pendant l'envoi
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Ajouter la question de l'utilisateur
        addMessage(question, 'user');
        questionInput.value = '';
        
        // Afficher le chargement
        addLoadingMessage();
        
        // Envoyer la question au serveur
        fetch('{{ route("faqs.chatbot.ask") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                question: question
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            removeLoadingMessage();
            
            if (data.success) {
                addMessage(data.data.answer, 'bot');
                
                // Afficher les FAQ similaires si disponibles
                if (data.data.existing_faqs && data.data.existing_faqs.length > 0) {
                    addSimilarFaqs(data.data.existing_faqs);
                }
                
                // Ajouter les suggestions si disponibles
                if (data.data.suggestions && data.data.suggestions.length > 0) {
                    addSuggestions(data.data.suggestions);
                }
                
                // Afficher la confiance
                if (data.data.confidence) {
                    addConfidenceIndicator(data.data.confidence);
                }
            } else {
                console.error('Error response:', data);
                addMessage(data.message || 'Désolé, je ne peux pas traiter votre question pour le moment.', 'bot error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            removeLoadingMessage();
            addMessage('Erreur de connexion. Veuillez réessayer.', 'bot error');
        })
        .finally(() => {
            // Réactiver le bouton
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    });
    
    function addMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-3 ${type === 'user' ? 'text-end' : ''}`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = `d-inline-block p-3 rounded ${type === 'user' ? 'bg-primary text-white' : 'bg-light'}`;
        bubbleDiv.style.maxWidth = '80%';
        
        if (type === 'bot' && type !== 'error') {
            bubbleDiv.innerHTML = message.replace(/\n/g, '<br>');
        } else {
            bubbleDiv.textContent = message;
        }
        
        messageDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function addLoadingMessage() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'mb-3';
        loadingDiv.id = 'loadingMessage';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'd-inline-block p-3 rounded bg-light';
        bubbleDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Réflexion en cours...';
        
        loadingDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(loadingDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function removeLoadingMessage() {
        const loadingMessage = document.getElementById('loadingMessage');
        if (loadingMessage) {
            loadingMessage.remove();
        }
    }
    
    function addSimilarFaqs(faqs) {
        const faqsDiv = document.createElement('div');
        faqsDiv.className = 'mb-3';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'd-inline-block p-3 rounded bg-info text-white';
        bubbleDiv.innerHTML = '<strong><i class="fas fa-lightbulb me-2"></i>Questions similaires trouvées :</strong><br><br>';
        
        faqs.forEach(faq => {
            const faqDiv = document.createElement('div');
            faqDiv.className = 'mb-2 p-2 bg-white text-dark rounded';
            faqDiv.innerHTML = `
                <div class="fw-bold">${faq.question}</div>
                <div class="small text-muted">${faq.answer}</div>
                <div class="mt-1">
                    <span class="badge bg-secondary">${faq.category}</span>
                    <span class="badge bg-success">${faq.votes} votes</span>
                </div>
            `;
            bubbleDiv.appendChild(faqDiv);
        });
        
        faqsDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(faqsDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function addSuggestions(suggestions) {
        const suggestionsDiv = document.createElement('div');
        suggestionsDiv.className = 'mb-3';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'd-inline-block p-3 rounded bg-warning text-dark';
        bubbleDiv.innerHTML = '<strong><i class="fas fa-lightbulb me-2"></i>Suggestions :</strong><br><br>';
        
        suggestions.forEach(suggestion => {
            const button = document.createElement('button');
            button.className = 'btn btn-sm btn-outline-primary me-2 mb-1';
            button.textContent = suggestion;
            button.onclick = () => askQuestion(suggestion);
            bubbleDiv.appendChild(button);
        });
        
        suggestionsDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(suggestionsDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function addConfidenceIndicator(confidence) {
        const confidenceDiv = document.createElement('div');
        confidenceDiv.className = 'mb-3';
        
        const percentage = Math.round(confidence * 100);
        let color = 'success';
        if (percentage < 50) color = 'danger';
        else if (percentage < 75) color = 'warning';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'd-inline-block p-2 rounded bg-light';
        bubbleDiv.innerHTML = `
            <small class="text-muted">
                <i class="fas fa-chart-line me-1"></i>
                Confiance : <span class="text-${color} fw-bold">${percentage}%</span>
            </small>
        `;
        
        confidenceDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(confidenceDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
});

function askQuestion(question) {
    document.getElementById('questionInput').value = question;
    document.getElementById('chatForm').dispatchEvent(new Event('submit'));
}

function filterByCategory(category) {
    // Fonctionnalité future pour filtrer par catégorie
    console.log('Filtrer par catégorie:', category);
}
</script>
@endpush
@endsection 