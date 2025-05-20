<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PagoProveedores extends Mailable 
{
    use Queueable, SerializesModels;


    public function __construct()
    {
        \Log::info('Constructor de PagoProveedores ejecutado');
    }

     /**
      * Get the message envelope.
      */
    //  public function envelope(): Envelope
    //  {
        
    //      return new Envelope(
    //          subject: 'Pago proveedores',
    //          from: new Address(config('mail.from.address'), config('mail.from.name'))
    //      );
    //  }
 
     /**
      * Get the message content definition.
      */
    //  public function build(): 
    //  {
    //     \Log::info('Cargando correo PagoProveedores');
    //      return new Content(
    //          view: 'inscripcion_proveedores_emails.email_cuenta_de_cobro_corregir'
    //     //     with: [] 
    //      );
    //  } proveedores_email\pago_proveedor.blade.php

    public function build()
    {
        try {
            Log::info('Cargando correo PagoProveedores');
            return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Asignacion de viaje')
                ->view('proveedores_email.pago_proveedor'); // Ensure the view name is correct
        } catch (\Throwable $th) {
            Log::error(
                'Error al enviar el correo de pagos',
                [
                    'error' => $th->getMessage(),
                ]
            );

            throw $th; // Optionally handle the exception further or return a user-friendly message
        }
    }

 
 
}
