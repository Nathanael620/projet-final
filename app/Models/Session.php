<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory;

    protected $table = 'support_sessions';

    protected $fillable = [
        'student_id',
        'tutor_id',
        'title',
        'description',
        'subject',
        'level',
        'type',
        'status',
        'scheduled_at',
        'duration_minutes',
        'price',
        'meeting_link',
        'location',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    // Relations
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(feedbacks::class);
    }

    // Méthodes utilitaires
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'accepted' => 'bg-info',
            'rejected' => 'bg-danger',
            'completed' => 'bg-success',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'accepted' => 'Acceptée',
            'rejected' => 'Refusée',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            default => 'Inconnu',
        };
    }

    public function getSubjectText(): string
    {
        return match($this->subject) {
            'mathematics' => 'Mathématiques',
            'physics' => 'Physique',
            'chemistry' => 'Chimie',
            'biology' => 'Biologie',
            'computer_science' => 'Informatique',
            'languages' => 'Langues',
            'literature' => 'Littérature',
            'history' => 'Histoire',
            'geography' => 'Géographie',
            'economics' => 'Économie',
            'other' => 'Autre',
            default => 'Inconnu',
        };
    }

    public function getTypeText(): string
    {
        return match($this->type) {
            'online' => 'En ligne',
            'in_person' => 'En présentiel',
            default => 'Inconnu',
        };
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2) . '€';
    }

    public function getFormattedDuration(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'min' : '');
        }
        
        return $minutes . 'min';
    }
}
