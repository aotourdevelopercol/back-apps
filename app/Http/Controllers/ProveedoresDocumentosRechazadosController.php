<?php

namespace App\Http\Controllers;



use App\Mail\ProveedoresDocumentosRechazados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; 

class ProveedoresDocumentosRechazadosController extends Controller
{
    public function enviarCorreo(Request $request)
    {
        $validated = $request->validate(
            [
                'total' => 'required|integer',
                'link' => 'required|string',
                'email' => 'required|email',
            ]
        );

        try {
            Mail::to($validated['email'])->send(new ProveedoresDocumentosRechazados(
                $validated['total'],
                $validated['link']
            ));

            return response()->json(['message' => 'Conrreo enviado correctamente'], 200);
                    
        } catch (\Throwable $th) {
            \Log::error('Error al enviar correo de documentos rechazados', [
                'error' => $th->getMessage(),
                'link' => $validated['link'],
                'total' => $validated['total'],
                'email' => $validated['email'],
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['error' => $th-> getMessage()], 500 );
        }
    }
}
