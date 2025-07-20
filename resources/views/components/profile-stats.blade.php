@props(['stats'])

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-chart-bar text-primary me-2"></i>
            Statistiques
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-center">
                    <h5 class="text-primary mb-1">{{ $stats['total_sessions'] }}</h5>
                    <small class="text-muted">Séances totales</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h5 class="text-success mb-1">{{ $stats['completed_sessions'] }}</h5>
                    <small class="text-muted">Séances terminées</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    @if(auth()->user()->isTutor())
                        <h5 class="text-warning mb-1">{{ number_format($stats['total_earnings'], 2) }}€</h5>
                        <small class="text-muted">Gains totaux</small>
                    @else
                        <h5 class="text-warning mb-1">{{ number_format($stats['total_spent'], 2) }}€</h5>
                        <small class="text-muted">Dépenses totales</small>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h5 class="text-info mb-1">{{ $stats['unread_messages'] }}</h5>
                    <small class="text-muted">Messages non lus</small>
                </div>
            </div>
        </div>
    </div>
</div> 