<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\TempToken;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Mail;

class TokenController extends Controller
{
    public function validateEmail(Request $request)
    {
        // Validar el correo electrónico recibido
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Convertir el correo ingresado a minúsculas
        $email = strtolower($request->input('email'));

        // Comprobar si el correo existe en la tabla users
        $userExists = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->exists();

        if ($userExists) {
            return response()->json(['code' => 'EXIST_EMAIL'], 200);
        }

        // Comprobar si el correo ya existe en la tabla temp_tokens
        $existingToken = TempToken::where('email', $email)->first();

        // Si ya existe un token para ese correo, eliminar el registro
        if ($existingToken) {
            $existingToken->delete(); // Eliminar el registro existente
        }

        // Generar un código aleatorio
        $codigo = Str::random(6);

        // Generar el token JWT con claims personalizados incluyendo el 'sub'
        $payload = JWTAuth::factory()->customClaims([
            'sub' => $email,  // Usar el correo como 'sub' (subject)
            'email' => $email,
            'codigo' => $codigo,
            'exp' => now()->addHour()->timestamp
        ])->make();

        $token = JWTAuth::encode($payload)->get();

        // Almacenar el token y el email en la tabla temp_tokens
        TempToken::create([
            'email' => $email,
            'token' => $token
        ]);

        // Enviar el código por correo electrónico
        Mail::send('emails.codigo', ['codigo' => $codigo], function ($message) use ($email) {
            $message->to($email)
                ->subject('Tu código de verificación');
        });

        return response()->json([
            'token' => $token,
            'code' => 'CODE_SENT_TO_EMAIL'
        ], 200);
    }


    public function recuperarContraseña(Request $request)
    {
        // Validar el correo electrónico recibido
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Convertir el correo ingresado a minúsculas
        $email = strtolower($request->input('email'));

        // Comprobar si el correo existe en la tabla users
        $userExists = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->exists();

        if ($userExists) {
            // Comprobar si el correo ya existe en la tabla temp_tokens
            $existingToken = TempToken::where('email', $email)->first();

            // Si ya existe un token para ese correo, eliminar el registro
            if ($existingToken) {
                $existingToken->delete(); // Eliminar el registro existente
            }

            // Generar un código aleatorio
            $codigo = Str::random(6);

            // Generar el token JWT con claims personalizados incluyendo el 'sub'
            $payload = JWTAuth::factory()->customClaims([
                'sub' => $email,  // Usar el correo como 'sub' (subject)
                'email' => $email,
                'codigo' => $codigo,
                'exp' => now()->addHour()->timestamp
            ])->make();

            $token = JWTAuth::encode($payload)->get();

            // Almacenar el token y el email en la tabla temp_tokens
            TempToken::create([
                'email' => $email,
                'token' => $token
            ]);

            // Enviar el código por correo electrónico
            Mail::send('emails.recuperacion_password', ['codigo' => $codigo], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Tu código de verificación');
            });

            return response()->json([
                'token' => $token,
                'code' => 'CODE_SENT_TO_EMAIL'
            ], 200);
        } 

        return response()->json(
            ['code' => 'EMAIL_NOT_FOUD']
        );

    }
}