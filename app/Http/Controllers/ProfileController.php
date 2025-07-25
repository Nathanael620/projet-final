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

    // Le reste du code reste identique...

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
            $this->deleteUserData($user);
            Auth::logout();
            $user->delete();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')->with('success', 'Votre compte a été supprimé avec succès. Toutes vos données ont été définitivement effacées.');
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['userDeletion' => 'Une erreur est survenue lors de la suppression du compte. Veuillez réessayer.']);
        }
    }

    private function deleteUserData(User $user): void
    {
        $avatarService = app(AvatarService::class);
        $avatarService->deleteAvatar($user);

        $user->studentSessions()->delete();
        $user->tutorSessions()->delete();
        $user->sentMessages()->delete();
        $user->receivedMessages()->delete();
        $user->givenFeedbacks()->delete();
        $user->receivedFeedbacks()->delete();
        $user->faqs()->delete();

        if (cache()->has("user_sessions_{$user->id}")) {
            cache()->forget("user_sessions_{$user->id}");
        }
    }

    public function logoutAllDevices(Request $request): RedirectResponse
    {
        $user = $request->user();
        $currentSessionId = $request->session()->getId();
        UserSession::deactivateAllUserSessions($user->id, $currentSessionId);
        $user->tokens()->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('login')->with('success', 'Vous avez été déconnecté de tous vos appareils.');
    }

    public function sessions(Request $request): View
    {
        $user = $request->user();

        try {
            $sessions = $user->sessions()->orderBy('created_at', 'desc')->paginate(10);
            $activeSessionsCount = $user->activeSessions()->count();
            $totalSessionsCount = $user->sessions()->count();

            return view('profile.sessions', compact('sessions', 'activeSessionsCount', 'totalSessionsCount'));
        } catch (\Exception $e) {
            \Log::error('Error in sessions method: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            abort(500, 'Erreur lors du chargement des sessions: ' . $e->getMessage());
        }
    }

    public function logoutSession(Request $request, $sessionId): RedirectResponse
    {
        $user = $request->user();
        $userSession = $user->sessions()->where('session_id', $sessionId)->first();

        if (!$userSession) {
            return Redirect::back()->withErrors(['session' => 'Session non trouvée.']);
        }

        UserSession::deactivateSession($sessionId);

        if ($sessionId === $request->session()->getId()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return Redirect::route('login')->with('success', 'Vous avez été déconnecté de cet appareil.');
        }

        return Redirect::back()->with('success', 'Session déconnectée avec succès.');
    }

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

    public function show(User $user): View
    {
        if (!$user->is_public_profile && $user->id !== auth()->id()) {
            abort(403, 'Ce profil est privé.');
        }

        return view('profile.show', compact('user'));
    }
}
