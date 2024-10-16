<?php

use Illuminate\Http\Request;
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




// Rutas para correos de proveedores 
Route::post('/inscripcion', [InscripcionProveedoresController::class, 'enviarCorreo']);
Route::post('/revision', [ProveedoresRevisionController :: class, 'enviarCorreo']);
Route::post('/documentosRechazados', [ProveedoresDocumentosRechazadosController :: class, 'enviarCorreo']);
Route::post('/documentoAprobados', [ProveedoresDocumentosAprobadosController :: class, 'enviarCorreo']);
Route::post('/documentosAprovadosC', [ProveedoresDocumentosAprobadosCController :: class, 'enviarCorreo']);
Route::post('/documentosActualizados', [ProveedoresDocumentosActualizadosController :: class, 'enviarCorreo']);
Route::post('/capacitar', [ProveedoresCapacitarController :: class, 'enviarCorreo']);
Route::post('/bienvenido', [ProveedoresBienvenidoController :: class, 'enviarCorreo']);
Route::post('/aviso', [ProveedoresAvisoController :: class, 'enviarCorreo']);

// Ruta de la validación del correo electronico para la app
Route::post('/validate-email', [TokenController::class, 'validateEmail']);
// 
Route::post('/validate-codigo', [VerificationController::class, 'verifyCode']);