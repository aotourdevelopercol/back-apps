<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ConsultaDocumentosVehiculos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consulta-documentos-vehiculos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta documentos de vehículos y verifica si están vencidos o próximos a vencer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       
        try {
            // Ejecutar la consulta
            $vehiculos = DB::select("
                SELECT
                    v.id, 
                    v.placa,
                    v.fecha_vigencia_operacion, 
                    v.fecha_vigencia_soat, 
                    v.fecha_vigencia_tecnomecanica, 
                    v.poliza_todo_riesgo, 
                    v.poliza_contractual, 
                    v.poliza_extracontractual 
                FROM proveedores p 
                INNER JOIN vehiculos v ON p.id = v.fk_proveedor 
                WHERE p.fk_tipo_afiliado = 14 
                  AND p.fk_estado = 50 
                  AND v.fk_estado = 50
            ");

            $resultado = [];
            $vehiculosConAlertas = [];

            // Verificar si hay vehículos y agrupar resultados
            if (!empty($vehiculos)) {
                foreach ($vehiculos as $vehiculo) {
                    $detallesVencidos = [];
                    $detallesProximos = [];

                    // Procesar cada fecha para determinar el estado de los documentos
                    $documentos = [
                        'fecha_vigencia_operacion' => $vehiculo->fecha_vigencia_operacion,
                        'fecha_vigencia_soat' => $vehiculo->fecha_vigencia_soat,
                        'fecha_vigencia_tecnomecanica' => $vehiculo->fecha_vigencia_tecnomecanica,
                    ];

                    foreach ($documentos as $tipoDocumento => $fechaVigencia) {
                        $fechaObjetivo = Carbon::createFromFormat('Y-m-d', $fechaVigencia);
                        $fechaActual = Carbon::now();

                        $diasFaltantes = $fechaActual->floatDiffInDays($fechaObjetivo, false); // Puede ser negativo si está vencido
                        $diasFaltantesRedondeados = round($diasFaltantes);
                        if ($diasFaltantesRedondeados < 0) {
                            $detallesVencidos[] = [
                                'documento' => $tipoDocumento,
                                'dias_vencidos' => abs($diasFaltantesRedondeados)
                            ];
                        } elseif ($diasFaltantesRedondeados <= 7 && $diasFaltantesRedondeados >= 0) {
                            $detallesProximos[] = [
                                'documento' => $tipoDocumento,
                                'dias_restantes' => $diasFaltantesRedondeados
                            ];
                        }
                    }

                    if (!empty($detallesVencidos) || !empty($detallesProximos)) {
                        
                        $vehiculosConAlertas[] = [
                            'vehiculo_id' => $vehiculo->placa,
                            'vencidos' => $detallesVencidos,
                            'proximos_a_vencer' => $detallesProximos
                        ];
                    }

                    // Agregar los detalles al resultado agrupados por vehículo
                    $resultado[] = [
                        'vehiculo_id' => $vehiculo->placa,
                        'vencidos' => $detallesVencidos,
                        'proximos_a_vencer' => $detallesProximos
                    ];
                }

                // Enviar correo si hay vehículos con alertas
                if (!empty($vehiculosConAlertas)) {
                    $this->enviarCorreo($vehiculosConAlertas);
                } else {
                    $this->info('No se encontraron documentos vencidos o próximos a vencer.');
                }
            } else {
                $this->info('No se encontraron documentos de vehículos que coincidan con los criterios.');
            }
        } catch (\Exception $e) {
            $this->error('Ocurrió un error al ejecutar la consulta: ' . $e->getMessage());
        }
    }

    /**
     * Envía un correo electrónico con los detalles de los documentos vencidos o próximos a vencer.
     *
     * @param array $vehiculosConAlertas
     */
    private function enviarCorreo(array $vehiculosConAlertas)
    {
        $correoDestino = 'teosio97@gmail.com'; // Cambia esto por el correo real

        $data = [
            'vehiculos' => $vehiculosConAlertas
        ];


        // Enviar el correo
        Mail::send('emails.alerta_documentos', $data, function ($message) use ($correoDestino) {
            $message->to($correoDestino)
                    ->subject('Alertas de Documentos Vencidos o Próximos a Vencer');
        });

    }
}