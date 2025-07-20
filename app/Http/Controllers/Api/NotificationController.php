<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
     * Vérifier les nouvelles notifications
     */
    public function checkNewNotifications(Request $request): JsonResponse
    {
        $request->validate([
            'last_check' => 'required|date',
        ]);

        $user = auth()->user();
        $lastCheck = $request->last_check;

        // Récupérer les notifications non lues créées après la dernière vérification
        $notifications = $user->unreadNotifications()
            ->where('created_at', '>', $lastCheck)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $user = auth()->user();

        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->notifiable_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
            ], 404);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
        ]);
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->getUnreadNotificationsCount();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Obtenir toutes les notifications de l'utilisateur
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->get('limit', 20);
        $type = $request->get('type');

        $query = $user->notifications();

        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'total' => $notifications->count(),
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues',
        ]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $user = auth()->user();

        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->notifiable_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
            ], 404);
        }

        $this->notificationService->deleteNotification($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée',
        ]);
    }

    /**
     * Supprimer toutes les notifications lues
     */
    public function deleteRead(): JsonResponse
    {
        $user = auth()->user();
        $deletedCount = $this->notificationService->deleteReadNotifications($user);

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} notifications supprimées",
            'deleted_count' => $deletedCount,
        ]);
    }
} 