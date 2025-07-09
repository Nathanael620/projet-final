@props(['user'])

<div class="row g-3">
    <!-- Séances totales -->
    <div class="col-md-3">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-calendar-check fa-2x text-primary"></i>
                </div>
                <h5 class="text-primary mb-1">
                    {{ $user->isTutor() ? $user->tutorSessions()->count() : $user->studentSessions()->count() }}
                </h5>
                <small class="text-muted">Séances totales</small>
            </div>
        </div>
    </div>

    <!-- Séances terminées -->
    <div class="col-md-3">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h5 class="text-success mb-1">
                    {{ $user->isTutor() 
                        ? $user->tutorSessions()->where('status', 'completed')->count() 
                        : $user->studentSessions()->where('status', 'completed')->count() }}
                </h5>
                <small class="text-muted">Séances terminées</small>
            </div>
        </div>
    </div>

    <!-- Taux de complétion -->
    <div class="col-md-3">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-percentage fa-2x text-info"></i>
                </div>
                <h5 class="text-info mb-1">{{ $user->getCompletionRate() }}%</h5>
                <small class="text-muted">Taux de complétion</small>
            </div>
        </div>
    </div>

    <!-- Gains/Dépenses -->
    <div class="col-md-3">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-euro-sign fa-2x text-warning"></i>
                </div>
                <h5 class="text-warning mb-1">
                    @if($user->isTutor())
                        {{ number_format($user->getTotalEarnings(), 2) }}€
                        <small class="d-block text-muted">Gains totaux</small>
                    @else
                        {{ number_format($user->getTotalSpent(), 2) }}€
                        <small class="d-block text-muted">Dépenses totales</small>
                    @endif
                </h5>
            </div>
        </div>
    </div>
</div>

@if($user->isTutor())
<!-- Statistiques spécifiques aux tuteurs -->
<div class="row g-3 mt-3">
    <!-- Note moyenne -->
    <div class="col-md-4">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-star fa-2x text-warning"></i>
                </div>
                <h5 class="text-warning mb-1">{{ number_format($user->rating ?? 0, 1) }}</h5>
                <small class="text-muted">Note moyenne</small>
                <div class="mt-1">
                    {!! $user->getRatingStars() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Tarif horaire -->
    <div class="col-md-4">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-clock fa-2x text-success"></i>
                </div>
                <h5 class="text-success mb-1">{{ number_format($user->hourly_rate ?? 20, 2) }}€</h5>
                <small class="text-muted">Tarif horaire</small>
            </div>
        </div>
    </div>

    <!-- Disponibilité -->
    <div class="col-md-4">
        <div class="card bg-light border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-toggle-{{ $user->is_available ? 'on' : 'off' }} fa-2x text-{{ $user->getAvailabilityClass() }}"></i>
                </div>
                <h5 class="text-{{ $user->getAvailabilityClass() }} mb-1">
                    {{ $user->getAvailabilityText() }}
                </h5>
                <small class="text-muted">Statut</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Messages non lus -->
@if($user->getUnreadMessagesCount() > 0)
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-envelope me-2"></i>
            Vous avez <strong>{{ $user->getUnreadMessagesCount() }}</strong> message(s) non lu(s).
            <a href="{{ route('messages.index') }}" class="alert-link ms-2">Voir les messages</a>
        </div>
    </div>
</div>
@endif 