<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ModificacionViaje extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;

    // Nombre conductor
    public $conductor;

    // Placa del vehiculo
    public $placa;

    // Telefono del conductor
    public $telefonoConductor;

    // punto a
    public $puntoA;

    // Punto b
    public $puntoB;

    // Fecha y hora de viaje
    public $fecha;
    public $hora;
    

    public function __construct($nombre, $conductor, $placa, $telefonoConductor, $puntoA, $puntoB, $fecha, $hora)
    {
        $this->nombre = $nombre;
        $this->conductor = $conductor;
        $this->placa = $placa;
        $this->telefonoConductor = $telefonoConductor;
        $this->puntoA = $puntoA;
        $this->puntoB = $puntoB;
        $this->fecha = $fecha;
        $this->hora = $hora;
   

    }

    /**
     * Definición del envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('CORREO_NO_REPLY'), env('NOMBRE_CORREOS')), // Aquí usamos la clase Address
            subject: 'Modificación de Viaje'
        );
    }

    /**
     * Definición del contenido del correo
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails_viajes.email_modificacion_viaje', // Asegúrate de que la vista exista
            with: [
                'nombre' => $this->nombre,
                'conductor' => $this->conductor,
                'placa' => $this->placa,
                'telefonoConductor' => $this->telefonoConductor,
                'puntoA' => $this->puntoA,
                'puntoB' => $this->puntoB,
                'fecha' => $this->fecha,
                'hora' => $this->hora,
           
            ]
        );
    }

    /**
     * Manejo de posibles adjuntos (si aplica)
     */
    public function attachments(): array
    {
        return [];
    }
}