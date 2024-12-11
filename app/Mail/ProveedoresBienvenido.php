<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProveedoresBienvenido extends Mailable
{
    use Queueable, SerializesModels;

    

    public function __construct()
    {
        //
    }

   public function build () {
    try {
        return $this
        ->from(env('CORREO_NO_REPLY'), env('NOMBRE_CORREOS')) // Cambia esto a tu dirección de correo
        //->Bcc('comercial@aotour.com.co') // Volver global
        ->subject('Bienvenido a nuestra familia de proveedores')
        ->view('inscripcion_proveedores_emails.email_bienvenido');
    } catch (\Throwable $th) {
        Log::error('Error al enviar el correo de bienvenida', [
            'error' => $th->getMessage()
        ]);
        throw $th;
    }
   }
}
