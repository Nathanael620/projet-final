<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedbacks extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'reviewer_id',
        'reviewed_id',
        'rating',
        'comment',
        'type',
        'is_anonymous',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    // Relations
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }

    // Méthodes utilitaires
    public function getRatingStars(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-warning"></i>';
            }
        }
        return $stars;
    }

    public function getTypeText(): string
    {
        return match($this->type) {
            'student_to_tutor' => 'Étudiant vers Tuteur',
            'tutor_to_student' => 'Tuteur vers Étudiant',
            default => 'Inconnu',
        };
    }

    public function getReviewerName(): string
    {
        if ($this->is_anonymous) {
            return 'Anonyme';
        }
        
        return $this->reviewer->name;
    }

    public function getFormattedComment(): string
    {
        if (strlen($this->comment) > 200) {
            return substr($this->comment, 0, 200) . '...';
        }
        
        return $this->comment;
    }
}
