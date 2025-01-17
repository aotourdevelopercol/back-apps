<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CancelarViaje extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;

    public function __construct($nombre)
    {
        $this->nombre = $nombre;
    }

    public function build()
    {
        try {
            return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Cancelación de viaje')
                ->view('emails_viajes.email_cancelacion_viaje')
                ->with([
                    'nombre' => $this->nombre,
                ])
                ->withSwiftMessage(function ($message) {
                    $message->getHeaders()
                        ->addTextHeader('X-Priority', '1 (Highest)');
                    $message->getHeaders()
                        ->addTextHeader('Importance', 'high');
                });
        } catch (\Throwable $th) {
            Log::error(
                'Error al enviar el correo de Cancelación de viaje: ',
                [
                    'error' => $th->getMessage(),
                ]
            );

        }
    }
}
