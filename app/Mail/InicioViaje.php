<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InicioViaje extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
   
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('CORREO_NO_REPLY'), env('NOMBRE_CORREOS')),
            subject: 'Inicio Viaje',
        );
    }

   
    public function content(): Content
    {
        return new Content(
            view: 'emails_viajes.email_inicio_viaje',
            with: ['token' => $this->token],
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
