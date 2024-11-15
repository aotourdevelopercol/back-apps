<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Log;

class NuevoViaje extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function build()
    {
        try {
            return $this
            ->from('no-reply@aotour.com.co', 'Nuevo viaje')
            ->subject('Nuevo viaje')
            ->view('emails_viajes.email_nuevo_viaje');
            
        } catch (\Throwable $th) {
            Log::error('Error al enviar el email de nuevo viaje: '. $th);
        }
        
    }
}
