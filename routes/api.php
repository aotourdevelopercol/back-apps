<?php

use App\Http\Controllers\ProveedoresMailController\ProveedorMailController;

use App\Http\Controllers\viajes\Viajescontroller;
use App\Http\Controllers\whatsapp\WhatsappController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InscripcionProveedoresController;
use App\Http\Controllers\ProveedoresRevisionController;
use App\Http\Controllers\ProveedoresDocumentosRechazadosController;
use App\Http\Controllers\ProveedoresDocumentosAprobadosController;
use App\Http\Controllers\ProveedoresDocumentosActualizadosController;
use App\Http\Controllers\ProveedoresAvisoController;
use App\Http\Controllers\ProveedoresCapacitarController;
use App\Http\Controllers\ProveedoresBienvenidoController;
use App\Http\Controllers\ProveedoresDocumentosAprobadosCController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AuthController;





// Rutas para correos de proveedores 
Route::post('/aviso', [ProveedoresAvisoController :: class, 'enviarCorreo']);
Route::post('/bienvenido', [ProveedoresBienvenidoController :: class, 'enviarCorreo']);
Route::post('/capacitar', [ProveedoresCapacitarController :: class, 'enviarCorreo']);
Route::post('/cuenta-de-cobro-corregir', [ProveedorMailController :: class, 'cuentaDeCobroPorCorregir']);
Route::post('/cuenta-de-cobro-radicada', [ProveedorMailController :: class, 'cuentaDeCobroRadicada']);
Route::post('/documentosActualizados', [ProveedoresDocumentosActualizadosController :: class, 'enviarCorreo']);
Route::post('/documentoAprobados', [ProveedoresDocumentosAprobadosController :: class, 'enviarCorreo']);
Route::post('/documentosAprovadosC', [ProveedoresDocumentosAprobadosCController :: class, 'enviarCorreo']);
Route::post('/documentosRechazados', [ProveedoresDocumentosRechazadosController :: class, 'enviarCorreo']);
Route::post('/inscripcion', [InscripcionProveedoresController::class, 'enviarCorreo']);
Route::post('/revision', [ProveedoresRevisionController :: class, 'enviarCorreo']);

// Ruta de la validación del correo electronico para la app
Route::post('/recuperar-password', [TokenController::class, 'recuperarContraseña']);
Route::post('/validate-email', [TokenController::class, 'validateEmail']);
Route::post('/validate-codigo', [VerificationController::class, 'verifyCode']);

// Ruta autenticación 
Route::post('/createuser', [AuthController::class, 'createuser']);
Route::post('/eliminar-cuenta', [AuthController::class, 'eliminarcuenta']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/cambio-password', [AuthController::class, 'cambiarContraseña']);


//Rutas de viajes controller 
/*
Route::post('/actualizar-ubicacion', [ViajesController::class, 'actualizarubicacion']);
Route::post('/add-token', [ViajesController::class, 'addtoken']);
Route::post('/calificacion-de-ruta', [ViajesController::class, 'calificacionderuta']);
Route::post('/confirmar-direccion', [ViajesController::class, 'confirmardireccion']);
Route::post('/consultar-codigo', [ViajesController::class, 'consultarcodigo']);
Route::post('/consultar-tarjetas', [ViajesController::class, 'consultartarjetas']);
Route::post('/calcular-tarifa-servicio', [ViajesController::class, 'calculartarifaservicio']);
Route::post('/cambiar-idioma', [ViajesController::class, 'cambiaridioma']);
Route::post('/contactos', [ViajesController::class, 'contactos']);
Route::post('/editar-datos', [ViajesController::class, 'editardatos']);
Route::post('/editar-lugar', [ViajesController::class, 'editarlugar']);
Route::post('/eliminar-lugar', [ViajesController::class, 'eliminarlugar']);
Route::post('/eliminar-token', [ViajesController::class, 'eliminartoken']);
Route::post('/guardar-id-registration', [ViajesController::class, 'guardaridregistration']);
Route::post('/guardar-lugar', [ViajesController::class, 'guardarlugar']);
Route::post('/listar-idiomas', [ViajesController::class, 'listaridiomas']);
Route::post('/listar-lugares', [ViajesController::class, 'listarlugares']);
Route::post('/misviajes', [ViajesController::class, 'misviajes']);
Route::post('/obtener-usuario', [ViajesController::class, 'obtenerusuario']);
Route::post('/proximas-rutas', [ViajesController::class, 'proximasrutas']);
Route::post('/reestablecer-contrasena-cliente', [ViajesController::class, 'reestablecercontrasenacliente']);
Route::post('/reintentar-pago', [ViajesController::class, 'reintentarpago']);
Route::post('/servicio-activo', [ViajesController::class, 'servicioactivo']);
Route::post('/servicios-pedidos', [ViajesController::class, 'serviciospedidos']);
*/


// Rutas de viajes controller 
Route::post('/listar-viajes-generales', [Viajescontroller::class, 'listarViajesGenerales']);


// Rutas de whatsapp
Route::post('/enviar-wapp', [WhatsappController::class, 'enviarWhatsApp']);