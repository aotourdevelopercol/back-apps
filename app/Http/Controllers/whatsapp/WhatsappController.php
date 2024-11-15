<?php

namespace App\Http\Controllers\whatsapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function enviarWhatsApp(Request $request)
    {
        $validate = $request->validate([
            'number' => 'required|string',
            'parameters' => 'required|string',
            'parametersButton' => 'nullable|string',
            'plantilla-nombre' => 'required|string',
            'type' => 'required|string',
            'typeButton' => 'nullable|string'
        ]);

        try {
            // Decodificar el string JSON a un array
            $parametersArray = json_decode($validate['parameters'], true);

            // Verificar si viene el parámetro "parametersButton" y decodificarlo
            $parametersArrayButton = [];
            if (!empty($validate['parametersButton'])) {
                $parametersArrayButton = json_decode($validate['parametersButton'], true);
            }

            // Preparar el array base de "components"
            $components = [
                [
                    "type" => $validate['type'],
                    "parameters" => $parametersArray, // Usar el array decodificado aquí
                ]
            ];

            // Añadir el componente del botón solo si está presente
            if (!empty($parametersArrayButton)) {
                $components[] = [
                    "type" => $validate['typeButton'],
                    "parameters" => $parametersArrayButton, // Usar el array decodificado aquí
                ];
            }

            // Verificar si la decodificación fue exitosa
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Invalid JSON format for parameters.'], 400);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v21.0/489528307571128/messages");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "messaging_product" => "whatsapp",
                "to" => $validate['number'],
                "type" => "template",
                "template" => [
                    "name" => $validate['plantilla-nombre'],
                    "language" => ["code" => "es"],
                    "components" => $components
                ],
            ]));

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer " . env('KEY_WHATSAPP'),
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            return response()->json(['Success' => $response], 200);
        } catch (\Exception $th) {
            \Log::error('Error al enviar el mensaje de WhatsApp: ', [
                'error' => $th->getMessage(),
            ]);
            return response()->json(['error' => 'Error al enviar el mensaje.'], 500);
        }
    }
}
