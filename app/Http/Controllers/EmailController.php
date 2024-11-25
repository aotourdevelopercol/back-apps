<?php

    namespace App\Http\Controllers;

    use App\Mail\AsignacionDeViaje;
    use App\Mail\EsperarEjecutivo;
    use App\Mail\NuevoViaje;
    use App\Mail\ProveedoresDocumentosAprobadosC;
    use App\Mail\ProveedoresDocumentosAprobados;
    use App\Mail\ProveedoresRevision;
    use App\Mail\InscripcionProveedoresEmails;
    use App\Mail\ProveedoresDocumentosActualizados;
    use App\Mail\ProveedoresCapacitar;
    use App\Mail\ProveedoresBienvenido;
    use App\Mail\ProveedoresAviso;
    use App\Mail\ProveedoresDocumentosRechazados;
    use App\Mail\ProveedorProvisional;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Log; 

    class EmailController extends Controller
    {
        public function sendEmail(Request $request)
        {
            Log::info('Solicitud recibida: ' . $request->fullUrl());
            Log::info('Solicitud completa:', ['request' => $request->all()]);
            // Tipo de plantilla recibido desde el request
            $validated = $request->validate([
                'email' => 'required|array',               // Cambia `email` para aceptar un array
                'email.*' => 'email', 
                'templateType' =>'required|string',
                'token' => 'nullable|string',
                // Los datos almacenados en data corresponden a los datos que se enviarán en el correo para diferentes plantillas
                'data' =>'nullable|array',
                'data.nombre' =>'nullable|string',
                'data.totalConductores' =>'nullable|integer',
                'data.totalVehiculos' =>'nullable|integer',
                'data.asunto' =>'nullable|string',
                'data.texto' =>'nullable|string',
                'data.motivo' =>'nullable|integer',
                'data.link' =>'nullable|integer',
                'data.total' =>'nullable|integer',
                'data.titulo' =>'nullable|string',
                'data.conductor' =>'nullable|string',
                'data.placa' =>'nullable|string',
                'data.telefonoConductor' =>'nullable|string',
                'data.fecha' =>'nullable|string',
                'data.hora' =>'nullable|string',
                'data.destino' =>'nullable|string',
                'data.origen' =>'nullable|string',
                'data.ruta' =>'nullable|array',     
                'data.motivo2' =>'nullable|string',
            ]);

            Log::info('Inicio - Solicitud recibida: ' . json_encode($request->all()));
            
      

            
            // Seleccionar plantilla y asunto basados en el tipo de plantilla
            switch ($validated['templateType']) {
                
                case 'inscripcion_proveedores':
                
                    try {
                        Mail::to($validated['email'])->send(new InscripcionProveedoresEmails(
                            $validated['data']['nombre'],
                            $validated['data']['totalConductores'] ,
                            $validated['data']['totalVehiculos']
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }
                    
                    break;

                case 'proveedores_aviso':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresAviso(
                            $validated['data']['nombre'],
                            $validated['data']['totalConductores'],
                            $validated['data']['totalVehiculos']
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }
                    
                    break;

                case 'proveedores_bienvenido':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresBienvenido);
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;
                
                case 'proveedores_capacitacion':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresCapacitar(
                            $validated['data']['asunto']
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;
                
                case 'proveedores_documentos_actualizados':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresDocumentosActualizados(
                            $validated['data']['nombre']
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                case 'proveedores_documentos_aprobados_c':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresDocumentosAprobadosC(
                            $validated['data']['titulo'],
                            $validated['data']['texto'],
                            $validated['data']['motivo'],
                            $validated['data']['link'],
                            
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                case 'proveedores_documentos_aprobados':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresDocumentosAprobados(
                            $validated['data']['titulo'],
                            $validated['data']['texto'],
                            $validated['data']['link'],
                            
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                case 'proveedores_documentos_rechazados':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresDocumentosRechazados(
                            $validated['data']['total'],
                            $validated['data']['link'],    
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                case 'proveedores_revision':
                    try {
                        Mail::to($validated['email'])->send(new ProveedoresRevision(
                            $validated['data']['nombre']
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;


                //
                // Correos para viajes para pasajeros - Asginación de viaje, Espera de ejecutivo (Esperar al pasajero), Nuevo viaje
                //
                case 'nuevo_viaje':
                    try {
                        Mail::to($validated['email'])->send(new NuevoViaje(
                          $validated['token'],
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                case 'esperar_ejecutivo':
                    try {
                        Mail::to($validated['email'])->send(new EsperarEjecutivo(
                            $validated['token' ]
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                case 'asignacion_de_viaje':
                    try {
                        Mail::to($validated['email'])->send(new AsignacionDeViaje(
                            $validated['data']['nombre' ],
                            $validated['data']['conductor'],
                            $validated['data']['placa'],
                            $validated['data']['telefonoConductor'],   
                            $validated['data']['origen'],
                            $validated['data']['destino'],
                            $validated['data']['fecha'],
                            $validated['data']['hora'],
                            $validated['token'],
                            
                            
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;


                case 'provi_provee' : 
                    try {
                        Mail::to($validated['email'])->send(new ProveedorProvisional(
                            $validated['data']['ruta'],
                            $validated['data']['motivo2']
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error al enviar correo: '. $th);
                    }

                    break;

                default:
                    return response()->json(['error' => 'Tipo de plantilla no válido'], 400);
            }

            return response()->json(['message' => 'Correo enviado con éxito']);
        }
    }