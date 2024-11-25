<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProveedoresDocumentosAprobados extends Mailable
{
    use Queueable, SerializesModels;


    public $titulo;

    public $texto;

    public $link;

    
    public function __construct($titulo, $texto, $link)
    {
        $this->titulo = $titulo;
        $this->texto = $texto;
        $this->link = $link;
    }


    public function build(){
        try {
            return $this
            ->from('no-reply@aotour.com.co', 'Inscripción de Proveedor') // Cambia esto a tu dirección de correo
            //->Bcc('comercial@aotour.com.co') // Volver global
            ->subject('Nuevo proveedor para ingreso')
            ->view('inscripcion_proveedores_emails.email_documentos_aprobados') // Asegúrate de poner el nombre correcto de tu vista
            ->with([
                'titulo' => $this->titulo,
                'texto' => $this->texto,
                'link' => $this->link,
            ]);
        } catch (\Throwable $th) {
            Log::error('Error al enviar correo de documentos aprobados', [
                'error' => $th->getMessage(),
                'titulo' => $this->titulo,
                'texto' => $this->texto,
                'link' => $this->link,
            ]);

            throw $th;
        }
    }

    
}
