<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 

class ProveedoresDocumentosActualizados extends Mailable
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
                ->from(env('CORREO_NO_REPLY'), env('NOMBRE_CORREOS')) // Cambia esto a tu dirección de correo
                //->Bcc('comercial@aotour.com.co') // Volver global
                ->subject('Documentos actualizados')
                ->view('inscripcion_proveedores_emails.email_documentos_actualizados') // Asegúrate de poner el nombre correcto de tu vista
                ->with([
                    'nombre' => $this->nombre,
                ]);
        } catch (\Throwable $th) {
            Log::error('Error al enviar el correo de actualizacion de documentos', [
                'error' => $th->getMessage(),
                'nombre' => $this->nombre,
            ]);

            throw $th;
        }
    }
}
