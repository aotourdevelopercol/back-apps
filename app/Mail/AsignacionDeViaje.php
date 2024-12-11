<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AsignacionDeViaje extends Mailable
{
    use Queueable, SerializesModels;

    // Nombre pasajero
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

    // Token para url
    public $token;

    

    public function __construct($nombre, $conductor, $placa, $telefonoConductor, $puntoA, $puntoB, $fecha, $hora, $token)
    {
        $this->nombre = $nombre;
        $this->conductor = $conductor;
        $this->placa = $placa;
        $this->telefonoConductor = $telefonoConductor;
        $this->puntoA = $puntoA;
        $this->puntoB = $puntoB;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->token = $token;

    }

    public function build()
    {
        try {
            return $this
                ->from(env('CORREO_NO_REPLY'), env('NOMBRE_CORREOS'))
                ->subject('Asignacion de viaje')
                ->view('emails_viajes.email_asignacion_viaje') // Ensure the view name is correct
                ->with([
                   $this->nombre,
                   $this->conductor,
                   $this->placa,
                   $this->telefonoConductor,
                   $this->puntoA,
                   $this->puntoB,
                   $this->fecha,
                   $this->hora,
                   $this->token
                ]);
        } catch (\Throwable $th) {
            Log::error(
                'Error al enviar el correo de capacitación',
                [
                    'error' => $th->getMessage(),
                ]
            );

            throw $th; // Optionally handle the exception further or return a user-friendly message
        }
    }
}
