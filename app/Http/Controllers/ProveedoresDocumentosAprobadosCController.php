<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresDocumentosAprobadosC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; 

class ProveedoresDocumentosAprobadosCController extends Controller
{
    public function enviarCorreo (Request $request) {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'texto' => 'required|string',
            'motivo' => 'nullable|string',
            'link' => 'nullable|string',
            'email' => 'required|email',
        ]);


        try {
            Mail::to($validated['email'])->send(new ProveedoresDocumentosAprobadosC(
                $validated['titulo'],
                $validated['texto'],
                $validated['motivo'],
                $validated['link'],
                
            ));

            return response()->json(['message' => 'Correo enviado exitosamente', 200]);
        } catch (\Throwable $th) {
            \Log::error('Error al enviar el correo de inscripción de proveedores: ', [
                'error' => $th->getMessage(),
                'titulo' => $validated['titulo'],
                'texto' => $validated['texto'],
                'motivo' => $validated['motivo'],
                'link' => $validated['link'],
                'email' => $validated['email'],
                'trace' => $th->getTraceAsString() // Esto te dará más contexto sobre el error
            ]);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
