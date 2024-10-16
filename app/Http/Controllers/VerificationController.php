<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TempToken; // Asegúrate de importar tu modelo TempToken
use Tymon\JWTAuth\Facades\JWTAuth;

class VerificationController extends Controller
{
    public function verifyCode(Request $request)
    {
        // Validar el correo y el código recibidos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'codigo' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 'ERROR_IN_RECIVED_FIELDS'], 200);
        }

        // Extraer el correo y el código ingresado por el usuario
        $email = strtolower($request->input('email')); // Convertir el correo a minúsculas
        $codigoIngresado = $request->input('codigo');

        // Comprobar si el token existe en la tabla temp_tokens
        $tempToken = TempToken::where('email', $email)->first();

        if (!$tempToken) {
            return response()->json(['code' => 'NO_TOKEN_FOUND_ASSOCIATED_WITH_EMAIL'], 200); // 404 Not Found
        }

        // Decodificar el token para obtener el payload
        try {
            // Decodificamos el token
            $payload = JWTAuth::setToken($tempToken->token)->getPayload();
        } catch (\Exception $e) {
            return response()->json(['code' => 'INVALID_TOKEN'], 200); // 401 Unauthorized
        }

        // Obtener el código del payload
        $codigoAlmacenado = $payload['codigo'];

        // Validar el código
        if ($codigoAlmacenado !== $codigoIngresado) {
            return response()->json(['code' => 'INCORRECT_CODE'], 200); // 400 Bad Request
        }

        // Si el código es correcto, puedes proceder a eliminar el token o hacer otras acciones
        // Eliminar el registro de temp_tokens si es necesario
        $tempToken->delete();

        return response()->json(['code' => 'CODE_VERIFIED_SUCCESFULLY'], 200); // 200 OK
    }
}