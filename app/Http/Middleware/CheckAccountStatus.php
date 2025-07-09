<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
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

            // Vérifier si le compte est désactivé
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur pour plus d\'informations.'
                ]);
            }

            // Vérifier si le compte a été supprimé
            if ($user->deleted_at) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Ce compte n\'existe plus.'
                ]);
            }

            // Mettre à jour la dernière activité
            $user->update([
                'last_activity_at' => now(),
                'last_ip' => $request->ip(),
                'last_user_agent' => $request->header('User-Agent'),
            ]);
        }

        return $next($request);
    }
} 