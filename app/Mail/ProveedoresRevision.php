<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProveedoresRevision extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;

    public function __construct($nombre)
    {
        $this->nombre = $nombre;
    }


   public function build(){
    try {
        return $this
        ->from(config('mail.from.address'), config('mail.from.name'))
        ->subject('En revición documental')
        ->view('inscripcion_proveedores_emails.email_revision')
        ->with(
            ['nombre'=> $this->nombre]
        );
    } catch (\Throwable $th) {
        Log::error('Error al enviar correo de revisión', [
            'error' => $th-> getMessage(),
            'nombre' => $this->nombre
        ]);

        throw $th;
    }
   }
}
