@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-shield-alt text-primary me-2"></i>
                    Gestion des sessions
                </h1>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour au profil
                </a>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-success mb-1">{{ $activeSessionsCount }}</h3>
                            <p class="text-muted mb-0">Sessions actives</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-info mb-1">{{ $totalSessionsCount }}</h3>
                            <p class="text-muted mb-0">Total des sessions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-warning mb-1">{{ $totalSessionsCount - $activeSessionsCount }}</h3>
                            <p class="text-muted mb-0">Sessions terminées</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions de sécurité -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Actions de sécurité
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-grid">
                                <form method="POST" action="{{ route('profile.logout-all') }}" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir vous déconnecter de tous vos appareils ? Cette action ne peut pas être annulée.')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Se déconnecter de tous les appareils
                                    </button>
                                </form>
                                <small class="text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Déconnecte tous vos appareils sauf celui-ci
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Paramètres de sécurité
                                </a>
                                <small class="text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Modifier le mot de passe et autres paramètres
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des sessions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Historique des sessions
                    </h5>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Appareil</th>
                                        <th>Navigateur</th>
                                        <th>Localisation</th>
                                        <th>Dernière activité</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $session->getDeviceIcon() }} fa-2x text-muted me-3"></i>
                                                <div>
                                                    <div class="fw-bold">{{ ucfirst($session->device_type) }}</div>
                                                    <small class="text-muted">{{ $session->ip_address }}</small>
                                                    @if($session->platform)
                                                        <br><small class="text-muted">{{ $session->platform }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $session->getBrowserIcon() }} fa-2x text-muted me-2"></i>
                                                <span>{{ $session->browser ?? 'Inconnu' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($session->location)
                                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                {{ $session->location }}
                                            @else
                                                <span class="text-muted">Non disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $session->getFormattedLastActivity() }}</div>
                                            <small class="text-muted">{{ $session->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $session->getStatusBadgeClass() }}">
                                                {{ $session->getStatusText() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($session->is_active && !$session->is_current)
                                                <form method="POST" action="{{ route('profile.logout-session', $session->session_id) }}" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir déconnecter cette session ?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times"></i>
                                                        Déconnecter
                                                    </button>
                                                </form>
                                            @elseif($session->is_current)
                                                <span class="badge bg-success">Session actuelle</span>
                                            @else
                                                <span class="text-muted">Déconnectée</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $sessions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h5>Aucune session trouvée</h5>
                            <p class="text-muted">Aucune session n'a été enregistrée pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conseils de sécurité -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Conseils de sécurité
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-2"></i>Bonnes pratiques</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Déconnectez-vous des appareils publics</li>
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Utilisez un mot de passe fort</li>
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Activez l'authentification à deux facteurs</li>
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Surveillez régulièrement vos sessions</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Signes suspects</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Sessions de localisations inconnues</li>
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Activité à des heures inhabituelles</li>
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Appareils non reconnus</li>
                                <li><i class="fas fa-arrow-right text-muted me-2"></i>Connexions multiples simultanées</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 