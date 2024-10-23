<?php

use App\Http\Controllers\ProveedoresMailController\ProveedorMailController;

use App\Http\Controllers\Viajes;
use App\Http\Controllers\viajesEnGeneral\ViajesEnGeneralController;
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



// Rutas de viajes controller requesttrips
Route::post('/consultar-cliente', [ViajesEnGeneralController::class, 'consultclient']);
Route::post('/listar-viajes-generales', [ViajesEnGeneralController::class, 'listarViajesGenerales']);
Route::post('/crear-solicitud-viaje', [ViajesEnGeneralController::class, 'requesttrips']);


// Rutas de whatsapp
Route::post('/enviar-wapp', [WhatsappController::class, 'enviarWhatsApp']);