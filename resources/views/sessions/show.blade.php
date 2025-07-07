@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Détails de la séance -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ $session->title }}</h3>
                        <span class="badge {{ $session->getStatusBadgeClass() }} fs-6">
                            {{ $session->getStatusText() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p>{{ $session->description }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Informations</h6>
                            <ul class="list-unstyled">
                                <li><strong>Matière :</strong> {{ $session->getSubjectText() }}</li>
                                <li><strong>Niveau :</strong> {{ ucfirst($session->level) }}</li>
                                <li><strong>Type :</strong> {{ $session->getTypeText() }}</li>
                                <li><strong>Durée :</strong> {{ $session->getFormattedDuration() }}</li>
                                <li><strong>Prix :</strong> {{ $session->getFormattedPrice() }}</li>
                                <li><strong>Date :</strong> {{ $session->scheduled_at->format('d/m/Y H:i') }}</li>
                                @if($session->location)
                                    <li><strong>Lieu :</strong> {{ $session->location }}</li>
                                @endif
                                @if($session->meeting_link)
                                    <li><strong>Lien :</strong> <a href="{{ $session->meeting_link }}" target="_blank">{{ $session->meeting_link }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if($session->notes)
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Notes du tuteur</h6>
                            <p class="mb-0">{{ $session->notes }}</p>
                        </div>
                    @endif

                    <!-- Actions selon le statut -->
                    @if($session->status === 'pending')
                        @if(auth()->user()->isStudent())
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                En attente de réponse du tuteur
                            </div>
                            <form method="POST" action="{{ route('sessions.destroy', $session) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette séance ?')">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler la séance
                                </button>
                            </form>
                        @elseif(auth()->user()->isTutor())
                            <div class="alert alert-info">
                                <i class="fas fa-user-graduate me-2"></i>
                                Demande de séance de {{ $session->student->name }}
                            </div>
                            <div class="btn-group" role="group">
                                <form method="POST" action="{{ route('sessions.update', $session) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i>
                                        Accepter
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('sessions.update', $session) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times me-2"></i>
                                        Refuser
                                    </button>
                                </form>
                            </div>
                        @endif
                    @elseif($session->status === 'accepted')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Séance acceptée ! Préparez-vous pour votre rendez-vous.
                        </div>
                        @if(auth()->user()->isTutor())
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNotesModal">
                                <i class="fas fa-edit me-2"></i>
                                Ajouter des notes
                            </button>
                        @endif
                    @elseif($session->status === 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Séance terminée avec succès !
                        </div>
                    @elseif($session->status === 'cancelled')
                        <div class="alert alert-secondary">
                            <i class="fas fa-times-circle me-2"></i>
                            Séance annulée
                        </div>
                    @endif
                </div>
            </div>

            <!-- Profils des participants -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user-graduate text-primary me-2"></i>
                                Étudiant
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $session->student->name }}</h6>
                                    <p class="text-muted small mb-1">{{ $session->student->email }}</p>
                                    <p class="text-muted small mb-0">Niveau : {{ ucfirst($session->student->level) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user-tie text-success me-2"></i>
                                Tuteur
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $session->tutor->name }}</h6>
                                    <p class="text-muted small mb-1">{{ $session->tutor->email }}</p>
                                    <div class="mb-1">
                                        {!! $session->tutor->getRatingStars() !!}
                                        <small class="text-muted ms-1">({{ $session->tutor->rating ?? 0 }})</small>
                                    </div>
                                    <p class="text-muted small mb-0">{{ $session->tutor->getSkillsString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('messages.show', auth()->user()->isStudent() ? $session->tutor_id : $session->student_id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-comments me-2"></i>
                            Envoyer un message
                        </a>
                        <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>
                            Retour aux séances
                        </a>
                    </div>
                </div>
            </div>

            <!-- Feedback -->
            @if($session->status === 'completed')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-star text-warning me-2"></i>
                            Évaluation
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($session->feedbacks->where('reviewer_id', auth()->id())->count() > 0)
                            <div class="alert alert-success">
                                <i class="fas fa-check me-2"></i>
                                Vous avez déjà évalué cette séance
                            </div>
                        @else
                            <a href="#" class="btn btn-warning w-100">
                                <i class="fas fa-star me-2"></i>
                                Évaluer la séance
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour ajouter des notes (tuteurs) -->
@if(auth()->user()->isTutor() && $session->status === 'accepted')
<div class="modal fade" id="addNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter des notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('sessions.update', $session) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="accepted">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes pour l'étudiant</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" 
                                  placeholder="Ajoutez des informations utiles pour l'étudiant...">{{ $session->notes }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="meeting_link" class="form-label">Lien de réunion (si en ligne)</label>
                        <input type="url" class="form-control" id="meeting_link" name="meeting_link" 
                               value="{{ $session->meeting_link }}" placeholder="https://zoom.us/j/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection 