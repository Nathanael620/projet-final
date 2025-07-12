<?php

namespace App\Mail;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SessionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Session $session;
    public string $role; // 'tutor' ou 'student'
    public string $meetLink;

    public function __construct(Session $session, string $role)
    {
        $this->session = $session;
        $this->role = $role;
        
        // Générer un lien Meet unique pour les séances en ligne
        if ($session->type === 'online') {
            $this->meetLink = $this->generateMeetLink();
        }
    }

    public function envelope(): Envelope
    {
        $participantName = $this->role === 'tutor' ? $this->session->tutor->name : $this->session->student->name;
        
        return new Envelope(
            subject: "Rappel : Séance dans 10 minutes - {$this->session->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.session-reminder',
            with: [
                'session' => $this->session,
                'role' => $this->role,
                'meetLink' => $this->meetLink ?? null,
                'participantName' => $this->role === 'tutor' ? $this->session->tutor->name : $this->session->student->name,
                'otherParticipantName' => $this->role === 'tutor' ? $this->session->student->name : $this->session->tutor->name,
            ],
        );
    }

    private function generateMeetLink(): string
    {
        // Générer un lien Meet basé sur l'ID de la séance et un timestamp
        $sessionId = $this->session->id;
        $timestamp = now()->timestamp;
        $hash = Str::random(8);
        
        // Format : https://meet.google.com/xxx-xxxx-xxx
        $meetCode = Str::upper(Str::random(3)) . '-' . Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(3));
        
        return "https://meet.google.com/{$meetCode}";
    }
} 