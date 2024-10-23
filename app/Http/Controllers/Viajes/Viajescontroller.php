<?php

namespace App\Http\Controllers\viajes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\ViajesU;
use Response;
use Auth;

class Viajescontroller extends Controller
{
    // Consulta de clientes 
    public function consultclient(Request $request) {

        $query = "SELECT * FROM info_adicional_viajes  WHERE fk_centrodecosto = ".$request->cliente."";

        $consulta = DB::select($query);

        return Response::json([
            'response' => true,
            'consulta' => $consulta
        ]);
    }

    // Consulta de viajes
    function listarViajesGenerales(Request $request)
    {


        $validatedData = $request->validate([
            'fecha_inicio' => ['required', 'string'],
            'fecha_fin' => ['required', 'string'],
            'id_empleado' => ['nullable', 'string'],
            'app_user_id' => ['nullable', 'string'],
            'codigo_viaje' => ['nullable', 'string'],
            'estado_viaje' => ['nullable', 'array'],
        ]);

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
            LEFT JOIN pasajeros_rutas_qr prq ON prq.fk_viaje = v.id
            LEFT JOIN pasajeros_ejecutivos pe ON pe.fk_viaje = v.id
            WHERE 
                v.estado_eliminacion IS NULL
                AND v.fecha_viaje BETWEEN ? AND ?
                AND (
                    (prq.id_empleado = ? OR ? IS NULL)
                    OR
                    (v.app_user_id = ? OR ? IS NULL)
                )
                AND (t.codigo = ? or ? is null)";

            $params = [
                $validatedData['fecha_inicio'],
                $validatedData['fecha_fin'],
                $validatedData['id_empleado'],
                $validatedData['id_empleado'], // Este es para la comparación "OR NULL"
                $validatedData['app_user_id'],
                $validatedData['app_user_id'], // Este es para la comparación "OR NULL"
                $validatedData['codigo_viaje'],
                $validatedData['codigo_viaje'], // Este es para la comparación "OR NULL"
            ];

            // Comprobar si hay múltiples estados de viaje
            if (!empty($validatedData['estado_viaje'])) {
                Log::info('Estado de viaje: '.json_encode($validatedData['estado_viaje']));
                $placeholders = implode(',', array_fill(0, count($validatedData['estado_viaje']), '?'));
                $query .= " AND e.codigo IN ($placeholders)";
                $params = array_merge($params, $validatedData['estado_viaje']);
            }

            $query .= " GROUP BY 
                        v.id,
                        v.fecha_viaje,
                        v.hora_viaje,
                        v.cantidad,
                        e.id,
                        e.codigo,
                        e.nombre,
                        t.id,
                        t.codigo,
                        t.nombre,
                        v2.placa,
                        v2.modelo,
                        nombre_completo";


            // Aqui se ejecutaria la consulta y se obtendrian los resultados.
            // En este caso, se retornan los resultados de la consulta.
            $results = DB::select($query, $params);

            return response(json_encode([
                'Listado: ' => $results,
                'Mensaje: ' => 'true'
            ]));
        } catch (\Throwable $th) {
            \Log::error('Error al listar viajes generales: ' . $th->getMessage());
        }

    }

    // formulario de solicitud de viaje ejecutivo
    public function requesttrips(Request $request) {

        $viajes = $request->viajes;
        $fk_sede = $request->fk_sede;
        $centro = $request->centrodecosto_id;

        if( count($viajes)>1 ) {
            $tipo = 2;
        }else{
            $tipo = 1;
        }

        $know = DB::table('info_adicional_viajes')
        ->where('fk_centrodecosto', $centro)
        ->first();

        for ($a=0; $a < count($viajes); $a++){

            $fk_ciudad = $viajes[$a]['fk_ciudad'];
            $hora = $viajes[$a]['hora'];
            $fecha = $viajes[$a]['fecha'];
            $detalles = $viajes[$a]['detalles'];
            $desde = $viajes[$a]['desde'];
            $hasta = $viajes[$a]['hasta'];
            $vuelo = $viajes[$a]['vuelo'];
            $centrodecosto = $viajes[$a]['centrodecosto'];
            $infoAdicional = null;

            if($know) {

                if($know->campo1!=null) {

                    if( $know->campo2!=null or $know->campo3!=null or $know->campo4!=null or $know->campo5!=null ) {
                        $complement = ' / ';
                    }else{
                        $complement = '';
                    }

                    if( isset($viajes[$a]['campo1']) ) {
                        $infoAdicional .= $know->campo1.': '.$viajes[$a]['campo1'].$complement;
                    }

                }

                if($know->campo2!=null) {

                    if( $know->campo3!=null or $know->campo4!=null or $know->campo5!=null ) {
                        $complement = ' / ';
                    }else{
                        $complement = '';
                    }

                    if( isset($viajes[$a]['campo2']) ) {
                        $infoAdicional .= $know->campo2.': '.$viajes[$a]['campo2'].$complement;
                    }

                }

                if($know->campo3!=null) {

                    if( $know->campo4!=null or $know->campo5!=null ) {
                        $complement = ' / ';
                    }else{
                        $complement = '';
                    }

                    if( isset($viajes[$a]['campo3']) ) {
                        $infoAdicional .= $know->campo3.': '.$viajes[$a]['campo3'].$complement;
                    }

                }

                if($know->campo4!=null) {

                    if( $know->campo5!=null ) {
                        $complement = ' / ';
                    }else{
                        $complement = '';
                    }

                    if( isset($viajes[$a]['campo4']) ) {
                        $infoAdicional .= $know->campo4.': '.$viajes[$a]['campo4'].$complement;
                    }

                }

                if($know->campo5!=null) {

                    $complement = '';

                    if( isset($viajes[$a]['campo5']) ) {
                        $infoAdicional .= $know->campo5.': '.$viajes[$a]['campo5'];
                    }

                }

            }

            $viaje = new ViajesU;
            $viaje->fecha_solicitud = date('Y-m-d');
            $viaje->fecha = $fecha;
            $viaje->desde = $desde;
            $viaje->hasta = $hasta;
            $viaje->hora = $hora;
            $viaje->detalles = $detalles;
            $viaje->fk_sede = $fk_sede;
            $viaje->fk_centrodecosto = $centro;
            $viaje->fk_ciudad = $fk_ciudad;
            $viaje->vuelo = $vuelo;
            $viaje->centrodecosto = $centrodecosto;
            $viaje->tipo_solicitud = $tipo;
            $viaje->created_at = date('Y-m-d H:i:s');
            $viaje->creado_por = Auth::check() ? Auth::user()->id : 6164;
            $viaje->info_adicional = $infoAdicional;
            $viaje->save();

            $pasajeros = $viajes[$a]['pasajeros'];

            for ($i=0; $i < count($pasajeros); $i++){

                $pax = DB::table('viajes_upnet_pasajeros')
                ->insert([
                    'nombre' => $pasajeros[$i]['nombre'],
                    'celular' => $pasajeros[$i]['celular'],
                    'correo' => $pasajeros[$i]['correo'],
                    'fk_viaje_upnet' => $viaje->id
                ]);

            }

        }

        return Response::json([
            'response' => true
        ]);

    }

}
