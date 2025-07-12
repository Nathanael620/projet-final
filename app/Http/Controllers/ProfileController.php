<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\UserSession;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Statistiques pour le profil
        $stats = [
            'total_sessions' => $user->isTutor() 
                ? $user->tutorSessions()->count() 
                : $user->studentSessions()->count(),
            'completed_sessions' => $user->isTutor() 
                ? $user->tutorSessions()->where('status', 'completed')->count() 
                : $user->studentSessions()->where('status', 'completed')->count(),
            'total_earnings' => $user->isTutor() 
                ? $user->tutorSessions()->where('status', 'completed')->sum('price') 
                : 0,
            'total_spent' => $user->isStudent() 
                ? $user->studentSessions()->where('status', 'completed')->sum('price') 
                : 0,
            'unread_messages' => $user->getUnreadMessagesCount(),
        ];
        
        return view('profile.edit', compact('user', 'stats'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            $avatarService = app(AvatarService::class);
            $data['avatar'] = $avatarService->uploadAvatar($user, $request->file('avatar'));
        }

        // Gestion des compétences (pour les tuteurs)
        if ($request->has('skills') && is_array($request->skills)) {
            $data['skills'] = array_filter($request->skills); // Supprimer les valeurs vides
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Update user availability (for tutors).
     */
    public function updateAvailability(Request $request): RedirectResponse
    {
        $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->update(['is_available' => $request->is_available]);

        $status = $request->is_available ? 'disponible' : 'indisponible';
        return Redirect::route('profile.edit')->with('success', "Vous êtes maintenant {$status}.");
    }

    /**
     * Update user hourly rate (for tutors).
     */
    public function updateHourlyRate(Request $request): RedirectResponse
    {
        $request->validate([
            'hourly_rate' => 'required|numeric|min:5|max:200',
        ]);

        $user = $request->user();
        $user->update(['hourly_rate' => $request->hourly_rate]);

        return Redirect::route('profile.edit')->with('success', 'Tarif horaire mis à jour avec succès !');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            // Supprimer toutes les données associées
            $this->deleteUserData($user);

            // Déconnecter l'utilisateur
            Auth::logout();

            // Supprimer le compte (soft delete)
            $user->delete();

            // Invalider la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')->with('success', 'Votre compte a été supprimé avec succès. Toutes vos données ont été définitivement effacées.');

        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['userDeletion' => 'Une erreur est survenue lors de la suppression du compte. Veuillez réessayer.']);
        }
    }

    /**
     * Delete all user data before account deletion
     */
    private function deleteUserData(User $user): void
    {
        // Supprimer l'avatar
        $avatarService = app(AvatarService::class);
        $avatarService->deleteAvatar($user);

        // Supprimer les séances
        $user->studentSessions()->delete();
        $user->tutorSessions()->delete();

        // Supprimer les messages
        $user->sentMessages()->delete();
        $user->receivedMessages()->delete();

        // Supprimer les feedbacks
        $user->givenFeedbacks()->delete();
        $user->receivedFeedbacks()->delete();

        // Supprimer les FAQs créées
        $user->faqs()->delete();

        // Nettoyer les sessions en cache
        if (cache()->has("user_sessions_{$user->id}")) {
            cache()->forget("user_sessions_{$user->id}");
        }
    }

    /**
     * Logout user from all devices
     */
    public function logoutAllDevices(Request $request): RedirectResponse
    {
        $user = $request->user();
        $currentSessionId = $request->session()->getId();

        // Déconnecter toutes les sessions de l'utilisateur sauf la session actuelle
        UserSession::deactivateAllUserSessions($user->id, $currentSessionId);

        // Invalider tous les tokens de l'utilisateur
        $user->tokens()->delete();

        // Déconnecter de la session actuelle
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('login')->with('success', 'Vous avez été déconnecté de tous vos appareils.');
    }

    /**
     * Show user sessions management
     */
    public function sessions(Request $request): View
    {
        $user = $request->user();
        
        try {
            $sessions = $user->sessions()
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $activeSessionsCount = $user->activeSessions()->count();
            $totalSessionsCount = $user->sessions()->count();

            return view('profile.sessions', compact('sessions', 'activeSessionsCount', 'totalSessionsCount'));
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Error in sessions method: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Retourner une vue d'erreur ou rediriger
            abort(500, 'Erreur lors du chargement des sessions: ' . $e->getMessage());
        }
    }

    /**
     * Logout from specific session
     */
    public function logoutSession(Request $request, $sessionId): RedirectResponse
    {
        $user = $request->user();
        
        // Vérifier que la session appartient à l'utilisateur
        $userSession = $user->sessions()->where('session_id', $sessionId)->first();
        
        if (!$userSession) {
            return Redirect::back()->withErrors(['session' => 'Session non trouvée.']);
        }

        // Déconnecter la session spécifique
        UserSession::deactivateSession($sessionId);

        // Si c'est la session actuelle, déconnecter l'utilisateur
        if ($sessionId === $request->session()->getId()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return Redirect::route('login')->with('success', 'Vous avez été déconnecté de cet appareil.');
        }

        return Redirect::back()->with('success', 'Session déconnectée avec succès.');
    }

    /**
     * Deactivate account temporarily
     */
    public function deactivate(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $user->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

                 return Redirect::to('/')->with('success', 'Votre compte a été désactivé. Vous pouvez le réactiver en vous reconnectant.');
     }

     /**
      * Reactivate account
      */
     public function reactivate(Request $request): RedirectResponse
     {
         $user = User::where('email', $request->email)->first();
         
         if (!$user || !$user->deactivated_at) {
             return Redirect::back()->withErrors(['email' => 'Aucun compte désactivé trouvé avec cet email.']);
         }

         $user->update([
             'is_active' => true,
             'deactivated_at' => null,
             'deactivation_reason' => null,
         ]);

         return Redirect::route('login')->with('success', 'Votre compte a été réactivé. Vous pouvez maintenant vous connecter.');
     }

    /**
     * Show user's public profile.
     */
    public function show(User $user): View
    {
        // Empêcher l'accès aux profils privés
        if (!$user->is_public_profile && $user->id !== auth()->id()) {
            abort(403, 'Ce profil est privé.');
        }

        return view('profile.show', compact('user'));
    }
}
