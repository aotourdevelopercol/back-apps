<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProveedorMailCuentaDeCobroRadicada extends Mailable
{
    use Queueable, SerializesModels;

    public $copia;

    public function __construct($copia)
    {
        $this->copia = $copia;
    }

   public function build () {
    try {
        return $this
        ->from(config('mail.from.address'), config('mail.from.name')) // Cambia esto a tu dirección de correo
        //->Bcc($copia) // Volver global
        ->subject('Cuenta de cobro por radicada')
        ->view('inscripcion_proveedores_emails.email_cuenta_de_cobro_radicada');
    } catch (\Throwable $th) {
        Log::error('Error al enviar el correo de bienvenida', [
            'error' => $th->getMessage()
        ]);
        throw $th;
    }
   }
}
