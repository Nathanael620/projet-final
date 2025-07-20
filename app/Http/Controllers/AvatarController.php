<?php

namespace App\Http\Controllers;

use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    protected $avatarService;

    public function __construct(AvatarService $avatarService)
    {
        $this->avatarService = $avatarService;
    }

    /**
     * Upload user avatar
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = Auth::user();
            $file = $request->file('avatar');

            // Validation avec le service
            $errors = $this->avatarService->validateAvatar($file);
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $errors
                ], 422);
            }

            // Upload simple sans redimensionnement pour commencer
            $avatarPath = $this->uploadSimpleAvatar($user, $file);
            
            // Mise à jour de l'utilisateur
            $user->update(['avatar' => $avatarPath]);

            return response()->json([
                'success' => true,
                'avatar_url' => Storage::url($avatarPath),
                'message' => 'Avatar mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar upload error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement de l\'image. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Upload simple sans redimensionnement
     */
    private function uploadSimpleAvatar($user, $file): string
    {
        // Supprimer l'ancien avatar s'il existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Générer un nom unique pour le fichier
        $extension = $file->getClientOriginalExtension();
        $filename = $user->id . '_' . time() . '.' . $extension;
        $path = 'avatars/' . $filename;

        // Sauvegarder l'image directement
        Storage::disk('public')->putFileAs('avatars', $file, $filename);

        return $path;
    }

    /**
     * Remove user avatar
     */
    public function remove()
    {
        try {
            $user = Auth::user();

            // Supprimer l'avatar du stockage
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Mettre à jour l'utilisateur
            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'default_avatar_url' => $this->avatarService->getDefaultAvatarUrl($user),
                'message' => 'Avatar supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'avatar'
            ], 500);
        }
    }

    /**
     * Get user avatar URL
     */
    public function getAvatar(Request $request, $userId = null)
    {
        try {
            $user = $userId ? \App\Models\User::find($userId) : Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            $avatarUrl = $user->avatar ? Storage::url($user->avatar) : $this->avatarService->getDefaultAvatarUrl($user);

            return response()->json([
                'success' => true,
                'avatar_url' => $avatarUrl,
                'has_avatar' => !empty($user->avatar)
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar get error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'avatar'
            ], 500);
        }
    }

    /**
     * Crop and resize avatar (simplified)
     */
    public function crop(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Fonction de recadrage temporairement désactivée'
        ], 400);
    }
} 