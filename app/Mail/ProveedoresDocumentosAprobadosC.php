<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProveedoresDocumentosAprobadosC extends Mailable
{
    use Queueable, SerializesModels;

    public $titulo;

    public $texto;


    public $motivo;

    public $link;


    public function __construct($titulo, $texto, $motivo, $link)
    {
        $this->titulo = $titulo;
        $this->texto = $texto;
        $this->motivo = $motivo;
        $this->link = $link;
        
    }

    public function build()
    {
        try {
            return $this
                ->from('no-reply@aotour.com.co', 'Inscripción de Proveedor') // Cambia esto a tu dirección de correo
                //->Bcc('comercial@aotour.com.co') // Volver global
                ->subject('Documentos aprobados')
                ->view('inscripcion_proveedores_emails.email_ingreso') // Asegúrate de poner el nombre correcto de tu vista
                ->with([
                    'titulo' => $this->titulo,
                    'texto' => $this->texto,
                    'motivo' => $this->motivo,
                    'link' => $this->link
                ]);

        } catch (\Throwable $th) {
            // Registra el error en el log
            Log::error('Error al enviar el correo de inscripción de proveedores:', [
                'error' => $th->getMessage(),
                'titulo' => $this->titulo,
                'texto' => $this->texto,
                'motivo' => $this->motivo,
                'link' => $this->link
            ]);
            // Lanza la excepción de nuevo para que se maneje en el flujo de la aplicación
            throw $th;
        }
    }

}
