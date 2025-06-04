<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Str;


class CotizacionPdf extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $url;

    public $token;



    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotizacion Pdf',
            from: new Address(config('mail.from.address'), config('mail.from.name'))
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'comercial_emails.cotizacion',
            with: ['token' => $this->token]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $response = Http::get($this->url);
        if (!$response->successful()) {
            \Log::error('No se pudo descargar el PDF desde la URL: ' . $this->url);
            return [];
        }

        // Paso 2: Guardar temporalmente
        $tempPath = storage_path('app/temp_' . Str::random(10) . '.pdf');
        file_put_contents($tempPath, $response->body());

        // Paso 3: Adjuntar
        return [
            Attachment::fromPath($tempPath)
                ->as('cotizacion.pdf')
                ->withMime('application/pdf'),
        ];

    }
}
