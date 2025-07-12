<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Créer une notification pour un utilisateur
     */
    public function createNotification(User $user, string $type, array $data): Notification
    {
        return Notification::create([
            'uuid' => Str::uuid(),
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => $data,
        ]);
    }

    /**
     * Créer une notification de nouveau message
     */
    public function notifyNewMessage(User $recipient, User $sender, string $messageContent): Notification
    {
        return $this->createNotification($recipient, 'new_message', [
            'title' => 'Nouveau message',
            'message' => "Vous avez reçu un nouveau message de {$sender->name}",
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_content' => Str::limit($messageContent, 100),
            'conversation_url' => route('messages.show', $sender->id),
            'icon' => 'fas fa-comment',
            'color' => 'primary',
        ]);
    }

    /**
     * Créer une notification de message modifié
     */
    public function notifyMessageEdited(User $recipient, User $sender, string $messageContent): Notification
    {
        return $this->createNotification($recipient, 'message_edited', [
            'title' => 'Message modifié',
            'message' => "{$sender->name} a modifié un message",
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_content' => Str::limit($messageContent, 100),
            'conversation_url' => route('messages.show', $sender->id),
            'icon' => 'fas fa-edit',
            'color' => 'warning',
        ]);
    }

    /**
     * Créer une notification de nouvelle séance
     */
    public function notifyNewSession(User $recipient, User $sender, array $sessionData): Notification
    {
        return $this->createNotification($recipient, 'new_session', [
            'title' => 'Nouvelle séance',
            'message' => "{$sender->name} a demandé une nouvelle séance",
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'session_data' => $sessionData,
            'session_url' => route('sessions.show', $sessionData['id'] ?? 0),
            'icon' => 'fas fa-calendar-plus',
            'color' => 'success',
        ]);
    }

    /**
     * Créer une notification de séance annulée
     */
    public function notifySessionCancelled(User $recipient, User $sender, array $sessionData): Notification
    {
        return $this->createNotification($recipient, 'session_cancelled', [
            'title' => 'Séance annulée',
            'message' => "{$sender->name} a annulé une séance",
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'session_data' => $sessionData,
            'icon' => 'fas fa-calendar-times',
            'color' => 'danger',
        ]);
    }

    /**
     * Créer une notification de paiement
     */
    public function notifyPayment(User $recipient, array $paymentData): Notification
    {
        return $this->createNotification($recipient, 'payment', [
            'title' => 'Paiement reçu',
            'message' => "Paiement de {$paymentData['amount']}€ reçu",
            'payment_data' => $paymentData,
            'icon' => 'fas fa-credit-card',
            'color' => 'success',
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsRead(User $user): void
    {
        $user->markAllNotificationsAsRead();
    }

    /**
     * Supprimer une notification
     */
    public function deleteNotification(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Supprimer toutes les notifications lues d'un utilisateur
     */
    public function deleteReadNotifications(User $user): int
    {
        return $user->readNotifications()->delete();
    }

    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function getUnreadNotifications(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir toutes les notifications d'un utilisateur
     */
    public function getAllNotifications(User $user, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir le nombre de notifications non lues par type
     */
    public function getUnreadCountByType(User $user): array
    {
        return $user->unreadNotifications()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
} 