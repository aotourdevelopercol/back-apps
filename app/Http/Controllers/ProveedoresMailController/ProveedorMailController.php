<?php

namespace App\Http\Controllers\ProveedoresMailController;

use App\Http\Controllers\Controller;

use App\Mail\ProveedorMailCuentaDeCobro;
use App\Mail\ProveedorMailCuentaDeCobroRadicada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; 

class ProveedorMailController extends Controller
{
    public function cuentaDeCobroPorCorregir(Request $request)
    {
        // Validación de los datos
        $validated = $request->validate([
            'email' => 'required|email',
            'cc' => 'required|email'
        ]);

        // Envía el correo
        try {
            Mail::to($validated['email'])->send(new ProveedorMailCuentaDeCobro($validated['cc']));

            return response()->json(['message' => 'Correo enviado correctamente'], 200);
        }catch (\Exception $e) {
            \Log::error('Error : ', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Esto te dará más contexto sobre el error
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cuentaDeCobroRadicada(Request $request)
    {
        // Validación de los datos
        $validated = $request->validate([
            'email' => 'required|email',
            'cc' => 'required|email'
        ]);

        // Envía el correo
        try {
            Mail::to($validated['email'])->send(new ProveedorMailCuentaDeCobroRadicada($validated['cc']));

            return response()->json(['message' => 'Correo enviado correctamente'], 200);
        }catch (\Exception $e) {
            \Log::error('Error: ', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Esto te dará más contexto sobre el error
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
