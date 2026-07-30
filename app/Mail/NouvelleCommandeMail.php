<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notification envoyée à l'administrateur de la boutique à chaque nouvelle
 * commande passée depuis le front-office.
 */
class NouvelleCommandeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Commande $commande)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvelle commande {$this->commande->numero} — NUBELLE Cosmetics",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.nouvelle-commande');
    }
}
