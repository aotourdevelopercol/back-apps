<?php

    namespace App\Http\Controllers;

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
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Log; 

    class EmailController extends Controller
    {
        public function sendEmail(Request $request)
        {
            // Tipo de plantilla recibido desde el request
            $validated = $request->validate([
                'email' => 'required|array',               // Cambia `email` para aceptar un array
                'email.*' => 'email', 
                'templateType' =>'required|string',
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
            ]);


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

                case 'nuevo_viaje':
                    try {
                        Mail::to($validated['email'])->send(new NuevoViaje(
                          
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