<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
        'is_active', // account status
        'deactivated_at', // when account was deactivated
        'deactivation_reason', // reason for deactivation
        'last_login_at', // last login timestamp
        'last_activity_at', // last activity timestamp
        'last_ip', // last IP address
        'last_user_agent', // last user agent
        'is_public_profile', // profile visibility
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
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'deleted_at' => 'datetime',
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
        return $this->hasMany(Feedbacks::class, 'reviewer_id');
    }

    public function receivedFeedbacks(): HasMany
    {
        return $this->hasMany(Feedbacks::class, 'reviewed_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
            ->where('notifiable_type', User::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function activeSessions(): HasMany
    {
        return $this->sessions()->where('is_active', true);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->unread();
    }

    public function readNotifications(): HasMany
    {
        return $this->notifications()->read();
    }

    /**
     * Get the payments made by the user.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * Get the wallet associated with the user.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id');
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
     * Get unread notifications count
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
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

    /**
     * Get user's level as a formatted string
     */
    public function getLevelText(): string
    {
        return match($this->level) {
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
            default => 'Non spécifié'
        };
    }

    /**
     * Get user's skills as formatted badges
     */
    public function getSkillsBadges(): array
    {
        if (empty($this->skills)) {
            return [];
        }

        $skillLabels = [
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
            'philosophy' => 'Philosophie',
            'art' => 'Art',
            'music' => 'Musique',
            'sports' => 'Sport',
            'other' => 'Autre'
        ];

        $badges = [];
        foreach ($this->skills as $skill) {
            $badges[] = $skillLabels[$skill] ?? ucfirst($skill);
        }

        return $badges;
    }

    /**
     * Get user's availability status text
     */
    public function getAvailabilityText(): string
    {
        return $this->is_available ? 'Disponible' : 'Indisponible';
    }

    /**
     * Get user's availability status class
     */
    public function getAvailabilityClass(): string
    {
        return $this->is_available ? 'success' : 'danger';
    }

    /**
     * Get user's total earnings (for tutors)
     */
    public function getTotalEarnings(): float
    {
        if (!$this->isTutor()) {
            return 0;
        }
        
        return $this->tutorSessions()
            ->where('status', 'completed')
            ->sum('price');
    }

    /**
     * Get user's total spent (for students)
     */
    public function getTotalSpent(): float
    {
        if (!$this->isStudent()) {
            return 0;
        }
        
        return $this->studentSessions()
            ->where('status', 'completed')
            ->sum('price');
    }

    /**
     * Get user's completion rate
     */
    public function getCompletionRate(): float
    {
        $totalSessions = $this->isTutor() 
            ? $this->tutorSessions()->count() 
            : $this->studentSessions()->count();
            
        if ($totalSessions === 0) {
            return 0;
        }
        
        $completedSessions = $this->isTutor() 
            ? $this->tutorSessions()->where('status', 'completed')->count() 
            : $this->studentSessions()->where('status', 'completed')->count();
            
        return round(($completedSessions / $totalSessions) * 100, 1);
    }

    /**
     * Get masked phone number for privacy
     */
    public function getMaskedPhone(): string
    {
        if (!$this->phone) {
            return 'Non renseigné';
        }
        
        return 'Numéro masqué pour la confidentialité';
    }

    /**
     * Check if phone number should be visible (only for admin or own profile)
     */
    public function canViewPhone(User $viewer = null): bool
    {
        // Admin can always see phone numbers
        if ($viewer && $viewer->isAdmin()) {
            return true;
        }
        
        // User can see their own phone number
        if ($viewer && $viewer->id === $this->id) {
            return true;
        }
        
        return false;
    }
}
