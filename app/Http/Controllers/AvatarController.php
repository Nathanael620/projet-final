<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AvatarController extends Controller
{
    /**
     * Upload user avatar
     */
    public function upload(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Supprimer l'ancien avatar s'il existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Sauvegarder le nouvel avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'avatar_url' => Storage::url($avatarPath),
            'message' => 'Avatar mis à jour avec succès'
        ]);
    }

    /**
     * Remove user avatar
     */
    public function remove()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar supprimé avec succès'
        ]);
    }
} 