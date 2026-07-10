<?php

namespace App\Mail;

use App\Models\Gynecologue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompteGynecologueCreeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Gynecologue $gynecologue,
        public readonly string $motDePasseTemporaire,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur YOONU MAKK — Vos identifiants de connexion',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gynecologue.compte_cree',
        );
    }

}
