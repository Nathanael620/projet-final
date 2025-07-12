<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageNotificationService
{
    /**
     * Envoyer une notification pour un nouveau message
     */
    public function notifyNewMessage(Message $message): void
    {
        try {
            $receiver = $message->receiver;
            
            // Vérifier si l'utilisateur est en ligne
            if ($this->isUserOnline($receiver)) {
                $this->sendRealTimeNotification($message);
            } else {
                $this->sendEmailNotification($message);
            }
            
            // Mettre à jour les statistiques
            $this->updateMessageStats($receiver);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vérifier si un utilisateur est en ligne
     */
    private function isUserOnline(User $user): bool
    {
        // Vérifier la dernière activité (5 minutes)
        if ($user->last_activity_at && $user->last_activity_at->diffInMinutes(now()) < 5) {
            return true;
        }
        
        return false;
    }

    /**
     * Envoyer une notification en temps réel
     */
    private function sendRealTimeNotification(Message $message): void
    {
        // Ici, vous pourriez intégrer WebSockets, Pusher, ou d'autres services
        // Pour l'instant, on utilise une approche simple avec des événements
        
        event(new \App\Events\NewMessageReceived($message));
        
        Log::info('Notification temps réel envoyée', [
            'message_id' => $message->id,
            'receiver_id' => $message->receiver_id
        ]);
    }

    /**
     * Envoyer une notification par email
     */
    private function sendEmailNotification(Message $message): void
    {
        $receiver = $message->receiver;
        $sender = $message->sender;
        
        // Vérifier les préférences de notification
        if (!$this->shouldSendEmailNotification($receiver)) {
            return;
        }
        
        // Envoyer l'email
        Mail::send('emails.new-message', [
            'message' => $message,
            'sender' => $sender,
            'receiver' => $receiver
        ], function ($mail) use ($receiver, $sender) {
            $mail->to($receiver->email)
                 ->subject("Nouveau message de {$sender->name}")
                 ->from(config('mail.from.address'), config('mail.from.name'));
        });
        
        Log::info('Email de notification envoyé', [
            'message_id' => $message->id,
            'receiver_email' => $receiver->email
        ]);
    }

    /**
     * Vérifier si on doit envoyer une notification email
     */
    private function shouldSendEmailNotification(User $user): bool
    {
        // Vérifier les préférences de l'utilisateur
        // Pour l'instant, on envoie toujours
        return true;
    }

    /**
     * Mettre à jour les statistiques de messages
     */
    private function updateMessageStats(User $user): void
    {
        // Mettre à jour le nombre de messages non lus
        $unreadCount = $user->receivedMessages()
            ->where('is_read', false)
            ->count();
        
        // Ici, vous pourriez stocker ces statistiques dans le cache
        // ou dans une table dédiée pour de meilleures performances
        cache()->put("user_{$user->id}_unread_messages", $unreadCount, 300);
    }

    /**
     * Marquer une conversation comme lue
     */
    public function markConversationAsRead(User $user, User $otherUser): void
    {
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        // Mettre à jour les statistiques
        $this->updateMessageStats($user);
        
        Log::info('Conversation marquée comme lue', [
            'user_id' => $user->id,
            'other_user_id' => $otherUser->id
        ]);
    }

    /**
     * Obtenir les statistiques de messages pour un utilisateur
     */
    public function getUserMessageStats(User $user): array
    {
        $cacheKey = "user_{$user->id}_message_stats";
        
        return cache()->remember($cacheKey, 300, function () use ($user) {
            $sentMessages = $user->sentMessages();
            $receivedMessages = $user->receivedMessages();
            
            return [
                'total_sent' => $sentMessages->count(),
                'total_received' => $receivedMessages->count(),
                'unread_count' => $receivedMessages->where('is_read', false)->count(),
                'conversations_count' => $this->getConversationsCount($user),
                'messages_this_month' => $sentMessages->where('created_at', '>=', now()->startOfMonth())->count() +
                                       $receivedMessages->where('created_at', '>=', now()->startOfMonth())->count(),
            ];
        });
    }

    /**
     * Obtenir le nombre de conversations
     */
    private function getConversationsCount(User $user): int
    {
        $sentUserIds = $user->sentMessages()->distinct()->pluck('receiver_id');
        $receivedUserIds = $user->receivedMessages()->distinct()->pluck('sender_id');
        
        return $sentUserIds->merge($receivedUserIds)->unique()->count();
    }

    /**
     * Nettoyer les anciennes notifications
     */
    public function cleanupOldNotifications(): void
    {
        // Supprimer les messages très anciens (optionnel)
        $cutoffDate = now()->subMonths(6);
        
        $deletedCount = Message::where('created_at', '<', $cutoffDate)
            ->where('is_read', true)
            ->delete();
        
        Log::info('Nettoyage des anciens messages', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate
        ]);
    }

    /**
     * Obtenir les conversations récentes
     */
    public function getRecentConversations(User $user, int $limit = 10): array
    {
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->get()
            ->groupBy(function ($message) use ($user) {
                return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages) use ($user) {
                $otherUser = $messages->first()->sender_id === $user->id 
                    ? $messages->first()->receiver 
                    : $messages->first()->sender;
                
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $unreadCount = $messages->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'last_message_time' => $lastMessage->created_at,
                    'total_messages' => $messages->count(),
                ];
            })
            ->sortByDesc('last_message_time')
            ->take($limit)
            ->values()
            ->toArray();
        
        return $conversations;
    }
} 