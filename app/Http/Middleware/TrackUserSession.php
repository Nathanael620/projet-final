<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = $request->session()->getId();
            
            // Vérifier si la session existe déjà
            $userSession = UserSession::where('session_id', $sessionId)->first();
            
            if (!$userSession) {
                // Créer une nouvelle session
                UserSession::createFromRequest($user, $sessionId);
            } else {
                // Mettre à jour l'activité de la session existante
                UserSession::updateLastActivity($sessionId);
            }
        }
        
        return $next($request);
    }
}
