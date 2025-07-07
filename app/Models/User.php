<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'role', // 'student', 'tutor', 'admin'
        'skills', // JSON array of skills
        'level', // 'beginner', 'intermediate', 'advanced'
        'avatar',
        'is_available', // boolean for tutors
        'hourly_rate', // for tutors
        'rating', // average rating
        'total_sessions', // number of sessions completed
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skills' => 'array',
            'is_available' => 'boolean',
            'rating' => 'decimal:2',
        ];
    }

    // Relations
    public function studentSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'student_id');
    }

    public function tutorSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'tutor_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(FAQ::class);
    }

    public function givenFeedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'reviewer_id');
    }

    public function receivedFeedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'reviewed_id');
    }

    /**
     * Check if user is a tutor
     */
    public function isTutor(): bool
    {
        return $this->role === 'tutor';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get user's skills as a formatted string
     */
    public function getSkillsString(): string
    {
        if (empty($this->skills)) {
            return 'Aucune compétence spécifiée';
        }
        return implode(', ', $this->skills);
    }

    /**
     * Get user's rating with stars
     */
    public function getRatingStars(): string
    {
        $rating = $this->rating ?? 0;
        $fullStars = floor($rating);
        $halfStar = $rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        $stars = str_repeat('<i class="fas fa-star text-warning"></i>', $fullStars);
        if ($halfStar) {
            $stars .= '<i class="fas fa-star-half-alt text-warning"></i>';
        }
        $stars .= str_repeat('<i class="far fa-star text-warning"></i>', $emptyStars);
        
        return $stars;
    }

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCount(): int
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    /**
     * Get pending sessions count
     */
    public function getPendingSessionsCount(): int
    {
        if ($this->isTutor()) {
            return $this->tutorSessions()->where('status', 'pending')->count();
        } else {
            return $this->studentSessions()->where('status', 'pending')->count();
        }
    }

    /**
     * Get average rating from received feedbacks
     */
    public function getAverageRating(): float
    {
        $feedbacks = $this->receivedFeedbacks();
        if ($feedbacks->count() === 0) {
            return 0;
        }
        
        return round($feedbacks->avg('rating'), 2);
    }
}
