<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class MessageController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(): View
    {
        $user = auth()->user();
        
        // Récupérer les conversations (utilisateurs avec qui on a échangé)
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
            ->sortByDesc('last_message_time');
        
        // Récupérer tous les utilisateurs pour permettre de nouvelles conversations
        $allUsers = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
        
        // Filtrer les utilisateurs avec qui on n'a pas encore de conversation
        $newContacts = $allUsers->filter(function ($potentialUser) use ($conversations) {
            return !$conversations->has($potentialUser->id);
        });
        
        // Statistiques
        $stats = [
            'total_conversations' => $conversations->count(),
            'unread_messages' => $user->getUnreadMessagesCount(),
            'tutors_contacted' => $conversations->where('user.role', 'tutor')->count(),
            'students_contacted' => $conversations->where('user.role', 'student')->count(),
            'new_contacts_available' => $newContacts->count(),
        ];
        
        return view('messages.index', compact('conversations', 'newContacts', 'stats'));
    }

    public function show(User $otherUser): View
    {
        $user = auth()->user();
        
        // Récupérer tous les messages entre les deux utilisateurs (limité aux 50 derniers pour la performance)
        $messages = Message::where(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $otherUser->id);
            })
            ->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $otherUser->id)
                      ->where('receiver_id', $user->id);
            })
            ->with(['sender:id,name,role', 'receiver:id,name,role'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse(); // Remettre dans l'ordre chronologique
        
        // Marquer les messages comme lus (en arrière-plan pour ne pas ralentir l'affichage)
        if ($messages->where('sender_id', $otherUser->id)->where('is_read', false)->count() > 0) {
            Message::where('sender_id', $otherUser->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }
        
        // Récupérer les séances en commun (limitées aux 5 dernières)
        $commonSessions = collect();
        if ($user->isTutor() && $otherUser->isStudent()) {
            $commonSessions = Session::where('tutor_id', $user->id)
                ->where('student_id', $otherUser->id)
                ->orderBy('scheduled_at', 'desc')
                ->limit(5)
                ->get();
        } elseif ($user->isStudent() && $otherUser->isTutor()) {
            $commonSessions = Session::where('tutor_id', $otherUser->id)
                ->where('student_id', $user->id)
                ->orderBy('scheduled_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('messages.show', compact('messages', 'otherUser', 'commonSessions'));
    }

    public function store(Request $request, User $otherUser)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt'
        ], [
            'content.max' => 'Le message ne peut pas dépasser 1000 caractères.',
            'file.max' => 'Le fichier ne peut pas dépasser 10MB.',
            'file.mimes' => 'Type de fichier non autorisé.'
        ]);

        // Vérifier qu'au moins le contenu ou un fichier est fourni
        if (empty($request->content) && !$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez saisir un message ou sélectionner un fichier.'
            ], 422);
        }

        $user = auth()->user();

        // Déterminer le type de message
        $messageType = 'text';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $messageType = 'image';
            } else {
                $messageType = 'file';
            }
        }

        // Créer le message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'content' => $request->content,
            'type' => $messageType,
            'file_path' => null,
        ]);

        // Gérer le fichier si fourni
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('messages', $fileName, 'public');
            
            $message->update([
                'file_path' => $filePath,
            ]);
        }

        // Créer une notification pour le destinataire
        try {
            $this->notificationService->notifyNewMessage($otherUser, $user, $request->content ?? 'Fichier partagé');
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas faire échouer l'envoi du message
            \Log::error('Erreur lors de la création de notification: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
                'html' => view('messages.partials.message', ['message' => $message->load('sender')])->render()
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé avec succès.');
    }

    /**
     * Récupérer les nouveaux messages via AJAX
     */
    public function getNewMessages(Request $request, User $otherUser): JsonResponse
    {
        $user = auth()->user();
        $lastMessageId = $request->input('last_message_id', 0);
        
        $messages = Message::where(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $otherUser->id);
            })
            ->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $otherUser->id)
                      ->where('receiver_id', $user->id);
            })
            ->where('id', '>', $lastMessageId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Marquer comme lus
        if ($messages->count() > 0) {
            Message::where('sender_id', $otherUser->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }
        
        $html = '';
        foreach ($messages as $message) {
            $html .= view('messages.partials.message', compact('message'))->render();
        }
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'html' => $html,
            'count' => $messages->count()
        ]);
    }

    /**
     * Marquer une conversation comme lue
     */
    public function markAsRead(User $otherUser): JsonResponse
    {
        $user = auth()->user();
        
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'unread_count' => $user->fresh()->getUnreadMessagesCount()
        ]);
    }

    /**
     * Rechercher dans les conversations
     */
    public function search(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = $request->input('query', '');
        
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Requête vide']);
        }
        
        $messages = Message::where(function ($q) use ($user, $query) {
                $q->where('sender_id', $user->id)
                  ->where('content', 'like', "%{$query}%");
            })
            ->orWhere(function ($q) use ($user, $query) {
                $q->where('receiver_id', $user->id)
                  ->where('content', 'like', "%{$query}%");
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        $results = $messages->map(function ($message) use ($user) {
            $otherUser = $message->sender_id === $user->id ? $message->receiver : $message->sender;
            return [
                'message' => $message,
                'other_user' => $otherUser,
                'is_sent_by_me' => $message->sender_id === $user->id,
                'highlighted_content' => $this->highlightSearchTerm($message->content, request('query'))
            ];
        });
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Rechercher des utilisateurs pour commencer une conversation
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = $request->input('query', '');
        $role = $request->input('role', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json(['success' => false, 'message' => 'Requête trop courte']);
        }
        
        $usersQuery = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('skills', 'like', "%{$query}%");
            });
        
        // Filtrer par rôle si spécifié
        if (!empty($role) && in_array($role, ['tutor', 'student'])) {
            $usersQuery->where('role', $role);
        }
        
        $users = $usersQuery->select('id', 'name', 'email', 'role', 'skills', 'rating', 'total_sessions')
            ->orderBy('name')
            ->limit(10)
            ->get();
        
        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'skills' => $user->skills,
                'rating' => $user->rating,
                'total_sessions' => $user->total_sessions,
                'avatar_url' => null, // Pas d'avatar pour le moment
                'skills_string' => $user->getSkillsString(),
                'rating_stars' => $user->getRatingStars(),
                'conversation_url' => route('messages.show', $user->id)
            ];
        });
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Modifier un message
     */
    public function update(Request $request, Message $message): JsonResponse
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est le propriétaire du message
        if ($message->sender_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier ce message'
            ], 403);
        }
        
        // Vérifier que le message n'a pas plus de 5 minutes
        if ($message->created_at->diffInMinutes(now()) > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez modifier un message que dans les 5 premières minutes'
            ], 403);
        }
        
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $message->update([
            'content' => $request->content,
            'is_edited' => true,
            'edited_at' => now()
        ]);
        
        $message->load(['sender', 'receiver']);
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'html' => view('messages.partials.message', compact('message'))->render()
        ]);
    }

    /**
     * Supprimer un message
     */
    public function destroy(Message $message): JsonResponse
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est le propriétaire du message
        if ($message->sender_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer ce message'
            ], 403);
        }
        
        // Supprimer le fichier associé s'il existe
        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }
        
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Message supprimé avec succès'
        ]);
    }

    /**
     * Télécharger un fichier
     */
    public function downloadFile(Message $message): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        if (!$message->file_path || !Storage::disk('public')->exists($message->file_path)) {
            abort(404, 'Fichier non trouvé');
        }
        
        $fileName = basename($message->file_path);
        
        return Storage::disk('public')->download($message->file_path, $fileName);
    }

    /**
     * Obtenir les statistiques des messages
     */
    public function getStats(): JsonResponse
    {
        $user = auth()->user();
        
        $stats = [
            'total_conversations' => $user->sentMessages->merge($user->receivedMessages)
                ->groupBy(function ($message) use ($user) {
                    return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
                })->count(),
            'unread_messages' => $user->getUnreadMessagesCount(),
            'total_messages_sent' => $user->sentMessages->count(),
            'total_messages_received' => $user->receivedMessages->count(),
            'messages_this_month' => $user->sentMessages->where('created_at', '>=', now()->startOfMonth())->count() +
                                   $user->receivedMessages->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Mettre en surbrillance les termes de recherche
     */
    private function highlightSearchTerm(string $content, string $query): string
    {
        $highlighted = preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark>$1</mark>',
            $content
        );
        
        return $highlighted;
    }
}
