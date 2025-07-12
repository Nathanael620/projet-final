<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'is_current',
        'is_active',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function getDeviceIcon(): string
    {
        return match($this->device_type) {
            'mobile' => 'fas fa-mobile-alt',
            'tablet' => 'fas fa-tablet-alt',
            'desktop' => 'fas fa-desktop',
            default => 'fas fa-question-circle',
        };
    }

    public function getBrowserIcon(): string
    {
        return match(strtolower($this->browser)) {
            'chrome' => 'fab fa-chrome',
            'firefox' => 'fab fa-firefox-browser',
            'safari' => 'fab fa-safari',
            'edge' => 'fab fa-edge',
            'opera' => 'fab fa-opera',
            default => 'fas fa-globe',
        };
    }

    public function getPlatformIcon(): string
    {
        return match(strtolower($this->platform)) {
            'windows' => 'fab fa-windows',
            'macos' => 'fab fa-apple',
            'linux' => 'fab fa-linux',
            'ios' => 'fab fa-app-store-ios',
            'android' => 'fab fa-android',
            default => 'fas fa-desktop',
        };
    }

    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'bg-danger';
        }
        
        if ($this->is_current) {
            return 'bg-success';
        }
        
        return 'bg-warning';
    }

    public function getStatusText(): string
    {
        if (!$this->is_active) {
            return 'Déconnecté';
        }
        
        if ($this->is_current) {
            return 'Session actuelle';
        }
        
        return 'Active';
    }

    public function getFormattedLastActivity(): string
    {
        if (!$this->last_activity) {
            return 'Jamais';
        }
        
        $diff = now()->diffInMinutes($this->last_activity);
        
        if ($diff < 1) {
            return 'À l\'instant';
        } elseif ($diff < 60) {
            return "Il y a {$diff} min";
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return "Il y a {$hours}h";
        } else {
            $days = floor($diff / 1440);
            return "Il y a {$days}j";
        }
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Méthodes statiques
    public static function createFromRequest($user, $sessionId): self
    {
        $agent = new Agent();
        $request = request();
        
        return self::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'location' => self::getLocationFromIP($request->ip()),
            'is_current' => true,
            'is_active' => true,
            'last_activity' => now(),
            'expires_at' => now()->addMinutes(config('session.lifetime', 120)),
        ]);
    }

    private static function getLocationFromIP($ip): ?string
    {
        // Pour l'instant, on retourne null
        // Vous pouvez intégrer un service comme MaxMind ou IP-API pour obtenir la localisation
        return null;
    }

    public static function updateLastActivity($sessionId): void
    {
        self::where('session_id', $sessionId)
            ->update([
                'last_activity' => now(),
                'expires_at' => now()->addMinutes(config('session.lifetime', 120)),
            ]);
    }

    public static function deactivateSession($sessionId): void
    {
        self::where('session_id', $sessionId)
            ->update([
                'is_active' => false,
                'is_current' => false,
            ]);
    }

    public static function deactivateAllUserSessions($userId, $exceptSessionId = null): void
    {
        $query = self::where('user_id', $userId);
        
        if ($exceptSessionId) {
            $query->where('session_id', '!=', $exceptSessionId);
        }
        
        $query->update([
            'is_active' => false,
            'is_current' => false,
        ]);
    }

    public static function cleanupExpiredSessions(): int
    {
        return self::where('expires_at', '<', now())
            ->orWhere('last_activity', '<', now()->subDays(30))
            ->delete();
    }
} 