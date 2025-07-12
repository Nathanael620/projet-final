@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-bell me-2"></i>Gestion des rappels de séances
            </h1>
        </div>
    </div>

    <!-- Bouton d'envoi des rappels -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Envoi des rappels</h5>
                    <p class="card-text">Envoyez des rappels par email pour les séances qui commencent dans les 10 prochaines minutes.</p>
                    <button id="sendRemindersBtn" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer les rappels
                    </button>
                    <div id="reminderStatus" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Séances à venir nécessitant des rappels -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Séances à venir (10 prochaines minutes)
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingSessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tuteur</th>
                                        <th>Étudiant</th>
                                        <th>Date/Heure</th>
                                        <th>Type</th>
                                        <th>Rappel envoyé</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSessions as $session)
                                        <tr>
                                            <td>{{ $session->tutor->name }}</td>
                                            <td>{{ $session->student->name }}</td>
                                            <td>{{ $session->scheduled_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($session->type === 'online')
                                                    <span class="badge bg-primary">En ligne</span>
                                                @else
                                                    <span class="badge bg-secondary">En présentiel</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->reminder_sent)
                                                    <span class="badge bg-success">Oui</span>
                                                @else
                                                    <span class="badge bg-warning">Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucune séance à venir dans les 10 prochaines minutes nécessitant un rappel.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Séances récentes avec rappels envoyés -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Séances récentes avec rappels envoyés (24h)
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentSessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tuteur</th>
                                        <th>Étudiant</th>
                                        <th>Date/Heure</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSessions as $session)
                                        <tr>
                                            <td>{{ $session->tutor->name }}</td>
                                            <td>{{ $session->student->name }}</td>
                                            <td>{{ $session->scheduled_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($session->type === 'online')
                                                    <span class="badge bg-primary">En ligne</span>
                                                @else
                                                    <span class="badge bg-secondary">En présentiel</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($session->status)
                                                    @case('confirmed')
                                                        <span class="badge bg-success">Confirmée</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-info">Terminée</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $session->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucune séance récente avec rappel envoyé.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('sendRemindersBtn').addEventListener('click', function() {
    const btn = this;
    const statusDiv = document.getElementById('reminderStatus');
    
    // Désactiver le bouton
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    statusDiv.innerHTML = '<div class="alert alert-info">Envoi des rappels en cours...</div>';
    
    // Appel AJAX
    fetch('{{ route("admin.send-reminders") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
        } else {
            statusDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    })
    .catch(error => {
        statusDiv.innerHTML = '<div class="alert alert-danger">Erreur lors de l\'envoi des rappels.</div>';
        console.error('Error:', error);
    })
    .finally(() => {
        // Réactiver le bouton
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer les rappels';
        
        // Recharger la page après 2 secondes
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
});
</script>
@endpush
@endsection 