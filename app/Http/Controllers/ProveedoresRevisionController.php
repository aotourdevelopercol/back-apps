<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; 

class ProveedoresRevisionController extends Controller
{

    public function enviarCorreo (Request $request){

        $validated = $request -> validate([
            'nombre' => 'required|String',
            'email' => 'required|email',
        ]);

        try {
            Mail::to($validated['email'])->send(new ProveedoresRevision(
                $validated['nombre']
            ));

            return response() -> json(['message' => 'Correo enviado correctamente'], 200);
        } catch (\Throwable $th) {
            \Log::error ('Error al enviar el correo de revisón', [
                'error' => $th-> getMessage(),
                'Nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
   
}
