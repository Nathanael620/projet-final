<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FAQ extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question',
        'answer',
        'category',
        'status',
        'votes',
        'is_featured',
        'is_public',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'answered' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'answered' => 'Répondu',
            'closed' => 'Fermé',
            default => 'Inconnu',
        };
    }

    public function getCategoryText(): string
    {
        return match($this->category) {
            'general' => 'Général',
            'technical' => 'Technique',
            'payment' => 'Paiement',
            'sessions' => 'Séances',
            'account' => 'Compte',
            'other' => 'Autre',
            default => 'Inconnu',
        };
    }

    public function getCategoryLabel(): string
    {
        return $this->getCategoryText();
    }

    public function getCategoryColor(): string
    {
        return match($this->category) {
            'general' => 'primary',
            'technical' => 'info',
            'payment' => 'success',
            'sessions' => 'warning',
            'account' => 'secondary',
            'other' => 'dark',
            default => 'secondary',
        };
    }

    public function getCategoryIcon(): string
    {
        return match($this->category) {
            'general' => 'fas fa-info-circle',
            'technical' => 'fas fa-cog',
            'payment' => 'fas fa-credit-card',
            'sessions' => 'fas fa-calendar',
            'account' => 'fas fa-user',
            'other' => 'fas fa-question',
            default => 'fas fa-question',
        };
    }

    public function getCategoryBadgeClass(): string
    {
        return match($this->category) {
            'general' => 'bg-primary',
            'technical' => 'bg-info',
            'payment' => 'bg-success',
            'sessions' => 'bg-warning',
            'account' => 'bg-secondary',
            'other' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    public function incrementVotes(): void
    {
        $this->increment('votes');
    }

    public function decrementVotes(): void
    {
        $this->decrement('votes');
    }
}
