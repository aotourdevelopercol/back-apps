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

        $validated = $request->validate([
            'email' => 'required|array',
            'email.*' => 'email', 
            'templateType' =>'required|string',
            'token' => 'nullable|string',
            'data' =>'nullable|array',
        ]);

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
        ];

        if (!isset($templateMap[$validated['templateType']])) {
            return response()->json(['error' => 'Tipo de plantilla no válido'], 400);
        }

        try {
            $emails = $validated['email'];
            $emailClass = $templateMap[$validated['templateType']];
        
            // Extraemos los parámetros posibles
            $emailData = $validated['data'] ?? [];
            $token = $validated['token'] ?? null;
        
            // Obtener el número de argumentos que espera la clase
            $reflection = new \ReflectionClass($emailClass);
            $parameters = $reflection->getConstructor()->getParameters();
        
            // Construimos los argumentos dinámicamente según lo que necesite la clase
            $args = [];
            foreach ($parameters as $param) {
                if ($param->getName() === 'data') {
                    $args[] = $emailData;
                } elseif ($param->getName() === 'token') {
                    $args[] = $token;
                }
            }
        
            if (is_array($emails) && count($emails) > 1) {
                Log::info("Estoy enviando muchos correos");
                foreach ($emails as $email) {
                    Mail::to($email)->later(
                        now()->addSeconds(10),
                        $reflection->newInstanceArgs($args) // Crear instancia con los argumentos correctos
                    );
                }
            } else {
                $email = is_array($emails) ? $emails[0] : $emails;
                Log::info("Estoy enviando un solo correo");
                Mail::to($email)->later(
                    now()->addSeconds(10),
                    $reflection->newInstanceArgs($args) // Crear instancia con los argumentos correctos
                );
            }
        } catch (\Throwable $th) {
            Log::error("Error al enviar correo: " . $th->getMessage());
        }
        

        return response()->json(['message' => 'Correo enviado con éxito']);
    }
}
