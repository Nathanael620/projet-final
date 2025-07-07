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
        
        // Statistiques de base (à remplacer par de vraies données plus tard)
        $stats = [
            'total_sessions' => $user->total_sessions ?? 0,
            'rating' => $user->rating ?? 0,
            'completed_sessions' => 0, // À implémenter avec les modèles Session
            'upcoming_sessions' => 0, // À implémenter avec les modèles Session
            'total_earnings' => 0, // Pour les tuteurs
            'total_spent' => 0, // Pour les étudiants
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
