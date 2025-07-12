@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-users me-2"></i>Gestion des utilisateurs
            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des utilisateurs</h5>
        </div>
        <div class="card-body">
            @php
                $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(20);
            @endphp
            
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Niveau</th>
                                <th>Statut</th>
                                <th>Inscrit le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @switch($user->role)
                                            @case('admin')
                                                <span class="badge bg-danger">Admin</span>
                                                @break
                                            @case('tutor')
                                                <span class="badge bg-primary">Tuteur</span>
                                                @break
                                            @case('student')
                                                <span class="badge bg-success">Étudiant</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $user->role }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $user->level ?? 'Non défini' }}</td>
                                    <td>
                                        @if($user->is_available)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-warning">Indisponible</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('profile.show', $user->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @else
                <p class="text-muted">Aucun utilisateur trouvé.</p>
            @endif
        </div>
    </div>
</div>
@endsection 