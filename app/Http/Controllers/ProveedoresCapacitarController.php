<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresCapacitar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProveedoresCapacitarController extends Controller
{
    public function enviarCorreo(Request $request)
    {
        $validated = $request->validate(
            [
                'asunto' => 'required|String',
                'email' => 'required|email'
            ]
        );

        try {
            Mail::to($validated['email'])->send(new ProveedoresCapacitar(
                $validated['asunto']
            ));
            return response()->json(['message'=> 'Correo enviado exitosamente'], 200);
        } catch (\Throwable $th) {
            \Log::error('Error al enviar el correo de capacitación' , [
                'error' => $th->getMessage(),
                'asunto' => $validated['asunto'],
                'email' => $validated['email']
            ]);

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
