<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Log;

class ProveedoresCapacitar extends Mailable
{
    use Queueable, SerializesModels;

    public $texto;

    public function __construct($texto)
    {
        $this->texto = $texto;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        try {
            return $this
            ->from(config('mail.from.address'), config('mail.from.name')) // Cambia esto a tu dirección de correo
                //->Bcc('comercial@aotour.com.co') // Volver global
                ->subject('Capacitación de Conductor')
                ->view('inscripcion_proveedores_emails.email_capacitar') // Asegúrate de poner el nombre correcto de tu vista
                ->with([
                    'texto' => $this->texto
                ]);
        } catch (\Throwable $th) {
            Log::error(
                'Error al enviar el correo de capacitación',
                [
                    'error' => $th->getMessage(),
                    'texto' => $this->texto
                ]
            );

            throw $th;
        }
    }

}
