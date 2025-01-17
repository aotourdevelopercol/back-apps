<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Import Log facade

class EsperarEjecutivo extends Mailable
{

    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        try {
            return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Conductor a la espera del pasajero')
                ->view('emails_viajes.email_esperar_ejecutivo') // Ensure the view name is correct
                ->with([
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
