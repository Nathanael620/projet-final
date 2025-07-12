@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-cog me-2"></i>Tableau de bord Administrateur
            </h1>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\User::count() }}</h4>
                            <p class="card-text">Utilisateurs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\Session::where('status', 'confirmed')->count() }}</h4>
                            <p class="card-text">Séances confirmées</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\Payment::where('status', 'completed')->sum('amount') }}€</h4>
                            <p class="card-text">Revenus totaux</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-euro-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\Message::count() }}</h4>
                            <p class="card-text">Messages</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.session-reminders') }}" class="btn btn-primary w-100">
                                <i class="fas fa-bell me-2"></i>Gérer les rappels
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-users me-2"></i>Gérer les utilisateurs
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.sessions') }}" class="btn btn-success w-100">
                                <i class="fas fa-calendar me-2"></i>Gérer les séances
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('faqs.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-question-circle me-2"></i>Gérer les FAQ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Séances récentes -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Séances à venir (24h)
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $upcomingSessions = \App\Models\Session::where('status', 'confirmed')
                            ->where('scheduled_at', '>=', now())
                            ->where('scheduled_at', '<=', now()->addDay())
                            ->with(['tutor', 'student'])
                            ->orderBy('scheduled_at')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($upcomingSessions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingSessions as $session)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $session->tutor->name }}</strong> → {{ $session->student->name }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $session->scheduled_at->format('d/m/Y H:i') }} 
                                            ({{ $session->type }})
                                        </small>
                                    </div>
                                    <span class="badge bg-primary">{{ $session->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Aucune séance à venir dans les 24h</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Séances en attente
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $pendingSessions = \App\Models\Session::where('status', 'pending')
                            ->with(['tutor', 'student'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($pendingSessions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingSessions as $session)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $session->tutor->name }}</strong> → {{ $session->student->name }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $session->scheduled_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-warning">{{ $session->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Aucune séance en attente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 