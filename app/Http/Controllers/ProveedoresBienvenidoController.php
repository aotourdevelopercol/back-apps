<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresBienvenido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProveedoresBienvenidoController extends Controller
{
    public function enviarCorreo (Request $request){
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        try {
            Mail::to($validated['email'])->send(new ProveedoresBienvenido);

            return response()->json(['message' => 'Correo enviado exitosamente'], 200);
        } catch (\Throwable $th) {
            \Log::error('Error al enviar el correo de bienvenida', [
                'error' => $th->getMessage()
            ] );

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
