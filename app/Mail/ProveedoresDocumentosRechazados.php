<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProveedoresDocumentosRechazados extends Mailable
{
    use Queueable, SerializesModels;

    public $total;

    public $link;

    public function __construct($total, $link)
    {
        $this->total = $total;
        $this->link = $link;
    }

    public function build(){
        try {
            return $this
            ->from('no-reply@aotour.com.co', 'Documentos Rechazados') // Cambia esto a tu dirección de correo
            //->Bcc('comercial@aotour.com.co') // Volver global
            ->subject('Documentos Rechazados')
            ->view('inscripcion_proveedores_emails.email_documentos_rechazados') // Asegúrate de poner el nombre correcto de tu vista
            ->with([
                'total' => $this->total,
                'link' => $this->link,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error al enviar el correo de inscripción de proveedores:', [
                'error' => $th->getMessage(),
                'total' => $this->total,
                'link' => $this->link,
            ]);
        }
    }
}
