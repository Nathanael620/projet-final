<?php

namespace App\Services;

use App\Models\User;
use App\Models\Session;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Envoyer une notification push pour la création d'une séance
     */
    public function notifySessionCreated(Session $session): void
    {
        try {
            // Notification pour l'étudiant
            $this->notificationService->createNotification($session->student, 'session_created_student', [
                'title' => 'Séance créée avec succès',
                'message' => "Votre demande de séance avec {$session->tutor->name} a été créée et est en attente de validation.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'tutor_name' => $session->tutor->name,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-calendar-plus',
                'color' => 'success',
                'action_text' => 'Voir la séance',
                'action_url' => route('sessions.show', $session),
            ]);

            // Notification pour le tuteur
            $this->notificationService->createNotification($session->tutor, 'session_created_tutor', [
                'title' => 'Nouvelle demande de séance',
                'message' => "{$session->student->name} vous a demandé une séance de {$session->subject}.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'student_name' => $session->student->name,
                'subject' => $session->subject,
                'level' => $session->level,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'duration' => $session->duration_minutes,
                'price' => $session->price,
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-calendar-check',
                'color' => 'info',
                'action_text' => 'Répondre à la demande',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notifications push envoyées pour la séance créée', [
                'session_id' => $session->id,
                'student_id' => $session->student_id,
                'tutor_id' => $session->tutor_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications push pour la séance créée', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification push pour l'acceptation d'une séance
     */
    public function notifySessionAccepted(Session $session): void
    {
        try {
            // Notification pour l'étudiant
            $this->notificationService->createNotification($session->student, 'session_accepted', [
                'title' => 'Séance acceptée',
                'message' => "{$session->tutor->name} a accepté votre demande de séance.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'tutor_name' => $session->tutor->name,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'action_text' => 'Voir les détails',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notification push envoyée pour la séance acceptée', [
                'session_id' => $session->id,
                'student_id' => $session->student_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification push pour la séance acceptée', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification push pour le refus d'une séance
     */
    public function notifySessionRejected(Session $session): void
    {
        try {
            // Notification pour l'étudiant
            $this->notificationService->createNotification($session->student, 'session_rejected', [
                'title' => 'Séance refusée',
                'message' => "{$session->tutor->name} a refusé votre demande de séance.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'tutor_name' => $session->tutor->name,
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
                'action_text' => 'Voir les détails',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notification push envoyée pour la séance refusée', [
                'session_id' => $session->id,
                'student_id' => $session->student_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification push pour la séance refusée', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification push pour l'annulation d'une séance
     */
    public function notifySessionCancelled(Session $session, User $cancelledBy): void
    {
        try {
            $recipient = $cancelledBy->id === $session->student_id ? $session->tutor : $session->student;
            $cancelledByName = $cancelledBy->name;

            $this->notificationService->createNotification($recipient, 'session_cancelled', [
                'title' => 'Séance annulée',
                'message' => "{$cancelledByName} a annulé la séance prévue.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'cancelled_by' => $cancelledByName,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-calendar-times',
                'color' => 'warning',
                'action_text' => 'Voir les détails',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notification push envoyée pour la séance annulée', [
                'session_id' => $session->id,
                'cancelled_by' => $cancelledBy->id,
                'recipient_id' => $recipient->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification push pour la séance annulée', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification push pour le rappel de séance
     */
    public function notifySessionReminder(Session $session): void
    {
        try {
            // Notification pour l'étudiant
            $this->notificationService->createNotification($session->student, 'session_reminder_student', [
                'title' => 'Rappel de séance',
                'message' => "Votre séance avec {$session->tutor->name} commence dans 1 heure.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'tutor_name' => $session->tutor->name,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-bell',
                'color' => 'warning',
                'action_text' => 'Voir la séance',
                'action_url' => route('sessions.show', $session),
            ]);

            // Notification pour le tuteur
            $this->notificationService->createNotification($session->tutor, 'session_reminder_tutor', [
                'title' => 'Rappel de séance',
                'message' => "Votre séance avec {$session->student->name} commence dans 1 heure.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'student_name' => $session->student->name,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-bell',
                'color' => 'warning',
                'action_text' => 'Voir la séance',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notifications push de rappel envoyées', [
                'session_id' => $session->id,
                'student_id' => $session->student_id,
                'tutor_id' => $session->tutor_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications push de rappel', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification push pour la modification d'une séance
     */
    public function notifySessionUpdated(Session $session, User $updatedBy): void
    {
        try {
            $recipient = $updatedBy->id === $session->student_id ? $session->tutor : $session->student;
            $updatedByName = $updatedBy->name;

            $this->notificationService->createNotification($recipient, 'session_updated', [
                'title' => 'Séance modifiée',
                'message' => "{$updatedByName} a modifié les détails de la séance.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'updated_by' => $updatedByName,
                'scheduled_at' => $session->scheduled_at->format('d/m/Y H:i'),
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-edit',
                'color' => 'info',
                'action_text' => 'Voir les modifications',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notification push envoyée pour la séance modifiée', [
                'session_id' => $session->id,
                'updated_by' => $updatedBy->id,
                'recipient_id' => $recipient->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification push pour la séance modifiée', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification push pour la fin d'une séance
     */
    public function notifySessionCompleted(Session $session): void
    {
        try {
            // Notification pour l'étudiant
            $this->notificationService->createNotification($session->student, 'session_completed_student', [
                'title' => 'Séance terminée',
                'message' => "Votre séance avec {$session->tutor->name} est terminée. N'oubliez pas de laisser un avis !",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'tutor_name' => $session->tutor->name,
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-check-double',
                'color' => 'success',
                'action_text' => 'Laisser un avis',
                'action_url' => route('feedback.create', ['session_id' => $session->id]),
            ]);

            // Notification pour le tuteur
            $this->notificationService->createNotification($session->tutor, 'session_completed_tutor', [
                'title' => 'Séance terminée',
                'message' => "Votre séance avec {$session->student->name} est terminée.",
                'session_id' => $session->id,
                'session_title' => $session->title,
                'student_name' => $session->student->name,
                'session_url' => route('sessions.show', $session),
                'icon' => 'fas fa-check-double',
                'color' => 'success',
                'action_text' => 'Voir la séance',
                'action_url' => route('sessions.show', $session),
            ]);

            Log::info('Notifications push envoyées pour la séance terminée', [
                'session_id' => $session->id,
                'student_id' => $session->student_id,
                'tutor_id' => $session->tutor_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications push pour la séance terminée', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
} 