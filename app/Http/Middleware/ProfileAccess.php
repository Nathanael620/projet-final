<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->route('user');
        
        // Si l'utilisateur n'existe pas
        if (!$user) {
            abort(404, 'Utilisateur non trouvé.');
        }

        // Si l'utilisateur connecté essaie d'accéder à son propre profil
        if (auth()->check() && auth()->id() === $user->id) {
            return $next($request);
        }

        // Si le profil est public, autoriser l'accès
        if ($user->is_public_profile) {
            return $next($request);
        }

        // Si l'utilisateur connecté est admin, autoriser l'accès
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Si l'utilisateur connecté a une relation avec le profil (séances en commun, messages, etc.)
        if (auth()->check()) {
            $authUser = auth()->user();
            
            // Vérifier s'il y a des séances en commun
            $commonSessions = $authUser->isTutor() 
                ? $authUser->tutorSessions()->where('student_id', $user->id)->exists()
                : $authUser->studentSessions()->where('tutor_id', $user->id)->exists();
                
            if ($commonSessions) {
                return $next($request);
            }
            
            // Vérifier s'il y a des messages en commun
            $commonMessages = $authUser->sentMessages()->where('receiver_id', $user->id)->exists() ||
                             $authUser->receivedMessages()->where('sender_id', $user->id)->exists();
                             
            if ($commonMessages) {
                return $next($request);
            }
        }

        // Accès refusé
        abort(403, 'Ce profil est privé et vous n\'avez pas les permissions nécessaires pour y accéder.');
    }
} 