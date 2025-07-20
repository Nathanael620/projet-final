@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Détails de la séance -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ $session->title }}</h4>
                        <span class="badge {{ $session->getStatusBadgeClass() }} fs-6">
                            {{ $session->getStatusText() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Informations générales</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-book text-primary me-2"></i>
                                    <strong>Matière :</strong> {{ $session->getSubjectText() }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-signal text-info me-2"></i>
                                    <strong>Niveau :</strong> {{ ucfirst($session->level) }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <strong>Durée :</strong> {{ $session->getFormattedDuration() }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-euro-sign text-success me-2"></i>
                                    <strong>Prix :</strong> {{ $session->getFormattedPrice() }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Détails de la séance</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-secondary me-2"></i>
                                    <strong>Date :</strong> {{ $session->scheduled_at->format('d/m/Y') }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-secondary me-2"></i>
                                    <strong>Heure :</strong> {{ $session->scheduled_at->format('H:i') }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-video text-secondary me-2"></i>
                                    <strong>Type :</strong> 
                                    @if($session->type === 'online')
                                        <span class="text-primary">En ligne</span>
                                    @else
                                        <span class="text-success">En présentiel</span>
                                    @endif
                                </li>
                                @if($session->location)
                                <li class="mb-2">
                                    <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                                    <strong>Lieu :</strong> {{ $session->location }}
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p class="text-muted">{{ $session->description }}</p>
                    </div>

                    @if($session->notes)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Notes du tuteur</h6>
                        <div class="alert alert-light">
                            {{ $session->notes }}
                        </div>
                    </div>
                    @endif

                    @if($session->meeting_link)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Lien de la réunion</h6>
                        <a href="{{ $session->meeting_link }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Rejoindre la réunion
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions selon le statut -->
            @if($session->status === 'pending')
                @if(auth()->user()->isTutor() && $session->tutor_id === auth()->id())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Répondre à la demande</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sessions.update', $session) }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (optionnel)</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control" 
                                          placeholder="Ajoutez des notes ou instructions pour l'étudiant...">{{ old('notes', $session->notes) }}</textarea>
                            </div>

                            @if($session->type === 'online')
                            <div class="mb-3">
                                <label for="meeting_link" class="form-label">Lien de la réunion</label>
                                <input type="url" name="meeting_link" id="meeting_link" class="form-control" 
                                       value="{{ old('meeting_link', $session->meeting_link) }}" 
                                       placeholder="https://meet.google.com/... ou https://zoom.us/...">
                            </div>
                            @endif

                            <div class="d-flex gap-2">
                                <button type="submit" name="status" value="accepted" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>
                                    Accepter la séance
                                </button>
                                <button type="submit" name="status" value="rejected" class="btn btn-danger">
                                    <i class="fas fa-times me-2"></i>
                                    Refuser la séance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @elseif(auth()->user()->isStudent() && $session->student_id === auth()->id())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sessions.destroy', $session) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette séance ?')">
                                <i class="fas fa-times me-2"></i>
                                Annuler la séance
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endif

            @if($session->status === 'accepted')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Séance acceptée</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Cette séance a été acceptée par le tuteur. Préparez-vous pour votre session !
                    </div>
                    
                    @if($session->meeting_link)
                    <div class="mb-3">
                        <a href="{{ $session->meeting_link }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-video me-2"></i>
                            Rejoindre la réunion
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($session->status === 'completed')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Séance terminée</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-check-circle me-2"></i>
                        Cette séance est terminée. N'oubliez pas de noter votre expérience !
                    </div>
                    
                    @php
                        $user = auth()->user();
                        $feedbackType = $user->isTutor() ? 'tutor_to_student' : 'student_to_tutor';
                        $existingFeedback = $session->feedbacks()
                            ->where('reviewer_id', $user->id)
                            ->where('type', $feedbackType)
                            ->first();
                    @endphp
                    
                    @if($existingFeedback)
                        <div class="alert alert-success">
                            <i class="fas fa-star me-2"></i>
                            Vous avez déjà noté cette séance avec {{ $existingFeedback->rating }}/5 étoiles.
                            <a href="{{ route('feedback.edit', $existingFeedback) }}" class="btn btn-sm btn-outline-primary ms-2">
                                Modifier
                            </a>
                        </div>
                    @else
                        <a href="{{ route('feedback.create', $session) }}" class="btn btn-warning">
                            <i class="fas fa-star me-2"></i>
                            Noter cette séance
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Profils des participants -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Participants</h5>
                </div>
                <div class="card-body">
                    <!-- Étudiant -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-graduate fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $session->student->name }}</h6>
                            <small class="text-muted">Étudiant</small>
                        </div>
                    </div>

                    <!-- Tuteur -->
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-tie fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $session->tutor->name }}</h6>
                            <small class="text-muted">Tuteur</small>
                            <div class="mt-1">
                                {!! $session->tutor->getRatingStars() !!}
                                <small class="text-muted ms-1">({{ $session->tutor->rating ?? 0 }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('messages.show', auth()->user()->isStudent() ? $session->tutor : $session->student) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-comments me-2"></i>
                            Envoyer un message
                        </a>
                        
                        @if(auth()->user()->isStudent())
                        <a href="{{ route('tutors.show', $session->tutor) }}" class="btn btn-outline-info">
                            <i class="fas fa-user me-2"></i>
                            Voir le profil du tuteur
                        </a>
                        @else
                        <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>
                            Retour aux séances
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations de paiement -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Informations de paiement</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tarif horaire :</span>
                        <strong>{{ number_format($session->tutor->hourly_rate ?? 20, 2) }}€</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Durée :</span>
                        <strong>{{ $session->getFormattedDuration() }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total :</span>
                        <strong class="text-success">{{ $session->getFormattedPrice() }}</strong>
                    </div>
                    
                    @if($session->status === 'accepted')
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Le paiement sera effectué après la séance
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex space-x-4">
                <a href="{{ route('sessions.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Retour aux sessions
                </a>
                @if(auth()->user()->role === 'student')
                    <a href="{{ route('payments.create', $session) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Réserver cette session
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 