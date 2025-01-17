<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProveedorProvisional extends Mailable
{
    use Queueable, SerializesModels;

    public $motivo;
    public $ruta;


    /**
     * Create a new message instance.
     */
    public function __construct($ruta, $motivo)
    {
        $this->motivo = $motivo;
        $this->ruta = $ruta;
 
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Proveedor provisional')
            ->view('email_proveedores_provinsionales.email_provisional_proveedor')
            ->with(['ruta' => $this->ruta]);
    }
}
