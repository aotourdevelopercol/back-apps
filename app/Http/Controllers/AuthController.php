<?php

namespace App\Http\Controllers;
use App\Mail\ContraseñaOlvidada;
use Response;
use App\Models\User;
use App\Models\Subcentro;
use Illuminate\Support\Facades\Log;
use App\Models\TempToken;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Mail;

use DB;
use Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function logout(Request $request)
    {
        // Verifica si hay un usuario autenticado
        $user = Auth::user();

        

        if ($user) {
            try {
                // Elimina todos los tokens del usuario autenticado
                $user->tokens()->delete();

                // Retorna una respuesta JSON exitosa
                return response()->json([
                    'response' => true
                ], 200);
            } catch (\Exception $e) {
                // Captura cualquier excepción y retorna un mensaje de error
                \Log::error('Error al cerrar sesión:', [
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'code' => 'EXCEPTION',
                    'message' => $e->getMessage()
                ], 200);
            }
        }

        // Si no hay un usuario autenticado, retorna un error 401 (Unauthorized)
        return response()->json([
            'response' => true,
            'message' => 'LOGOUT'
        ], 200);
    }

    public function eliminarcuenta(Request $request)
    {

        $id = $request->user_id;

        $user = User::find($id);
        $user->email = null;
        $user->password = null;
        $user->idregistratiodevice = null;

        if ($user->save()) {

            return Response::json([
                'response' => true
            ]);

        }

    }

    /**
     * Crea un nuevo usuario y un subcentro asociado.
     *
     * Este método maneja la creación de un usuario basado en la información
     * del request. Valida los datos de entrada, crea un nuevo registro
     * de usuario, crea un registro de subcentro asociado y actualiza al usuario
     * con referencias al subcentro y al centro de costo creados.
     *
     * @param \Illuminate\Http\Request $request Datos del usuario a crear.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON indicando el éxito o el error de la operación.
     */
    public function createuser(Request $request)
    {
        try {
            // Inicializa una nueva instancia de User
            $user = new User();

            // Asigna propiedades al usuario
            $user->first_name = strtoupper(strtolower($request->nombre));
            $user->last_name = strtoupper(strtolower($request->apellido));
            $user->email = strtolower($request->email);
            $user->username = strtolower($request->email);
            $user->telefono = $request->telefono;
            $user->password = Hash::make($request->password);
            $user->id_perfil = 23;
            $user->fk_tipo_usuario = 4;
            $user->id_tipo_usuario = 0;
            $user->master = 0;

            if ($user->save()) {
                // Crea el subcentro
                $sub = new Subcentro();
                $sub->nombre = strtoupper(strtolower($request->nombre));
                $sub->apellido = strtoupper(strtolower($request->apellido));
                $sub->correo = strtolower($request->email);
                $sub->celular = $request->telefono;
                $sub->centrosdecosto_id = 100;
                $sub->save();

                // Actualiza el usuario
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'centrodecosto_id' => 100,
                        'subcentrodecosto_id' => $sub->id
                    ]);

                return Response::json(['code' => 'SUCCESFULLY']);
            }

            return Response::json(['code' => 'FAILS'], 500);
        } catch (QueryException $e) {
            // Código 23000 indica violación de restricción (como UNIQUE)
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'users_email_unique')) {
                return Response::json([
                    'code' => 'EMAIL_EXISTS',
                    'message' => 'El correo ya está registrado.'
                ], 409); // 409 Conflict
            }

            return Response::json([
                'code' => 'DB_ERROR',
                'message' => 'Error en la base de datos.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        } catch (\Exception $e) {
            return Response::json([
                'code' => 'EXCEPTION',
                'message' => 'Error general al registrar usuario.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            Log::info('Me estoy intentando loguear ' );
            // Validar credenciales
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            // Buscar usuario por nombre de usuario
            $usuario = DB::table('users')
                ->where('username', $credentials['username'])
                ->where('fk_tipo_usuario', 4)
                ->first();

            Log::info('Usuario: ' . json_encode($usuario));

            // Verificar si el usuario existe
            if ($usuario == null) {
                return Response::json([
                    'code' => 'NOT_FOUND_USER',
                ]);
            }


            // Verificar la contraseña
            if (Hash::check($credentials['password'], $usuario->password)) {
                Log::info('Contraseña válida');
                // La contraseña es correcta, procedemos con la autenticación
                Auth::loginUsingId($usuario->id);  // Loguear al usuario manualmente

                // Verificar si el usuario está baneado
                $user = Auth::user();
                if ($user->baneado == 1) {
                    return Response::json([
                        'message' => 'DISABLED_USER',
                    ]);
                } else {
                    // Eliminar tokens existentes
                    $user->tokens()->delete();

                    // Crear un nuevo token para el usuario
                    $token = $user->createToken('auth_token')->plainTextToken;

                    // Actualizar la fecha y hora del último inicio de sesión
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['last_login' => now()]);

                    // Recuperar el objeto usuario
                    $usuario = User::find($user->id);

                    // Retornar respuesta exitosa
                    return Response::json([
                        'code' => 'SUCCESFULLY',
                        'token' => $token,
                        'acceso' => true,
                        'id_usuario' => $user->id,
                        'usuario' => $usuario,
                    ]);
                }
            } else {
                Log::info('Contraseña invalida');
                // Fallo en la autenticación (contraseña incorrecta)
                return Response::json([
                    'response' => false,
                    'code' => 'FAILS',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error en login:', ['error' => $e->getMessage()]);
            return Response::json([
                'response' => false,
                'code' => 'UNKNOWN_ERROR',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    

    public function cambiarContraseña(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|string',
            'nueva-password' => 'required|string'
        ]);

        try {
            // Obtener el usuario por email
            $user = User::where('email', $validate['email'])
                ->first();

            // Validar si el usuario existe
            if (!$user) {
                return response()->json(['code' => 'USER_NOT_FOUND'], 404);
            }

            // Verificar si la nueva contraseña es la misma que la actual
            if (Hash::check($validate['nueva-password'], $user->password)) {
                return response()->json(['code' => 'SAME_PASSWORD_ERROR', 'message' => 'La nueva contraseña no puede ser igual a la anterior.'], 400);
            }

            // Si es diferente, encriptar la nueva contraseña
            $password = bcrypt($validate['nueva-password']);

            // Actualizar la contraseña
            DB::table('users')
                ->where('email', $validate['email'])  // puedes usar también username si prefieres
                ->update([
                    'password' => $password
                ]);

            return response()->json(['code' => 'PASSWORD_CHANGED'], 200);
        } catch (\Exception $e) {
            \Log::error('Error: ', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['code' => 'ERROR', 'message' => 'Error al cambiar la contraseña.'], 500);
        }
    }

}
