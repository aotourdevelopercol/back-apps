<?php

namespace App\Http\Controllers;

use App\Models\estados;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use App\Models\Tipo;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\ViajesU;
use Response;
use Auth;


class ViajeController extends Controller
{
    //Calificación del viaje
    public function calificarViaje(Request $request)
    {

        try {
            // Validar la solicitud
            $validatedData = $request->validate([
                'fk_viaje' => 'required|integer',
                'fk_user' => 'required|integer',
                'calificacion' => 'required|integer|min:1|max:5', // Asumiendo que la calificación es de 1 a 5
                'comentario' => 'required|string',
            ]);

            // Insertar el registro
            Calificacion::create([
                'fk_viaje' => $validatedData['fk_viaje'],
                'fk_user' => $validatedData['fk_user'],
                'calificacion' => $validatedData['calificacion'],
                'comentario' => $validatedData['comentario'],
            ]);

            return response()->json(['message' => 'Calificación registrada con éxito']);

        } catch (\Throwable $th) {
            \Log::error('Error al calificar viaje: ' . $th->getMessage());
        }

    }

    // Consulta de clientes
    public function consultclient(Request $request)
    {

        $query = "SELECT * FROM info_adicional_viajes  WHERE fk_centrodecosto = " . $request->cliente . "";

        $consulta = DB::select($query);

        return Response::json([
            'response' => true,
            'consulta' => $consulta
        ]);
    }

    // PARA LISTAR LOS ESTADOS PASANDO EL ESTADO MAESTRO
    public function listarEstadosPorMaestro(Request $request)
    {

        $validateData = $request->validate([
            'codigo_maestro' => ['required', 'string'],
        ]);

        try {
            $estados = estados::getEstados($validateData['codigo_maestro']);
            return Response::json([
                'response' => true,
                'estados' => $estados
            ]);

        } catch (\Throwable $th) {
            \Log::error('Error al listar estados por maestro: ' . $th->getMessage());
        }

    }

    // Listar tipos de viaje por codigo maestro
    public function listarTiposDeViaje(Request $request)
    {
        $validateData = $request->validate([
            'codigo_maestro' => ['required', 'string'],
        ]);

        $tipoViaje = Tipo::obtenerTiposPorEstadoMaestro($validateData['codigo_maestro']);

        return Response::json([
            'response' => true,
            'tiposEstado' => $tipoViaje
        ]);
    }

