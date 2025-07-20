<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedbacks;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Afficher le formulaire de notation pour une séance
     */
    public function create(Session $session): View
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur peut noter cette séance
        if (!$this->canRateSession($user, $session)) {
            abort(403, 'Vous ne pouvez pas noter cette séance.');
        }
        
        // Déterminer qui doit être noté
        $reviewedUser = $user->isTutor() ? $session->student : $session->tutor;
        $feedbackType = $user->isTutor() ? 'tutor_to_student' : 'student_to_tutor';
        
        return view('feedback.create', compact('session', 'reviewedUser', 'feedbackType'));
    }

    /**
     * Enregistrer un nouveau feedback
     */
    public function store(Request $request, Session $session): RedirectResponse
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur peut noter cette séance
        if (!$this->canRateSession($user, $session)) {
            abort(403, 'Vous ne pouvez pas noter cette séance.');
        }
        
        // Validation
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);
        
        // Déterminer qui doit être noté
        $reviewedUser = $user->isTutor() ? $session->student : $session->tutor;
        $feedbackType = $user->isTutor() ? 'tutor_to_student' : 'student_to_tutor';
        
        // Vérifier si un feedback existe déjà
        $existingFeedback = Feedbacks::where('session_id', $session->id)
            ->where('reviewer_id', $user->id)
            ->where('type', $feedbackType)
            ->first();
            
        if ($existingFeedback) {
            return redirect()->back()->withErrors(['error' => 'Vous avez déjà noté cette séance.']);
        }
        
        try {
            DB::beginTransaction();
            
            // Créer le feedback
            $feedback = Feedbacks::create([
                'session_id' => $session->id,
                'reviewer_id' => $user->id,
                'reviewed_id' => $reviewedUser->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'type' => $feedbackType,
                'is_anonymous' => $request->boolean('is_anonymous', false),
            ]);
            
            // Mettre à jour la note moyenne de l'utilisateur noté
            $this->updateUserRating($reviewedUser);
            
            DB::commit();
            
            return redirect()->route('sessions.show', $session)
                ->with('success', 'Votre notation a été enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.']);
        }
    }

    /**
     * Afficher les feedbacks d'un utilisateur
     */
    public function userFeedbacks(User $user): View
    {
        $feedbacks = $user->receivedFeedbacks()
            ->with(['reviewer', 'session'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $stats = [
            'average_rating' => $user->getAverageRating(),
            'total_feedbacks' => $user->receivedFeedbacks()->count(),
            'rating_distribution' => $this->getRatingDistribution($user),
        ];
        
        return view('feedback.user-feedbacks', compact('user', 'feedbacks', 'stats'));
    }

    /**
     * Afficher les feedbacks donnés par l'utilisateur connecté
     */
    public function myFeedbacks(): View
    {
        $user = Auth::user();
        
        $feedbacks = $user->givenFeedbacks()
            ->with(['reviewed', 'session'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('feedback.my-feedbacks', compact('feedbacks'));
    }

    /**
     * Modifier un feedback existant
     */
    public function edit(Feedbacks $feedback): View
    {
        $user = Auth::user();
        
        if ($feedback->reviewer_id !== $user->id) {
            abort(403, 'Vous ne pouvez pas modifier ce feedback.');
        }
        
        // Vérifier que le feedback n'est pas trop ancien (24h)
        if ($feedback->created_at->diffInHours(now()) > 24) {
            abort(403, 'Vous ne pouvez plus modifier ce feedback après 24h.');
        }
        
        return view('feedback.edit', compact('feedback'));
    }

    /**
     * Mettre à jour un feedback
     */
    public function update(Request $request, Feedbacks $feedback): RedirectResponse
    {
        $user = Auth::user();
        
        if ($feedback->reviewer_id !== $user->id) {
            abort(403, 'Vous ne pouvez pas modifier ce feedback.');
        }
        
        // Vérifier que le feedback n'est pas trop ancien (24h)
        if ($feedback->created_at->diffInHours(now()) > 24) {
            abort(403, 'Vous ne pouvez plus modifier ce feedback après 24h.');
        }
        
        // Validation
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
            $oldRating = $feedback->rating;
            
            // Mettre à jour le feedback
            $feedback->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_anonymous' => $request->boolean('is_anonymous', false),
            ]);
            
            // Mettre à jour la note moyenne de l'utilisateur noté
            $this->updateUserRating($feedback->reviewed);
            
            DB::commit();
            
            return redirect()->route('feedback.my-feedbacks')
                ->with('success', 'Votre feedback a été mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }

    /**
     * Supprimer un feedback
     */
    public function destroy(Feedbacks $feedback): RedirectResponse
    {
        $user = Auth::user();
        
        if ($feedback->reviewer_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Vous ne pouvez pas supprimer ce feedback.');
        }
        
        try {
            DB::beginTransaction();
            
            $reviewedUser = $feedback->reviewed;
            
            // Supprimer le feedback
            $feedback->delete();
            
            // Mettre à jour la note moyenne de l'utilisateur noté
            $this->updateUserRating($reviewedUser);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Le feedback a été supprimé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de la suppression.']);
        }
    }

    /**
     * Vérifier si un utilisateur peut noter une séance
     */
    private function canRateSession(User $user, Session $session): bool
    {
        // La séance doit être terminée
        if ($session->status !== 'completed') {
            return false;
        }
        
        // L'utilisateur doit être impliqué dans la séance
        if ($user->isTutor() && $session->tutor_id !== $user->id) {
            return false;
        }
        
        if ($user->isStudent() && $session->student_id !== $user->id) {
            return false;
        }
        
        // Vérifier qu'il n'y a pas déjà un feedback de cet utilisateur
        $feedbackType = $user->isTutor() ? 'tutor_to_student' : 'student_to_tutor';
        $existingFeedback = Feedbacks::where('session_id', $session->id)
            ->where('reviewer_id', $user->id)
            ->where('type', $feedbackType)
            ->exists();
            
        return !$existingFeedback;
    }

    /**
     * Mettre à jour la note moyenne d'un utilisateur
     */
    private function updateUserRating(User $user): void
    {
        $averageRating = $user->getAverageRating();
        $totalSessions = $user->receivedFeedbacks()->count();
        
        $user->update([
            'rating' => $averageRating,
            'total_sessions' => $totalSessions,
        ]);
    }

    /**
     * Obtenir la distribution des notes pour un utilisateur
     */
    private function getRatingDistribution(User $user): array
    {
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $count = $user->receivedFeedbacks()->where('rating', $i)->count();
            $total = $user->receivedFeedbacks()->count();
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        
        return $distribution;
    }
} 