<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'type',
        'file_path',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relations
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Méthodes utilitaires
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function getTypeIcon(): string
    {
        return match($this->type) {
            'text' => 'fas fa-comment',
            'file' => 'fas fa-file',
            'image' => 'fas fa-image',
            default => 'fas fa-comment',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match($this->type) {
            'text' => 'bg-primary',
            'file' => 'bg-info',
            'image' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getFormattedContent(): string
    {
        if (strlen($this->content) > 100) {
            return substr($this->content, 0, 100) . '...';
        }
        
        return $this->content;
    }
}