    // Consulta de viajes
    function listarViajesGenerales(Request $request)
    {

        $validatedData = $request->validate([
            'fecha' => ['nullable', 'string'],
            'app_user_id' => ['nullable', 'number'],
            'codigo_viaje' => ['nullable', 'string'],
            'estado_viaje' => ['nullable', 'array'],
        ]);

        $user = User::where('id', $validatedData['app_user_id'])->first();

        try {
            $query = "SELECT
                 v.id,
                 v.fecha_viaje,
                 v.hora_viaje,
                 v.cantidad as cantidad_pasajeros,
                 e.id AS id_estado,
                 e.codigo AS codigo_estado,
                 e.nombre AS nombre_estado,
                 t.id AS id_tipo_ruta,
                 t.codigo AS codigo_tipo_ruta,
                 t.nombre AS nombre_tipo_ruta,
                 v2.placa,
                 v2.modelo,
                 v2.marca,
                 v2.color,
                 e2.codigo as codigo_tipo_vehiculo,
                 e2.nombre as nombre_tipo_vehiculo,
                 UPPER(CONCAT(c2.primer_nombre, ' ', c2.primer_apellido)) AS nombre_completo,
                 JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) AS destinos
             FROM
                 viajes v
             LEFT JOIN centrosdecosto c ON c.id = v.fk_centrodecosto
             LEFT JOIN conductores c2 ON c2.id = v.fk_conductor
             LEFT JOIN vehiculos v2 ON v2.id = v.fk_vehiculo
             LEFT JOIN destinos d ON d.fk_viaje = v.id
             LEFT JOIN tipos t ON t.id = v.tipo_traslado
             LEFT JOIN estados e ON e.id = v.fk_estado
             LEFT JOIN estados e2 ON e2.id = v2.fk_tipo_vehiculo
             LEFT JOIN pasajeros_rutas_qr prq ON prq.fk_viaje = v.id
             LEFT JOIN pasajeros_ejecutivos pe ON pe.fk_viaje = v.id
             WHERE
                 v.estado_eliminacion IS NULL
                 AND (
                     (prq.id_empleado = ? OR ? IS NULL)
                     OR
                     (v.app_user_id = ? OR ? IS NULL)
                 )
                 AND (t.codigo = ? or ? is null)";

            $params = [
                $user->codigo_empleado,
                $user->codigo_empleado, // Este es para la comparación "OR NULL"
                $validatedData['app_user_id'],
                $validatedData['app_user_id'], // Este es para la comparación "OR NULL"
                $validatedData['codigo_viaje'] ?? null,
                $validatedData['codigo_viaje'] ?? null, // Este es para la comparación "OR NULL"
            ];

            if (!empty($validatedData['fecha'])) {
                $query .= " AND v.fecha_viaje = ?";
                $params = array_merge($params, [$validatedData['fecha']]); // Wrap in array
            }

            // Comprobar si hay múltiples estados de viaje
            if (!empty($validatedData['estado_viaje'])) {
                $placeholders = implode(',', array_fill(0, count($validatedData['estado_viaje']), '?'));
                $query .= " AND e.codigo IN ($placeholders)";
                $params = array_merge($params, $validatedData['estado_viaje']);
            }

            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17;";

            // Aqui se ejecutaria la consulta y se obtendrian los resultados.
            // En este caso, se retornan los resultados de la consulta.
            $results = DB::select($query, $params);

            return Response::json([
                'user' => $user->codigo_empleado,
                'response' => true,
                'listado' => $results,
            ]);
        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes generales: ' . $th->getMessage());
        }

    }

    // formulario de solicitud de viaje ejecutivo
    public function requesttrips(Request $request)
    {

        try {
            $viajes = $request->viajes;
            $fk_sede = 1;
            $centro = 489; // Buscar por el id de user que manda para poner el id del centrodecosto_id

            if (count($viajes) > 1) {
                $tipo = 2;
            } else {
                $tipo = 1;
            }

            for ($a = 0; $a < count($viajes); $a++) {
                $viaje = new ViajesU;
                $viaje->fecha_solicitud = date('Y-m-d');
                $viaje->fecha = $viajes[$a]['fecha'];
                $viaje->hora = $viajes[$a]['hora'];
                $viaje->desde = $viajes[$a]['desde'] ?? null;
                $viaje->hasta = $viajes[$a]['hasta'] ?? null;
                $viaje->detalles = $viajes[$a]['detalles'] ?? null;
                $viaje->fk_sede = 1;
                $viaje->fk_centrodecosto = 489;
                $viaje->fk_ciudad = 2;
                $viaje->vuelo = $viajes[$a]['vuelo'] ?? null;
                $viaje->tipo_solicitud = $tipo;
                $viaje->created_at = date('Y-m-d H:i:s');
                $viaje->creado_por = 2;
                $viaje->info_adicional = null;
                $viaje->app_user = $request->id_user;
                $viaje->save();


                $pax = DB::table('viajes_upnet_pasajeros')
                    ->insert([
                        'nombre' => 'Omar Mugno',
                        'celular' => '3106560877',
                        'correo' => 'Omar.mugno@gmail.com',
                        'fk_viaje_upnet' => $viaje->id
                    ]);
            }

            return Response::json([
                'response' => true,
                'codigo' => 'NOPROMAN',
            ]);

        } catch (\Throwable $th) {
            \Log::error('Error al solicitar viaje ejecutivo: ' . $th->getMessage());
            return response(json_encode([
                'response' => false,
                'codigo' => 'FAIL',
            ]));
        }



    }
}
