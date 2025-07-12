<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Console\Commands\SendSessionReminders;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function users(): View
    {
        return view('admin.users');
    }

    public function sessions(): View
    {
        return view('admin.sessions');
    }

    public function sessionReminders(): View
    {
        // Récupérer les séances qui nécessitent des rappels
        $now = Carbon::now();
        $tenMinutesFromNow = $now->copy()->addMinutes(10);
        
        $upcomingSessions = Session::where('status', 'confirmed')
            ->whereBetween('scheduled_at', [$now, $tenMinutesFromNow])
            ->where('reminder_sent', false)
            ->with(['tutor', 'student'])
            ->orderBy('scheduled_at')
            ->get();

        $recentSessions = Session::where('status', 'confirmed')
            ->where('scheduled_at', '>=', $now->copy()->subHours(24))
            ->where('reminder_sent', true)
            ->with(['tutor', 'student'])
            ->orderBy('scheduled_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.session-reminders', compact('upcomingSessions', 'recentSessions'));
    }

    public function sendReminders(): JsonResponse
    {
        try {
            $command = new SendSessionReminders();
            $command->handle();
            
            return response()->json([
                'success' => true,
                'message' => 'Rappels envoyés avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des rappels: ' . $e->getMessage()
            ], 500);
        }
    }
}
