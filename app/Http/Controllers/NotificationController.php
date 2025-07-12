<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher la liste des notifications
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $user = auth()->user();
        $notifications = $this->notificationService->getAllNotifications($user, 50);
        
        $unreadCount = $user->unreadNotifications()->count();
        $unreadCountByType = $this->notificationService->getUnreadCountByType($user);

        return view('notifications.index', compact('notifications', 'unreadCount', 'unreadCountByType'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        // Vérifier que l'utilisateur est propriétaire de la notification
        if ($notification->notifiable_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = auth()->user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues',
            'unread_count' => 0
        ]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        // Vérifier que l'utilisateur est propriétaire de la notification
        if ($notification->notifiable_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $this->notificationService->deleteNotification($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée',
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Supprimer toutes les notifications lues
     */
    public function deleteRead(Request $request): JsonResponse
    {
        $user = auth()->user();
        $deletedCount = $this->notificationService->deleteReadNotifications($user);

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} notifications lues supprimées",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Obtenir les notifications non lues (pour AJAX)
     */
    public function getUnread(Request $request): JsonResponse
    {
        $user = auth()->user();
        $notifications = $this->notificationService->getUnreadNotifications($user, 10);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'uuid' => $notification->uuid,
                    'type' => $notification->type,
                    'title' => $notification->data['title'] ?? '',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'fas fa-bell',
                    'color' => $notification->data['color'] ?? 'primary',
                    'url' => $notification->data['conversation_url'] ?? $notification->data['session_url'] ?? null,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_at_full' => $notification->created_at->format('d/m/Y H:i'),
                ];
            }),
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Obtenir les statistiques des notifications
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications()->count();
        $unreadCountByType = $this->notificationService->getUnreadCountByType($user);

        return response()->json([
            'unread_count' => $unreadCount,
            'unread_by_type' => $unreadCountByType,
            'total_notifications' => $user->notifications()->count()
        ]);
    }
} 