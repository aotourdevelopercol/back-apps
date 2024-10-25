<?php

namespace App\Http\Controllers;

use App\Models\estados;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use App\Models\Tipo;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\ViajesU;
use App\Models\User;
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

    // Listar Viajes activos
    public function listarViajesActivos(Request $request)
    {
        $validateData = $request->validate([
            'app_user_id' => ['required', 'integer'],
        ]);

        try {

            $query = "SELECT
            v.id,
            v.fecha_viaje,
            v.hora_viaje,
            v.recoger_pasajero,
	        v.codigo_viaje,
            t.id as id_tipo,
            t.codigo as codigo_tipo,
            t.nombre as nombre_tipo,
            e.id as id_estado,
            e.codigo as codigo_estado,
            e.nombre as nombre_estado,
            pe.id,
            pe.nombre AS nombre_pasajero,
            v2.placa,
            v2.modelo,
            v2.marca,
            v2.color,
            c2.foto as foto_conductor,
            e2.id as id_tipo_vehiculo,
            e2.codigo as codigo_tipo_vehiculo,
            e2.nombre as nombre_tipo_vehiculo,
            UPPER(CONCAT(c2.primer_nombre, ' ', c2.primer_apellido)) AS nombre_conductor,
            JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) AS destinos
        from viajes v
        left join vehiculos v2 on v2.id = v.fk_vehiculo
        left join conductores c2 on c2.id = v.fk_conductor
        left join pasajeros_ejecutivos pe on pe.fk_viaje = v.id
        left join estados e on e.id = v.fk_estado
        left join destinos d on d.fk_viaje = v.id
        left join tipos t on t.id = v.tipo_traslado
        left join estados e2 on e2.id = v2.fk_tipo_vehiculo
        where v.app_user_id = ? and pe.app_user_id = ? and e.codigo = 'INICIADO'
        GROUP BY
            v.id,
            v.fecha_viaje,
            v.hora_viaje,
            v.recoger_pasajero,
	        v.codigo_viaje,
            t.id,
            t.codigo,
            t.nombre,
            e.id,
            e.codigo,
            e.nombre,
            pe.id,
            pe.nombre,
            v2.placa,
            v2.modelo,
            v2.marca,
            v2.color,
            c2.foto,
            t2.codigo,
            t2.nombre,
            nombre_conductor;";

            $params = [$validateData['app_user_id'], $validateData['app_user_id']];

            $results = DB::select($query, $params);

            return Response::json([
                'response' => true,
                'listado' => $results[0],
            ]);

        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes activos: ' . $th->getMessage());
        }
    }


    // Consulta de viajes
    function listarViajesGenerales(Request $request)
    {

        $validatedData = $request->validate([
            'fecha' => ['nullable', 'string'],
            'app_user_id' => ['nullable', 'string'],
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
                     prq.id_empleado = ?
                     OR
                     v.app_user_id = ?)
                 AND (t.codigo = ? or ? is null)";

            $params = [
                $user->codigo_empleado,
                $validatedData['app_user_id'],
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

            $listaVijesPendientes = $this->listarViajesPendientesRutas($user->codigo_empleado, $validatedData['fecha']);
            $listaVijesPendientesEjecutivos = $this->listarViajesPendientesEjecutivos($validatedData['app_user_id'], $validatedData['fecha']);

            // Combina todos los resultados en un solo array si existen datos
            if (!empty($listaVijesPendientes)) {
                $results = array_merge($results, $listaVijesPendientes);
            }
            if (!empty($listaVijesPendientesEjecutivos)) {
                $results = array_merge($results, $listaVijesPendientesEjecutivos);
            }

            return Response::json([
                'response' => true,
                'listado' => $results,
            ]);
        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes generales: ' . $th->getMessage());
        }

    }


    // Consulta de viajes pendientes Rutas
    private function listarViajesPendientesRutas($appUserId, $fecha)
    {

        try {

            $query = "SELECT
                        rs.id,
                        rs.fecha,
                        rs.hora,
                        null as cantidad_pasajeros,
                        81 AS id_estado,
                        'NOPROMAN' AS codigo_estado,
                        'NO PROGRAMADO' AS nombre_estado,
                        70 AS id_tipo_ruta,
                        'RUTA' AS codigo_tipo_ruta,
                        'RUTA' AS nombre_tipo_ruta,
                        null as placa,
                        null as modelo,
                        null as marca,
                        null as color,
                        null as codigo_tipo_vehiculo,
                        null as nombre_tipo_vehiculo,
                        null AS nombre_completo,
                        null AS destinos
                        from rutas_solicitadas rs
                        left join rutas_solicitadas_pasajeros rsp on rsp.fk_rutas_solicitadas = rs.id
                        where rsp.empleado_id = ?";


            $params = [
                $appUserId,
            ];

            if (!empty($fecha)) {
                $query .= " AND rs.fecha = ?";
                $params = array_merge($params, [$fecha]); // Wrap in array
            }

            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17;";

            $results = DB::select($query, $params);

            return $results;

        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes pendientes: ' . $th->getMessage());
        }

    }


    // Consulta de viajes pendientes ejecutivos
    public function listarViajesPendientesEjecutivos($appUserId, $fecha)
    {
        try {
            $query = "SELECT
                        vu.id,
                        vu.fecha,
                        vu.hora,
                        null as cantidad_pasajeros,
                        81 AS id_estado,
                        'NOPROMAN' AS codigo_estado,
                        'NO PROGRAMADO' AS nombre_estado,
                        70 AS id_tipo_ruta,
                        'RUTA' AS codigo_tipo_ruta,
                        'RUTA' AS nombre_tipo_ruta,
                        null as placa,
                        null as modelo,
                        null as marca,
                        null as color,
                        null as codigo_tipo_vehiculo,
                        null as nombre_tipo_vehiculo,
                        null AS nombre_completo,
                        null AS destinos
                        from viajes_upnet vu
                        left join viajes_upnet_pasajeros vup on vup.fk_viaje_upnet = vu.id
                        where vu.app_user = ?";

            $params = [
                $appUserId,
            ];


            if (!empty($fecha)) {
                $query .= " AND vu.fecha = ?";
                $params = array_merge($params, [$fecha]); // Wrap in array
            }
            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17;";

            $results = DB::select($query, $params);

            Log::info('Viajes pendientes ejecutivos: ' . json_encode($query));
            // Registrar la consulta y los parámetros de forma separada
            Log::info('Consulta SQL de viajes pendientes ejecutivos: ', ['query' => $query, 'params' => $params]);


            return $results;

        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes pendientes ejecutivos: ' . $th->getMessage());
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
