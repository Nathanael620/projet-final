@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
            </div>
            <h1 class="display-4 fw-bold text-danger mb-3">403</h1>
            <h2 class="h4 mb-3">Accès non autorisé</h2>
            <p class="text-muted mb-4">
                Vous n'avez pas les permissions nécessaires pour accéder à cette page.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>
                    Retour au tableau de bord
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 