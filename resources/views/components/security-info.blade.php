@props(['user'])

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-shield-alt text-success me-2"></i>
            Informations de sécurité
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Statut du compte -->
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-circle text-{{ $user->is_active ? 'success' : 'danger' }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Statut du compte</h6>
                        <p class="text-muted mb-0">
                            {{ $user->is_active ? 'Actif' : 'Désactivé' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Email vérifié -->
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-{{ $user->email_verified_at ? 'check-circle text-success' : 'exclamation-triangle text-warning' }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Email vérifié</h6>
                        <p class="text-muted mb-0">
                            {{ $user->email_verified_at ? 'Oui' : 'Non' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dernière connexion -->
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clock text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Dernière connexion</h6>
                        <p class="text-muted mb-0">
                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dernière activité -->
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-user-clock text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Dernière activité</h6>
                        <p class="text-muted mb-0">
                            {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Inconnue' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- IP de connexion -->
            @if($user->last_ip)
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-globe text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Dernière IP</h6>
                        <p class="text-muted mb-0">
                            {{ $user->last_ip }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Navigateur -->
            @if($user->last_user_agent)
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-browser text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Navigateur</h6>
                        <p class="text-muted mb-0">
                            {{ Str::limit($user->last_user_agent, 30) }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions de sécurité -->
        <div class="mt-4 pt-3 border-top">
            <h6 class="text-muted mb-3">Actions de sécurité</h6>
            <div class="row g-2">
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#logoutAllModal">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Déconnecter partout
                    </button>
                </div>
                <div class="col-md-4">
                    <a href="#password-section" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-key me-1"></i>
                        Changer mot de passe
                    </a>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deactivateAccountModal">
                        <i class="fas fa-pause me-1"></i>
                        Désactiver compte
                    </button>
                </div>
            </div>
        </div>

        <!-- Alertes de sécurité -->
        @if(!$user->email_verified_at)
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Email non vérifié :</strong> 
            Votre email n'est pas vérifié. 
            <button form="send-verification" class="btn btn-link btn-sm p-0 ms-1">
                Renvoyer l'email de vérification
            </button>
        </div>
        @endif

        @if($user->last_login_at && $user->last_login_at->diffInDays(now()) > 30)
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Inactivité détectée :</strong> 
            Vous ne vous êtes pas connecté depuis {{ $user->last_login_at->diffInDays(now()) }} jours.
        </div>
        @endif
    </div>
</div> 