<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 

class ProveedoresAviso extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre; 

    public $totalConductores;

    public $totalVehiculos;


    public function __construct($nombre,$totalConductores, $totalVehiculos)
    {
        $this->nombre = $nombre;
        $this->totalConductores = $totalConductores;
        $this->totalVehiculos = $totalVehiculos;
    }

    public function build() {
        try {
            return $this
            ->from('no-reply@aotour.com.co', 'Inscripción de Proveedor') // Cambia esto a tu dirección de correo
            //->Bcc('comercial@aotour.com.co') // Volver global
            ->subject('Nuevo proveedor para ingreso')
            ->view('inscripcion_proveedores_emails.email_aviso') // Asegúrate de poner el nombre correcto de tu vista
            ->with([
                'nombre' => $this->nombre,
                'totalConductores' => $this->totalConductores,
                'totalVehiculos' => $this->totalVehiculos,
            ]);
        } catch (\Throwable $th) {
            // Registra el error en el log
            Log::error('Error al enviar el correo de inscripción de proveedores:', [
                'error' => $th->getMessage(),
                'nombre' => $this->nombre,
                'totalConductores' => $this->totalConductores,
                'totalVehiculos' => $this->totalVehiculos,
            ]);
            // Lanza la excepción de nuevo para que se maneje en el flujo de la aplicación
            throw $th;
        }
    }
}
