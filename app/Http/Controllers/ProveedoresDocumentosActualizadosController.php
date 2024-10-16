<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresDocumentosActualizados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProveedoresDocumentosActualizadosController extends Controller
{
    public function enviarCorreo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email',
        ]);


        try {
            Mail::to($validated['email'])->send(new ProveedoresDocumentosActualizados(
                $validated['nombre']
            ));

            return response()->json(['message' => 'Correo enviado correctamente'], 200);
        } catch (\Throwable $th) {
            \Log::error(
                'Error al enviar el correo de documentos actualizados',
                [
                    'error' => $th->getMessage(),
                    'nombre' => $validated['nombre'],
                    'email' => $validated['email'],
                ]
            );
            return response()->json(['error' => $th->getMessage()],  500);
        }
    }
}
