<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Agregar esta línea para usar el logger

class InscripcionProveedoresEmails extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $totalConductores;
    public $totalVehiculos;

    public function __construct($nombre, $totalConductores, $totalVehiculos)
    {
        $this->nombre = $nombre;
        $this->totalConductores = $totalConductores;
        $this->totalVehiculos = $totalVehiculos;
    }

    public function build()
    {
        try {
            return $this
            ->from(config('mail.from.address'), config('mail.from.name')) // Cambia esto a tu dirección de correo
                //->Bcc('comercial@aotour.com.co') // Volver global
                ->subject('Nuevo proveedor para ingreso')
                ->view('inscripcion_proveedores_emails.email_ingreso') // Asegúrate de poner el nombre correcto de tu vista
                ->with([
                    'nombre' => $this->nombre,
                    'totalConductores' => $this->totalConductores,
                    'totalVehiculos' => $this->totalVehiculos,
                ]);
        } catch (\Exception $e) {
            // Registra el error en el log
            Log::error('Error al enviar el correo de inscripción de proveedores:', [
                'error' => $e->getMessage(),
                'nombre' => $this->nombre,
                'totalConductores' => $this->totalConductores,
                'totalVehiculos' => $this->totalVehiculos,
            ]);
            // Lanza la excepción de nuevo para que se maneje en el flujo de la aplicación
            throw $e;
        }
    }
}