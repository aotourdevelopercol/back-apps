<?php

namespace App\Http\Controllers;

use App\Mail\ProveedoresDocumentosAprobados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProveedoresDocumentosAprobadosController extends Controller
{
    public function enviarCorreo (Request $request){

        //validar los datos

        $validated = $request-> validate(
            [
                'titulo' => 'required|string',
                'asunto' => 'required|string',
                'link' => 'required|string',
                'email' => 'required|email'
            ]
            );

            try {
                Mail::to($validated['email'])->send(new ProveedoresDocumentosAprobados(
                    $validated['titulo'],
                    $validated['asunto'],
                    $validated['link'],
                ));

                return response()->json(['message' => 'Correo enviado exitosamente'], 200);
            } catch (\Throwable $th) {
                \Log::error('Error al enviar el correo de documentos aprobados', [
                    'error' => $th->getMessage(),
                    'titulo' =>$validated['titulo'],
                    'asunto' =>$validated['asunto'],
                    'link' =>$validated['link'],
                ]);

                return response()->json(['error' => $th->getMessage()], 500);
            }
    }
}
