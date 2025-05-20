<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagoProveedores extends Mailable
{
    use Queueable, SerializesModels;

     /**
      * Get the message envelope.
      */
     public function envelope(): Envelope
     {
        
         return new Envelope(
             subject: 'Pago proveedore',
             from: new Address(config('mail.from.address'), config('mail.from.name'))
         );
     }
 
     /**
      * Get the message content definition.
      */
     public function content(): Content
     {
        \Log::info('Cargando correo PagoProveedores');
         return new Content(
             view: 'proveedores_email.pago_proveedor'
        //     with: []
         );
     }
 
     /**
      * Get the attachments for the message.
      *
      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
      */
     public function attachments(): array
     {
         return [];
     }
}
