<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeTuteurMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tuteur;
    public $isAccepted;

    /**
     * Create a new message instance.
     */
    public function __construct($tuteur, $isAccepted)
    {
        $this->tuteur = $tuteur;
        $this->isAccepted = $isAccepted;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAccepted ? ' Votre demande de tutorat a été acceptée' : "Mise à jour de l'état d'avancement de la candidature de votre tuteur",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->isAccepted ? 'emails.tuteur-accepted' : 'emails.tuteur-rejected',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
