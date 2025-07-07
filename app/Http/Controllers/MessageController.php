<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
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
                ];
            })
            ->sortByDesc('last_message_time');
        
        return view('messages.index', compact('conversations'));
    }

    public function show(User $otherUser): View
    {
        $user = auth()->user();
        
        // Debug: vérifier que $otherUser est bien résolu
        \Log::info('MessageController@show', [
            'otherUser_id' => $otherUser->id,
            'otherUser_name' => $otherUser->name,
            'current_user_id' => $user->id
        ]);
        
        // Récupérer tous les messages entre les deux utilisateurs
        $messages = Message::where(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $otherUser->id);
            })
            ->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $otherUser->id)
                      ->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Marquer les messages comme lus
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return view('messages.show', compact('messages', 'otherUser'));
    }

    public function store(Request $request, User $otherUser): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'nullable|in:text,file,image',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageData = [
            'sender_id' => auth()->id(),
            'receiver_id' => $otherUser->id,
            'content' => $request->content,
            'type' => $request->type ?? 'text',
        ];

        // Gestion des fichiers (à implémenter plus tard)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('messages', $fileName, 'public');
            $messageData['file_path'] = $filePath;
            $messageData['type'] = $file->getMimeType() === 'image/*' ? 'image' : 'file';
        }

        Message::create($messageData);

        return redirect()->route('messages.show', $otherUser)
            ->with('success', 'Message envoyé avec succès !');
    }
}
