@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-calendar me-2"></i>Gestion des séances
            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des séances</h5>
        </div>
        <div class="card-body">
            @php
                $sessions = \App\Models\Session::with(['tutor', 'student'])
                    ->orderBy('scheduled_at', 'desc')
                    ->paginate(20);
            @endphp
            
            @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tuteur</th>
                                <th>Étudiant</th>
                                <th>Date/Heure</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Rappel envoyé</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td>{{ $session->id }}</td>
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
                                            @case('pending')
                                                <span class="badge bg-warning">En attente</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-success">Confirmée</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Annulée</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">Terminée</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $session->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($session->reminder_sent)
                                            <span class="badge bg-success">Oui</span>
                                        @else
                                            <span class="badge bg-warning">Non</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sessions.show', $session->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $sessions->links() }}
                </div>
            @else
                <p class="text-muted">Aucune séance trouvée.</p>
            @endif
        </div>
    </div>
</div>
@endsection 