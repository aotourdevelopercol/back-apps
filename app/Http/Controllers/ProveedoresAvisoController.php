<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresAviso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProveedoresAvisoController extends Controller
{
    public function enviarCorreo(Request $request)
    {
        // Validación de los datos
        $validated = $request->validate([
            'nombre' => 'required|string',
            'totalConductores' => 'required|integer',
            'totalVehiculos' => 'required|integer',
            'email' => 'required|email',
        ]);

        // Envía el correo
        try {
            Mail::to($validated['email'])->send(new ProveedoresAviso(
                $validated['nombre'],
                $validated['totalConductores'],
                $validated['totalVehiculos']
            ));

            return response()->json(['message' => 'Correo enviado correctamente'], 200);
        }catch (\Exception $e) {
            \Log::error('Error al enviar el correo de inscripción de proveedores: ', [
                'error' => $e->getMessage(),
                'nombre' => $validated['nombre'],
                'totalConductores' => $validated['totalConductores'],
                'totalVehiculos' => $validated['totalVehiculos'],
                'email' => $validated['email'],
                'trace' => $e->getTraceAsString() // Esto te dará más contexto sobre el error
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
