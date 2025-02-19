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
use Carbon\Carbon;


class ViajeController extends Controller
{
    //Calificación del viaje
    public function calificarViaje(Request $request)
    {

        try {
            // Validar la solicitud
            $validatedData = $request->validate([
                'fk_viaje' => 'required|integer',
                'fk_user' => 'nullable|integer',
                'pasajero_ejecutivo_link' => 'nullable|integer',
                'pasajero_ruta_link' => 'nullable|integer',
                'calificacion' => 'required|numeric', // Asumiendo que la calificación es de 1 a 5
                'comentario' => 'nullable|string',
            ]);

            // Insertar el registro
            Calificacion::create([
                'fk_viaje' => $validatedData['fk_viaje'],
                'fk_user' => $validatedData['fk_user'] ?? null,
                'pasajero_ejecutivo_link' => $validatedData['pasajero_ejecutivo_link'] ?? null,
                'pasajero_ruta_link' => $validatedData['pasajero_ruta_link'] ?? null,
                'calificacion' => $validatedData['calificacion'] ?? 0.0,
                'comentario' => $validatedData['comentario'] ?? null,
            ]);

            return Response::json([
                'response' => true,
                'message' => 'Calificación registrada con éxito'
            ]);

        } catch (\Throwable $th) {
            \Log::error('Error al calificar viaje: ' . $th->getMessage());
            return Response::json([
                'response' => false,
                'message' => 'Calificación no registrada con éxito'
            ]);
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

            $codigoEmpleado = DB::table('users as u')
                ->join('empleados_clientes as ec', 'ec.id', '=', 'u.id_empleado_cliente')
                ->where('u.id', '=', $validateData['app_user_id'])
                ->select('ec.codigo_empleado')
                ->first();


            $query = "SELECT
                v.id,
                v.fecha_viaje,
                v.hora_viaje,
                v.recoger_pasajero,
                v.codigo_viaje,
                t.id as id_tipo,
                t.codigo as codigo_tipo,
                t.nombre as nombre_tipo,
                t3.id as id_tipo_ruta,
                t3.codigo as codigo_tipo_ruta,
                t3.nombre as nombre_tipo_ruta,
                e.id as id_estado,
                e.codigo as codigo_estado,
                e.nombre as nombre_estado,
                (case when pe.id is null then prq.id_empleado else pe.id end) as id_pasajero,
                (case when pe.nombre is null then prq.nombre else pe.nombre end) as nombre_pasajero,
                t4.id as id_estado_pasajero_ruta,
                t4.codigo as codigo_estado_pasajero_ruta,
                t4.nombre as nombre_estado_pasajero_ruta,
                prq.recoger_a as recoger_ruta_pasajero,
                prq.codigo_viaje as codigo_ruta_pasajero,
                v2.placa,
                v2.modelo,
                v2.marca,
                v2.color,
                c2.foto as foto_conductor,
                t2.id as id_tipo_vehiculo,
                t2.codigo as codigo_tipo_vehiculo,
                t2.nombre as nombre_tipo_vehiculo,
                UPPER(CONCAT(c2.primer_nombre, ' ', c2.primer_apellido)) AS nombre_conductor,
                JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) AS destinos
            from viajes v
            left join vehiculos v2 on v2.id = v.fk_vehiculo
            left join conductores c2 on c2.id = v.fk_conductor
            left join pasajeros_ejecutivos pe on pe.fk_viaje = v.id
            left join pasajeros_rutas_qr prq on prq.fk_viaje = v.id
            left join estados e on e.id = v.fk_estado
            left join destinos d on d.fk_viaje = v.id
            left join tipos t on t.id = v.tipo_traslado
            left join tipos t2 on t2.id = v2.fk_tipo_vehiculo
            left join tipos t3 on t3.id = v.tipo_ruta
            left join tipos t4 on t4.id = prq.estado_ruta
            where (pe.app_user_id = ? or prq.id_empleado = ?) and e.codigo = 'INICIADO'
            GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30
            LIMIT 1;";

            $params = [$validateData['app_user_id'], $codigoEmpleado->codigo_empleado];

            $results = DB::select($query, $params);

            Log::info($codigoEmpleado->codigo_empleado . " - " . $validateData['app_user_id']);

            $consulta = "SELECT
                    v.id
                FROM
                    viajes v
                LEFT JOIN pasajeros_rutas_qr prq ON
                    prq.fk_viaje = v.id
                LEFT JOIN pasajeros_ejecutivos pe ON
                    pe.fk_viaje = v.id
                WHERE v.fecha_viaje = ? AND v.fk_estado in (59, 60) AND
                    v.estado_eliminacion is null
                    AND (
                        prq.id_empleado = ?
                        OR pe.app_user_id = ?
                );";

            $fechaHoy = Carbon::now('America/Bogota')->format('Y-m-d');
            $paramsVia = [$fechaHoy, $codigoEmpleado->codigo_empleado, $validateData['app_user_id']];
            $viajes = DB::select($consulta, $paramsVia);

            $calification = null;
            $calificationResult = null;

            foreach ($viajes as $via) {
                $calificationResult = $this->calificationtrip($codigoEmpleado->codigo_empleado, $validateData['app_user_id'], $via->id);

                if (!empty($calificationResult) && !$calificationResult[0]->id_calificacion) {
                    $calification = $calificationResult[0];
                    break;
                }
            }

            return Response::json([
                'response' => true,
                'calificacion' => !empty($calification) && empty($calification->id_calificacion) ? $calification : null,
                'listado' => !empty($results) && empty($calification) && empty($calificationResult) ? $results[0] : null,
            ]);

        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes activos: ' . $th->getMessage());
        }
    }


    // Listar Viajes activos
    public function listarViajesLink(Request $request)
    {
        $validateData = $request->validate([
            'viaje' => ['required', 'integer'],
            'id_pasajero_ejecutivo' => ['nullable', 'integer'],
            'id_pasajero_ruta' => ['nullable', 'integer']
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
                    t3.id as id_tipo_ruta,
                    t3.codigo as codigo_tipo_ruta,
                    t3.nombre as nombre_tipo_ruta,
                    e.id as id_estado,
                    e.codigo as codigo_estado,
                    e.nombre as nombre_estado,
                    v2.placa,
                    v2.modelo,
                    v2.marca,
                    v2.color,
                    c2.foto as foto_conductor,
                    c2.celular as celular_conductor,
                    e2.id as id_tipo_vehiculo,
                    e2.codigo as codigo_tipo_vehiculo,
                    e2.nombre as nombre_tipo_vehiculo,
                    (case when pe.id is null then prq.id_empleado else pe.id end) as id_pasajero,
                    (case when pe.nombre is null then prq.nombre else pe.nombre end) as nombre_pasajero,
                    prq.id as id_pasajero_ruta,
                    pe.id as id_pasajero_ejecutivo,
                    pe.app_user_id,
                    t4.id as id_estado_pasajero_ruta,
                    t4.codigo as codigo_estado_pasajero_ruta,
                    t4.nombre as nombre_estado_pasajero_ruta,
                    prq.recoger_a as recoger_ruta_pasajero,
                    prq.codigo_viaje as codigo_ruta_pasajero,
                    UPPER(CONCAT(c2.primer_nombre, ' ', c2.primer_apellido)) AS nombre_conductor,
                    cv.id as id_calificacion,
                    JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) AS destinos
                from viajes v
                left join vehiculos v2 on v2.id = v.fk_vehiculo
                left join conductores c2 on c2.id = v.fk_conductor
                left join calificacion_viajes cv on cv.fk_viaje = v.id
                left join pasajeros_ejecutivos pe on pe.fk_viaje = v.id
                left join pasajeros_rutas_qr prq on prq.fk_viaje = v.id
                left join estados e on e.id = v.fk_estado
                left join destinos d on d.fk_viaje = v.id
                left join tipos t on t.id = v.tipo_traslado
                left join estados e2 on e2.id = v2.fk_tipo_vehiculo
                left join tipos t3 on t3.id = v.tipo_ruta
                left join tipos t4 on t4.id = prq.estado_ruta
                where v.id = ? and (pe.id = ? or prq.id = ?)
                GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35
                LIMIT 1;";

            $params = [$validateData['viaje'], $validateData['id_pasajero_ejecutivo'], $validateData['id_pasajero_ruta']];

            $results = DB::select($query, $params);

            return Response::json([
                'response' => true,
                'listado' => !empty($results) ? $results[0] : null,
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

        $codigoEmpleado = DB::table('users as u')
            ->join('empleados_clientes as ec', 'ec.id', '=', 'u.id_empleado_cliente')
            ->where('u.id', '=', $validatedData['app_user_id'])
            ->select('ec.codigo_empleado')
            ->first();

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
                     pe.app_user_id = ?)
                 AND (t.codigo = ? or ? is null)";

            $params = [
                $codigoEmpleado->codigo_empleado,
                $validatedData['app_user_id'],
                $validatedData['codigo_viaje'] ?? null,
                $validatedData['codigo_viaje'] ?? null, // Este es para la comparación "OR NULL"
            ];

            $fechaHoy = Carbon::now('America/Bogota')->format('Y-m-d');
            $query .= " AND v.fecha_viaje " . ($request->fecha ? ("= '" . $request->fecha . "'") : (">= '" . $fechaHoy . "'"));
            // Comprobar si hay múltiples estados de viaje
            if (!isset($validatedData['estado_viaje']) || !empty($validatedData['estado_viaje'])) {
                $placeholders = implode(',', array_fill(0, count($validatedData['estado_viaje']), '?'));
                $query .= " AND e.codigo IN ($placeholders)";
                $params = array_merge($params, $validatedData['estado_viaje']);
            }

            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17;";

            // Aqui se ejecutaria la consulta y se obtendrian los resultados.
            // En este caso, se retornan los resultados de la consulta.
            $results = DB::select($query, $params);

            \Log::info(json_encode($query));

            if (!empty($validatedData['estado_viaje'])) {
                $estadoViaje = (array) $validatedData['estado_viaje']; // Convierte a array si no lo es
                // Si estado_viaje es un array, verifica si alguno de sus elementos está en la lista
                if (is_array($estadoViaje) && array_intersect($estadoViaje, ["ENTEND", "NOPROMAN", "PORAUTORIZAR", "PROGRAM"])) {
                    $listaVijesPendientes = $this->listarViajesPendientesRutas($codigoEmpleado->codigo_empleado, $fechaHoy);

                    $listaVijesPendientesEjecutivos = $this->listarViajesPendientesEjecutivos($validatedData['app_user_id'], $fechaHoy);
                    // Combina todos los resultados en un solo array si existen datos
                    if (!empty($listaVijesPendientes)) {
                        $results = array_merge($results, $listaVijesPendientes);
                    }
                    if (!empty($listaVijesPendientesEjecutivos)) {
                        $results = array_merge($results, $listaVijesPendientesEjecutivos);
                    }
                }
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
    private function listarViajesPendientesRutas($idEmpleado, $fecha)
    {

        try {

            $query = "SELECT
                    rs.id,
                    rs.fecha as fecha_viaje,
                    rs.hora as hora_viaje,
                    NULL AS cantidad_pasajeros,
                    81 AS id_estado,
                    'NOPROMAN' AS codigo_estado,
                    'NO PROGRAMADO' AS nombre_estado,
                    70 AS id_tipo_ruta,
                    'RUTA' AS codigo_tipo_ruta,
                    'RUTA' AS nombre_tipo_ruta,
                    NULL AS placa,
                    NULL AS modelo,
                    NULL AS marca,
                    NULL AS color,
                    NULL AS codigo_tipo_vehiculo,
                    NULL AS nombre_tipo_vehiculo,
                    NULL AS nombre_completo,
                    JSON_ARRAY(
                        JSON_OBJECT(
                            'direccion', CASE
                                            WHEN rs.fk_tipo_ruta = 67 THEN c.razonsocial
                                            ELSE rsp.direccion
                                        END,
                            'coordenadas', null,
                            'orden', 1
                        ),
                        JSON_OBJECT(
                            'direccion', CASE
                                            WHEN rs.fk_tipo_ruta = 67 THEN rsp.direccion
                                            ELSE c.razonsocial
                                        END,
                            'coordenadas', null,
                            'orden', 2
                        )
                    ) AS destinos
                FROM
                    rutas_solicitadas rs
                LEFT JOIN
                    centrosdecosto c ON c.id = rs.fk_centrodecosto
                LEFT JOIN
                    rutas_solicitadas_pasajeros rsp ON rsp.fk_rutas_solicitadas = rs.id
                        where rsp.empleado_id = ? and rs.montado is null and rs.visible is null";

            $params = [
                $idEmpleado
            ];

            if (!empty($fecha)) {
                $query .= " AND rs.fecha >= ?";
                $params = array_merge($params, [$fecha]); // Wrap in array
            }

            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18;";

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
                    vu.fecha as fecha_viaje,
                    vu.hora as hora_viaje,
                    null as cantidad_pasajeros,
                    81 AS id_estado,
                    'NOPROMAN' AS codigo_estado,
                    'NO PROGRAMADO' AS nombre_estado,
                    69 AS id_tipo_ruta,
                    'EJEC' AS codigo_tipo_ruta,
                    'EJECUTIVO' AS nombre_tipo_ruta,
                    null as placa,
                    null as modelo,
                    null as marca,
                    null as color,
                    null as codigo_tipo_vehiculo,
                    null as nombre_tipo_vehiculo,
                    null AS nombre_completo,
                    JSON_ARRAY(
                                        JSON_OBJECT(
                                            'direccion', vu.desde,
                                            'coordenadas', null,
                                            'orden', 1
                                        ),
                                        JSON_OBJECT(
                                            'direccion', vu.hasta,
                                            'coordenadas', null,
                                            'orden', 2
                                        )
                                    ) AS destinos
                FROM
                    viajes_upnet vu
                LEFT JOIN
                                    centrosdecosto c ON
                    c.id = vu.centrodecosto
                LEFT JOIN
                                    viajes_upnet_pasajeros vup ON
                    vup.fk_viaje_upnet = vu.id
                        where vup.app_user_id = ? and vu.fk_estado != 57";

            $params = [
                $appUserId,
            ];



            if (!empty($fecha)) {
                $query .= " AND vu.fecha >= ?";
                $params = array_merge($params, [$fecha]); // Wrap in array
            }
            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17;";

            $results = DB::select($query, $params);




            return $results;

        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes pendientes ejecutivos: ' . $th->getMessage());
        }
    }

    private function calificationtrip($idEmpleado, $appUserId, $viaje)
    {
        try {

            $fechaHoy = Carbon::now('America/Bogota')->format('Y-m-d');

            $query = "SELECT
                v.id AS id,
                v.fecha_viaje,
                v.hora_viaje,
                CONCAT(c.primer_nombre, ' ', c.primer_apellido) AS conductor,
                e2.nombre AS tipo_de_vehiculo,
                cv.id AS id_calificacion,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'direccion', d.direccion,
                        'coordenadas', d.coordenadas,
                        'orden', d.orden
                    )
                ) AS destinos
            FROM
                viajes v
            INNER JOIN destinos d ON
                d.fk_viaje = v.id
            INNER JOIN vehiculos v2 ON
                v2.id = v.fk_vehiculo
            INNER JOIN estados e2 ON
                e2.id = v2.fk_tipo_vehiculo
            INNER JOIN conductores c ON
                c.id = v.fk_conductor
            LEFT JOIN calificacion_viajes cv ON
                v.id = cv.fk_viaje
                AND (
                    cv.fk_user = ?
                )
            LEFT JOIN pasajeros_rutas_qr prq ON
                prq.fk_viaje = v.id
            LEFT JOIN pasajeros_ejecutivos pe ON
                pe.fk_viaje = v.id
            WHERE
                v.fecha_viaje = ? AND v.id = ? AND
                v.estado_eliminacion is null
                -- AND cv.id IS NULL
                AND (
                    prq.id_empleado = ?
                    OR pe.app_user_id = ?
                )
                AND
                    CASE
                        WHEN v.tipo_traslado = 70
                        AND prq.recoger_a = 2
                        AND (v.fk_estado = 59
                            OR v.fk_estado = 60) THEN true
                        WHEN v.tipo_traslado = 69
                        AND v.recoger_pasajero = 1
                        AND v.fk_estado = 60 THEN true
                    ELSE false
                END
            GROUP BY
                v.id,
                v.fecha_viaje,
                v.hora_viaje,
                conductor,
                e2.nombre,
                cv.id
            ORDER BY
                v.fecha_viaje DESC,
                v.hora_viaje DESC;";

            $params = [
                $appUserId ?? null,
                $fechaHoy,
                $viaje,
                $idEmpleado ?? null,
                $appUserId ?? null
            ];

            $results = DB::select($query, $params);

            \Log::info(json_encode($results));

            return $results;

        } catch (\Throwable $th) {
            \Log::error('Error al obtener la ultima calificacion pendiente por revisar: ' . $th->getMessage());
        }
    }

    // formulario de solicitud de viaje ejecutivo
    public function requesttrips(Request $request)
    {
        try {
            // Ejecutar la consulta para obtener el usuario
            $query = DB::selectOne("
            SELECT u.id, u.centrodecosto_id, u.subcentrodecosto_id, c.fk_sede as sede_user, c.ciudad as ciudad_user, ec.nombre, ec.telefono, ec.correo, u.master
            FROM users u
            LEFT JOIN centrosdecosto c ON c.id = u.centrodecosto_id
            LEFT JOIN empleados_clientes ec ON ec.codigo_empleado = u.codigo_empleado
            WHERE u.id = ?
        ", [$request->id_user]);

            // Verificar que se encontró el usuario
            if (!$query) {
                return response()->json([
                    'response' => false,
                    'codigo' => 'USER_NOT_FOUND',
                    'message' => 'No se encontró un usuario con el id proporcionado.'
                ]);
            }

            // Asegurarse de que $viajes sea un array
            $viajes = $request->viajes ?? [];

            $fk_sede = $query->sede_user;
            $centro = $query->centrodecosto_id;

            // Determinar el tipo de solicitud según la cantidad de viajes
            $tipo = count($viajes) > 1 ? 2 : 1;

            // Crear registros de viaje
            foreach ($viajes as $viajeData) {
                $viaje = new ViajesU;
                $viaje->fecha_solicitud = date('Y-m-d');
                $viaje->fecha = $viajeData['fecha'];
                $viaje->hora = $viajeData['hora'];
                $viaje->desde = $viajeData['desde'] ?? null;
                $viaje->hasta = $viajeData['hasta'] ?? null;
                $viaje->detalles = $viajeData['detalles'] ?? null;
                $viaje->fk_sede = $fk_sede;
                $viaje->fk_centrodecosto = $centro;
                $viaje->fk_ciudad = $query->ciudad_user;
                $viaje->vuelo = $viajeData['vuelo'] ?? null;
                $viaje->tipo_vehiculo = $viajeData['tipo_vehiculo'] ?? null;
                $viaje->tipo_solicitud = $tipo;
                $viaje->created_at = date('Y-m-d H:i:s');
                $viaje->creado_por = $request->id_user;
                $viaje->info_adicional = null;
                $viaje->app_user = $request->id_user;
                $viaje->fk_estado = ($query->master == 1) ? 81 : 82;
                $viaje->save();

                try {
                    Log::info('Datos a insertar en viajes_upnet_pasajeros: ', [
                        'nombre' => $query->nombre,
                        'celular' => $query->telefono,
                        'correo' => $query->correo,
                        'fk_viaje_upnet' => $viaje->id
                    ]);

                    DB::table('viajes_upnet_pasajeros')->insert([
                        'nombre' => $query->nombre,
                        'celular' => $query->telefono,
                        'correo' => $query->correo,
                        'fk_viaje_upnet' => $viaje->id
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Error al insertar en viajes_upnet_pasajeros: ' . $e->getMessage());
                }
            }

            Log::info('Solicitud de viaje ejecutivo: ' . json_encode($query));




            return response()->json([
                'response' => true,
                'codigo' => 'NOPROMAN',
            ]);

        } catch (\Throwable $th) {
            Log::error('Error al solicitar viaje ejecutivo: ' . $th->getMessage());
            return response()->json([
                'response' => false,
                'codigo' => 'FAIL',
            ]);
        }
    }


    // solicitud de ruta pasajeros
    public function solicitudViajePasajeros(Request $request)
    {
        // Obtener datos del usuario autenticado
        $user = Auth::user();

        // Obtener el empleado asociado al usuario
        $empleado = DB::table('empleados_clientes as ec')
            ->where('ec.id', $user->id_empleado_cliente)
            ->orderBy('id', 'desc')
            ->first();

        // obtener el id de la ciudad del centro de costo del empleado 
        $ciudad_centro_de_costo = DB::table('centrosdecosto as cc')
        ->join('users as u', 'u.centrodecosto_id', '=', 'cc.id')
        ->select('cc.ciudad')
        ->where('u.id', $user->id)
        ->first();


        // Log request all
        Log::info('Solicitud de viaje pasajeros: ' . json_encode($request->all()));

        // Obtener el primer viaje del array "viajes"
        $viaje = $request->input('viajes.0'); // Accede al primer elemento del array de viajes

        // Convertir la hora ingresada a formato de tiempo
        $horaIngresada = $viaje['hora']; // Asumimos que viene en formato "HH:mm"
        $horaMenos15Min = date("H:i", strtotime($horaIngresada . " -15 minutes"));

        // condicional ternario para redondear la hora por el tipo de ruta envio un parametro a la funcion si es 67 o 68 
        $rango = $viaje['tipo_ruta'] == 67 ? 15 : 30;

        $horaFormateada = $this->redondearHora($viaje['hora'], $rango);

        // si la $horaFormateada es igual a 00:00 debe tomar a $viaje['fecha'] y sumarle un dia (REVISAR)
        if (($horaFormateada == '00:00' && $viaje['tipo_ruta'] == 68) && $viaje['fecha'] != '00:00') {
            $viaje['fecha'] = date('Y-m-d', strtotime($viaje['fecha'] . ' +1 day'));
        }


        // Primera consulta
        $consulta1 = DB::table('rutas_solicitadas_pasajeros as rsp')
            ->join('rutas_solicitadas as rs', 'rs.id', '=', 'rsp.fk_rutas_solicitadas')
            ->join('users as u', 'u.codigo_empleado', '=', 'rsp.empleado_id')
            ->select(
                'rs.id',
                'rs.visible',
                'rs.montado',
                DB::raw('NULL as usuario_eliminacion'),
                DB::raw("CASE 
        WHEN rs.visible IS NULL AND rs.montado IS NULL THEN 3
        WHEN rs.visible = 1 AND rs.montado IS NULL THEN 1
        ELSE 'definir'
        END AS accion")
            )
            ->where('u.id', $user->id)
            ->where('rs.fecha', $viaje['fecha'])
            ->where('rs.fk_tipo_ruta', $viaje['tipo_ruta'])
            ->where('rs.fk_centrodecosto', $empleado->fk_centrodecosto)
            ->where('rs.fk_subcentrodecosto', $empleado->fk_subcentrodecosto)
            ->where('rs.fk_sede', $ciudad_centro_de_costo->ciudad)
            ->whereNull('rs.visible')
            ->whereNull('rs.montado')
            ->whereNull('rsp.user_eliminacion');

        // Segunda consulta
        $consulta2 = DB::table('rutas_solicitadas_pasajeros as rsp')
            ->join('rutas_solicitadas as rs', 'rs.id', '=', 'rsp.fk_rutas_solicitadas')
            ->join('users as u', 'u.codigo_empleado', '=', 'rsp.empleado_id')
            ->join('pasajeros_rutas_qr as prq', 'prq.id_empleado', '=', 'rsp.empleado_id')
            ->join('viajes as v', function ($join) {
                $join->on('v.id', '=', 'prq.fk_viaje')
                    ->on('v.fecha_viaje', '=', 'rs.fecha')
                    ->on('v.hora_viaje', '=', 'rs.hora')
                    ->whereNull('v.usuario_eliminacion');
            })
            ->select(
                'rs.id',
                'rs.visible',
                'rs.montado',
                'v.usuario_eliminacion',
                DB::raw("CASE 
        WHEN rs.visible IS NULL AND rs.montado IS NULL AND v.usuario_eliminacion IS NULL THEN 1
        WHEN rs.visible IS NULL AND rs.montado = 1 AND v.usuario_eliminacion IS NULL THEN 3
        WHEN rs.visible IS NULL AND rs.montado = 1 AND v.usuario_eliminacion IS NOT NULL THEN 1
        ELSE 'definir'
        END AS accion")
            )
            ->where('u.id', $user->id)
            ->where('rs.fecha', $viaje['fecha'])
            ->where('rs.fk_tipo_ruta', $viaje['tipo_ruta'])
            ->where('rs.fk_centrodecosto', $empleado->fk_centrodecosto)
            ->where('rs.fk_subcentrodecosto', $empleado->fk_subcentrodecosto)
            ->where('rs.fk_sede', $ciudad_centro_de_costo->ciudad)
            ->whereNull('rs.visible')
            ->where('rs.montado', 1)
            ->whereNull('rsp.user_eliminacion');

        // Unir ambas consultas con UNION
        $resultado = $consulta1->union($consulta2)->get();

        $accion = $resultado->first()->accion ?? null;

        Log::info('accion: ' . $accion);


        // Buscar si el usuario que esta solicitando la ruta de entrada o salida ya tiene una de esas solicitudes para el dia de hoy $viaje['tipo_ruta']


        // ACCION 1 Crear , 2(no trae nada) Montar, 3 No permitir nada
        if ($accion == 3) {
            Log::info('El usuario ya tiene una ruta solicitada para el dia de hoy');
            return Response::json([
                'response' => false,
                'message' => 'RUOTE_EXIST',
            ], 200);
        }

        // inicializar la variable para guardar la consutla de $rutas_solicitadas que esta dentro del if
        $rutas_solicitadas = null;

        if (!$accion) {
            // Buscar la ruta solicitada con los datos del viaje
            $rutas_solicitadas = DB::table('rutas_solicitadas as rs')
                ->where('rs.fecha', $viaje['fecha'])
                ->where('rs.fk_tipo_ruta', $viaje['tipo_ruta'])
                ->where('rs.fk_centrodecosto', $empleado->fk_centrodecosto)
                ->where('rs.fk_subcentrodecosto', $empleado->fk_subcentrodecosto)
                ->where('rs.hora', $horaFormateada)
                ->where('rs.fk_sede', $ciudad_centro_de_costo->ciudad)  // Cambiar por la sede del subcentro de costo 
                ->where(function ($query) {
                    $query->whereNull('rs.visible')->orWhere('rs.visible', '!=', 1);
                })
                ->where(function ($query) {
                    $query->whereNull('rs.montado')->orWhere('rs.montado', '!=', 1);
                })
                ->first();
        }


        // Si existe el empleado, actualizar latitud y longitud
        if ($empleado && ($empleado->longitude == null || $empleado->latitude == null)) {
            DB::table('empleados_clientes as ec')
                ->where('ec.codigo_empleado', $user->codigo_empleado)
                ->update([
                    'latitude' => $viaje['latitude'],
                    'longitude' => $viaje['longitude'],
                ]);
        }

        try {
            $descripcion = $empleado->fk_subcentrodecosto == 3845 ? 'BR' : 'CO';

            // Si no existe la ruta, crearla
            if (!$rutas_solicitadas || $accion == 1) {
                $autorizacion_de_rutas = DB::table('autorizacion_de_rutas')->insertGetId([
                    'estado_autorizacion' => 0,
                    'created_at' => now(),
                ]);


                $rutas_solicitadas = DB::table('rutas_solicitadas')->insertGetId([
                    'fecha' => $viaje['fecha'],
                    'descripcion' => $descripcion,
                    'fecha_solicitud' => now(),
                    'fk_solicitado_por' => Auth::user()->id,
                    'fk_centrodecosto' => $empleado->fk_centrodecosto,
                    'fk_subcentrodecosto' => $empleado->fk_subcentrodecosto,
                    'fk_sede' => $ciudad_centro_de_costo->ciudad,  // Cambiar por la sede del subcentro de costo 
                    'fk_tipo_ruta' => $viaje['tipo_ruta'],
                    'hora' => $horaFormateada,
                    'autorizacion_id' => $autorizacion_de_rutas,
                    'created_at' => now(),
                ]);
            }

            // Insertar pasajero en la ruta solicitada
            DB::table('rutas_solicitadas_pasajeros')->insert([
                'nombre' => $empleado->nombre,
                'fecha' => $viaje['fecha'],
                'empleado_id' => $empleado->codigo_empleado,
                'fk_centrodecosto' => $empleado->fk_centrodecosto,
                'fk_subcentrodecosto' => $empleado->fk_subcentrodecosto,
                'telefono' => $empleado->telefono,
                'direccion' => $viaje['desde'] ?? $viaje['hasta'],
                //'barrio' => $empleado->barrio,
                //'localidad' => $empleado->localidad,
                'latitude' => $viaje['latitude'],
                'longitude' => $viaje['longitude'],
                'hora' => $horaFormateada,
                'programa' => $empleado->programa,
                'correo' => $empleado->correo,
                'fk_rutas_solicitadas' => is_object($rutas_solicitadas) ? $rutas_solicitadas->id : $rutas_solicitadas,
            ]);

            return Response::json([
                'response' => true,
                'message' => 'Pasajero agregado correctamente'
            ], 200);

        } catch (\Throwable $th) {
            Log::info($th);
            return Response::json([
                'response' => false,
                'message' => "FAIL_ADDED_ERROR_OCURRED"
            ], 500);
        }
    }

    private function redondearHora($hora, $rango)
    {
        // Convertir la hora en un objeto Carbon
        $carbonHora = Carbon::createFromFormat('H:i', $hora);

        // Obtener los minutos actuales
        $minutos = $carbonHora->minute;

        if ($rango == 15) {
            // Redondear SIEMPRE hacia atrás
            $minutosRedondeados = floor($minutos / $rango) * $rango;
        } else {
            // Redondear SIEMPRE hacia adelante cuando el rango es 30
            $minutosRedondeados = ceil($minutos / $rango) * $rango;

            // Si se pasa de 60, ajustar la hora
            if ($minutosRedondeados >= 60) {
                $carbonHora->addHour();
                $minutosRedondeados = 0;
            }
        }

        // Ajustar la hora con los minutos redondeados
        $carbonHora->setMinute($minutosRedondeados);

        // Retornar la hora formateada
        return $carbonHora->format('H:i');
    }

}

