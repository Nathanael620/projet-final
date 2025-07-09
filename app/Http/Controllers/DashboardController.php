<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isTutor()) {
            $stats = [
                'total_sessions' => $user->tutorSessions()->count(),
                'rating' => $user->rating ?? 0,
                'completed_sessions' => $user->tutorSessions()->where('status', 'completed')->count(),
                'upcoming_sessions' => $user->tutorSessions()->where('status', 'accepted')->where('scheduled_at', '>', now())->count(),
                'total_earnings' => $user->tutorSessions()->where('status', 'completed')->sum('price'),
                'pending_requests' => $user->tutorSessions()->where('status', 'pending')->count(),
            ];
        } else {
            $stats = [
                'total_sessions' => $user->studentSessions()->count(),
                'rating' => $user->rating ?? 0,
                'completed_sessions' => $user->studentSessions()->where('status', 'completed')->count(),
                'upcoming_sessions' => $user->studentSessions()->where('status', 'accepted')->where('scheduled_at', '>', now())->count(),
                'total_spent' => $user->studentSessions()->where('status', 'completed')->sum('price'),
                'pending_requests' => $user->studentSessions()->where('status', 'pending')->count(),
            ];
        }

        // Séances récentes
        $recentSessions = $user->isTutor() 
            ? $user->tutorSessions()->with(['student'])->latest()->take(5)->get()
            : $user->studentSessions()->with(['tutor'])->latest()->take(5)->get();

        return view('dashboard', compact('user', 'stats', 'recentSessions'));
    }
}
