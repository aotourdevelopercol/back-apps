<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        Log::info('Solicitud recibida: ' . $request->fullUrl());
        Log::info('Solicitud completa:', ['request' => $request->all()]);

        // Validar datos de entrada
        $validated = $request->validate([
            'email' => 'required|array',
            'email.*' => 'email',
            'templateType' => 'required|string',
            'token' => 'nullable|string',
            'data' => 'nullable|array',
        ]);

        // Mapeo de plantillas a clases de mailable
        $templateMap = [
            'inscripcion_proveedores' => \App\Mail\InscripcionProveedoresEmails::class,
            'proveedores_aviso' => \App\Mail\ProveedoresAviso::class,
            'proveedores_bienvenido' => \App\Mail\ProveedoresBienvenido::class,
            'proveedores_capacitacion' => \App\Mail\ProveedoresCapacitar::class,
            'proveedores_documentos_actualizados' => \App\Mail\ProveedoresDocumentosActualizados::class,
            'proveedores_documentos_aprobados_c' => \App\Mail\ProveedoresDocumentosAprobadosC::class,
            'proveedores_documentos_aprobados' => \App\Mail\ProveedoresDocumentosAprobados::class,
            'proveedores_documentos_rechazados' => \App\Mail\ProveedoresDocumentosRechazados::class,
            'proveedores_revision' => \App\Mail\ProveedoresRevision::class,
            'viaje_entendido' => \App\Mail\ViajeEntendido::class,
            'esperar_ejecutivo' => \App\Mail\EsperarEjecutivo::class,
            'asignacion_de_viaje' => \App\Mail\AsignacionDeViaje::class,
            'finalizar_viaje' => \App\Mail\FinalizarViaje::class,
            'fin_viaje' => \App\Mail\ViajeFinalizado::class,
            'cancelar_viaje' => \App\Mail\CancelarViaje::class,
            'modificacion_viaje' => \App\Mail\ModificacionViaje::class,
            'confirmar_viaje' => \App\Mail\ConfirmarViaje::class,
            'inicio_viaje' => \App\Mail\InicioViaje::class,
            'provi_provee' => \App\Mail\ProveedorProvisional::class,
            'cuenta_cobro_habilitada' => \App\Mail\CuentaDeCobroHabilitada::class,
            'cuenta_cobro_cerrada' => \App\Mail\CuentaDeCobroCerrada::class,
            'cuenta_cobro_corregir' => \App\Mail\CuentaDeCobroCorregir::class,
            'cuenta_cobro_radicada' => \App\Mail\CuentaDeCobroRadicada::class,
            'forgot_password' => \App\Mail\ContraseñaOlvidada::class,
            'nuevo_usuario' => \App\Mail\NuevosUsuariosEmail::class,
            'pago_proveedores' => \App\Mail\PagoProveedores::class
        ];
       

        // Verificar si la plantilla existe en el mapa
        if (!isset($templateMap[$validated['templateType']])) {
            return response()->json(['error' => 'Tipo de plantilla no válido'], 400);
        }

        try {
            $emails = $validated['email'];
            $emailClass = $templateMap[$validated['templateType']];
            $emailData = $validated['data'] ?? [];
            $token = $validated['token'] ?? null;

            Log::info('Clase de correo seleccionada: ' . $emailClass);

            // Reflexión para obtener parámetros del constructor del mailable
            $reflection = new \ReflectionClass($emailClass);
            $parameters = $reflection->getConstructor()?->getParameters() ?? [];

            // Construir los argumentos para el mailable
            $args = [];
            foreach ($parameters as $param) {
                $paramName = $param->getName();
                if (isset($emailData[$paramName])) {
                    $args[] = $emailData[$paramName];
                } elseif ($paramName === 'token') {
                    $args[] = $token;
                } else {
                    $args[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                }
            }

            Log::info('argumentos: ' . $args);

            // Función para enviar el correo
            $sendMail = function ($email) use ($reflection, $args) {
                $mailable = count($args) > 0
                    ? $reflection->newInstanceArgs($args)  // Si requiere parámetros
                    : new ($reflection->getName());  // Si no requiere parámetros

                Mail::to($email)->later(now()->addSeconds(10), $mailable);
            };

            Log::info('?????: ' . $sendMail);

            // Enviar correos: uno o múltiples
            if (count($emails) > 1) {
                Log::info("Enviando múltiples correos...");
                foreach ($emails as $email) {
                    $sendMail($email);
                }
            } else {
                Log::info("Enviando un solo correo...");
                $sendMail($emails[0]);
            }
        } catch (\Throwable $th) {
            Log::error("Error al enviar correo: " . $th->getMessage());
            return response()->json(['error' => 'Error al enviar correo. Revisa logs.'], 500);
        }

        return response()->json(['message' => 'Correo enviado con éxito']);
    }
}
