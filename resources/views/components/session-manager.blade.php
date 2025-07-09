@props(['user'])

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-shield-alt text-info me-2"></i>
            Sécurité et sessions
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Déconnexion de tous les appareils -->
            <div class="col-md-6">
                <div class="d-grid">
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#logoutAllModal">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Déconnecter de tous les appareils
                    </button>
                    <small class="text-muted mt-1">
                        Termine toutes vos sessions actives sur tous vos appareils
                    </small>
                </div>
            </div>

            <!-- Changer le mot de passe -->
            <div class="col-md-6">
                <div class="d-grid">
                    <a href="#password-section" class="btn btn-outline-info">
                        <i class="fas fa-key me-2"></i>
                        Changer le mot de passe
                    </a>
                    <small class="text-muted mt-1">
                        Mettez à jour votre mot de passe pour plus de sécurité
                    </small>
                </div>
            </div>
        </div>

        <!-- Informations de session -->
        <div class="mt-4">
            <h6 class="text-muted mb-3">Informations de session</h6>
            <div class="row g-2">
                <div class="col-md-6">
                    <small class="text-muted d-block">
                        <i class="fas fa-clock me-2"></i>
                        Dernière connexion : {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}
                    </small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar me-2"></i>
                        Membre depuis : {{ $user->created_at->format('d/m/Y') }}
                    </small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">
                        <i class="fas fa-globe me-2"></i>
                        IP actuelle : {{ request()->ip() }}
                    </small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">
                        <i class="fas fa-browser me-2"></i>
                        Navigateur : {{ request()->header('User-Agent') ? Str::limit(request()->header('User-Agent'), 50) : 'Inconnu' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de déconnexion de tous les appareils -->
<div class="modal fade" id="logoutAllModal" tabindex="-1" aria-labelledby="logoutAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-warning" id="logoutAllModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Déconnexion de tous les appareils
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Attention !</h6>
                    <p class="mb-0">
                        Cette action va déconnecter votre compte de tous vos appareils (ordinateur, téléphone, tablette, etc.).
                        Vous devrez vous reconnecter sur chaque appareil.
                    </p>
                </div>
                
                <p>Êtes-vous sûr de vouloir continuer ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Annuler
                </button>
                <form method="post" action="{{ route('profile.logout-all') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Déconnecter partout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> 