<?php

use App\Http\Controllers\EmailController;

use App\Http\Controllers\ViajeController;
use App\Http\Controllers\Viajes;

use App\Http\Controllers\whatsapp\WhatsappController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas con el midelware de autenticación
Route::middleware(['auth:sanctum'])->group(function () {
    //Rutas de viajes controller
    Route::post('/actualizar-ubicacion', [Viajes::class, 'actualizarubicacion']);
    Route::post('/add-token', [Viajes::class, 'addtoken']);
    Route::post('/calificacion-de-ruta', [Viajes::class, 'calificacionderuta']);
    Route::post('/confirmar-direccion', [Viajes::class, 'confirmardireccion']);
    Route::post('/consultar-codigo', [Viajes::class, 'consultarcodigo']);
    Route::post('/consultar-tarjetas', [Viajes::class, 'consultartarjetas']);
    Route::post('/calcular-tarifa-servicio', [Viajes::class, 'calculartarifaservicio']);
    Route::post('/cambiar-idioma', [Viajes::class, 'cambiaridioma']);
    Route::post('/contactos', [Viajes::class, 'contactos']);
    Route::post('/editar-datos', [Viajes::class, 'editardatos']);
    Route::post('/editar-lugar', [Viajes::class, 'editarlugar']);
    Route::post('/eliminar-lugar', [Viajes::class, 'eliminarlugar']);
    Route::post('/eliminar-token', [Viajes::class, 'eliminartoken']);
    Route::post('/guardar-id-registration', [Viajes::class, 'guardaridregistration']);
    Route::post('/guardar-lugar', [Viajes::class, 'guardarlugar']);
    Route::post('/listar-idiomas', [Viajes::class, 'listaridiomas']);
    Route::post('/listar-lugares', [Viajes::class, 'listarlugares']);
    Route::post('/misviajes', [Viajes::class, 'misviajes']);
    Route::post('/obtener-usuario', [Viajes::class, 'obtenerusuario']);
    Route::post('/proximas-rutas', [Viajes::class, 'proximasrutas']);
    Route::post('/reestablecer-contrasena-cliente', [Viajes::class, 'reestablecercontrasenacliente']);
    Route::post('/reintentar-pago', [Viajes::class, 'reintentarpago']);
    Route::post('/servicio-activo', [Viajes::class, 'servicioactivo']);
    Route::post('/servicios-pedidos', [Viajes::class, 'serviciospedidos']);
    Route::post('/listar-notificaciones', [Viajes::class, 'listarNotificaciones']);

    // Rutas de viajes controller requesttrips
    Route::post('/consultar-cliente', [ViajeController::class, 'consultclient']);
    Route::post('/listar-estados-viaje', [ViajeController::class, 'listarEstadosPorMaestro']);
    Route::post('/listar-tipos-viaje', [ViajeController::class, 'listarTiposDeViaje']);
    Route::post('/listar-viajes-activos', [ViajeController::class, 'listarViajesActivos']);
    Route::post('/listar-viajes-generales', [ViajeController::class, 'listarViajesGenerales']);
    Route::post('/crear-solicitud-viaje', [ViajeController::class, 'requesttrips']);

});


// Ruta de la validación del correo electronico para la app
Route::post('/recuperar-password', [TokenController::class, 'recuperarContraseña']);
Route::post('/validate-email', [TokenController::class, 'validateEmail']);
Route::post('/validate-codigo', [VerificationController::class, 'verifyCode']);

// Ruta autenticación
Route::post('/createuser', [AuthController::class, 'createuser']);
Route::post('/eliminar-cuenta', [AuthController::class, 'eliminarcuenta']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/cambio-password', [AuthController::class, 'cambiarContrasenia']);
Route::post('/contrasena-olvidada', [AuthController::class, 'contrasenaOlvidada']);


Route::post('/calificar-viaje', [ViajeController::class, 'calificarViaje']); // --
Route::post('/listar-viajes-link', [ViajeController::class, 'listarViajesLink']); // --

// Rutas de whatsapp
Route::post('/enviar-wapp', [WhatsappController::class, 'enviarWhatsApp']);

// Rutas correos electronicos
Route::post('/enviar-correo', [EmailController :: class, 'sendEmail']);
