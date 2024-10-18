<?php

namespace App\Http\Controllers;
use Response;
use App\Models\User;
use App\Models\Subcentro;
use Illuminate\Support\Facades\Log;
use Auth;
use DB;
use Hash;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function logout(Request $request)
    {

        $user = Auth::user();

        $user->tokens()->delete();

        return Response::json([
            'response' => true
        ]);

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

            // Asigna propiedades al usuario con datos normalizados
            $user->first_name = strtoupper(strtolower($request->nombre));
            $user->last_name = strtoupper(strtolower($request->apellido));
            $user->email = strtolower($request->email);
            $user->username = strtolower($request->email);
            $user->telefono = $request->telefono;
            $user->password = Hash::make($request->password);
            $user->id_perfil = 23; // ID de perfil por defecto
            $user->fk_tipo_usuario = 1; // Tipo de usuario por defecto
            $user->id_tipo_usuario = 0; // ID de tipo de usuario
            $user->master = 0; // Indicador de maestro

            // Intenta guardar el usuario
            if ($user->save()) {
                // Crea una nueva instancia de Subcentro
                $sub = new Subcentro();
                $sub->nombre = strtoupper(strtolower($request->nombre));
                $sub->apellido = strtoupper(strtolower($request->apellido));
                $sub->correo = strtolower($request->email);
                $sub->celular = $request->telefono;
                $sub->centrosdecosto_id = 100; // ID de centro de costo por defecto
                $sub->save(); // Guarda el subcentro

                // Actualiza al usuario con el subcentro y el centro de costo
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'centrodecosto_id' => 100,
                        'subcentrodecosto_id' => $sub->id
                    ]);

                // Retorna una respuesta JSON exitosa
                return Response::json(['code' => 'SUCCESFULLY']);
            }

            // Retorna una respuesta de error si no se pudo guardar el usuario
            return Response::json(['code' => 'FAILS'], 500);
        } catch (\Exception $e) {
            // Captura cualquier excepción y retorna un mensaje de error
            return Response::json([
                'code' => 'EXCEPTION',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            // Buscar usuario por correo electrónico
            $usuario = DB::table('users')
                ->where('username', $request->username)
                ->first();

            // Verificar si el usuario existe
            if ($usuario == null) {
                return Response::json([
                    'code' => 'NOT_FOUND_USER', // El usuario con el correo proporcionado no existe.
                ]);
            }

            // Validar credenciales
            $credentials = $request->validate([

                'username' => 'required|string', // Asegurarse de que se proporcione un correo electrónico válido
                'password' => 'required|string', // Asegurarse de que se proporcione una contraseña
            ]);

            // Intentar autenticar al usuario
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Verificar si el usuario está baneado
                if ($user->baneado == 1) {
                    return Response::json([
                        'message' => 'DISABLED_USER', // El usuario está desactivado. Contacte al administrador del sistema o soporte.
                    ]);
                } else {
                    // Eliminar tokens existentes
                    $user->tokens()->delete();

                    // Crear un nuevo token para el usuario
                    $token = $user->createToken('auth_token')->plainTextToken;

                    // Actualizar la fecha y hora del último inicio de sesión
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'last_login' => now(), // Usar now() para mayor claridad
                        ]);

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
                // Fallo en la autenticación
                return Response::json([
                    'response' => false,
                    'code' => 'FAILS', // Las credenciales son incorrectas.
                ]);
            }
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción no controlada
            \Log::error('asdads:', [
                'error' => $e->getMessage(),
            ]);
            return Response::json([
                'response' => false,
                'code' => 'UNKNOWN_ERROR', // Error desconocido.
                'message' => $e->getMessage(),
                // Mensaje del error desconocido.
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
            $user = User::where('email', $validate['email'])->first();

            // Validar si el usuario existe
            if (!$user) {
                return response()->json(['CODE' => 'USER_NOT_FOUND'], 404);
            }

            // Verificar si la nueva contraseña es la misma que la actual
            if (Hash::check($validate['nueva-password'], $user->password)) {
                return response()->json(['CODE' => 'SAME_PASSWORD_ERROR', 'message' => 'La nueva contraseña no puede ser igual a la anterior.'], 400);
            }

            // Si es diferente, encriptar la nueva contraseña
            $password = bcrypt($validate['nueva-password']);

            // Actualizar la contraseña
            DB::table('users')
                ->where('email', $validate['email'])  // puedes usar también username si prefieres
                ->update([
                    'password' => $password
                ]);

            return response()->json(['CODE' => 'PASSWORD_CHANGED'], 200);
        } catch (\Exception $e) {
            \Log::error('Error: ', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['CODE' => 'ERROR', 'message' => 'Error al cambiar la contraseña.'], 500);
        }
    }

}
