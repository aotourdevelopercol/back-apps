<?php

namespace App\Http\Controllers;
use App\Models\LugarF;
use App\Models\User;
use App\Models\PasajeroRuta;
use App\Models\Tipo;
use App\Models\Viaje;
use App\Models\Destino;
use App\Models\PasajeroEjecutivo;
use App\Models\Gps;
use App\Models\PagoServicio;
use App\Models\Conductor;
use App\Models\ReferenciasPayu;
use App\Models\ViajeAplicacion;
use App\Models\TokenPayU;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Response;
use DB;

class ViajesController extends Controller
{
    // Codigo modificado, porque la función original no tenia variables definidas y generaba errores: Validar si la corrección esta OK
    public function actualizarubicacion(Request $request)
    {

        // Obtener el ID del servicio desde la solicitud
        $servicio_id = $request->id;

        // Buscar el viaje en la tabla 'viajes' con el ID proporcionado
        $viaje = DB::table('viajes')
            ->select('id', 'fk_estado', 'recorrido_gps') // Asegúrate de seleccionar los campos necesarios
            ->where('id', $servicio_id)
            ->first();

        // Verificar si el viaje fue encontrado
        if (!$viaje) {
            return Response::json([
                'response' => false,
                'message' => 'Viaje no encontrado',
            ]);
        }

        // Buscar las coordenadas del GPS para el viaje
        $gps = DB::table('gps')
            ->where('fk_viaje', $servicio_id)
            ->first();

        // Verificar si se encontraron coordenadas GPS
        if (!$gps) {
            return Response::json([
                'response' => false,
                'message' => 'Datos GPS no encontrados',
            ]);
        }

        // Decodificar las coordenadas JSON
        $value = json_decode($gps->coordenadas);

        // Verificar que las coordenadas no estén vacías
        if (empty($value)) {
            return Response::json([
                'response' => false,
                'message' => 'No hay coordenadas disponibles',
            ]);
        }

        // Contar los puntos en el recorrido GPS
        $cantidad_puntos = count($value);

        // Obtener la última ubicación del recorrido
        $ultima_ubicacion = $value[$cantidad_puntos - 1];

        // Decodificar el recorrido GPS desde el viaje (si existe)
        $parseRecorrido = json_decode($viaje->recorrido_gps);

        // Devolver la respuesta JSON con los datos solicitados
        return Response::json([
            'response' => true,
            'servicio_id' => $servicio_id,
            'ultima_ubicacion' => $ultima_ubicacion, // Última coordenada
            'cantidad_puntos' => $cantidad_puntos,    // Cantidad de puntos en el recorrido
            'estado_servicio_app' => $viaje->fk_estado, // Estado del servicio
            'recogido' => $viaje->recoger_pasajero ?? 'No definido' // Verificar si la propiedad existe
        ]);

    }
    public function addtoken(Request $request)
    {

        $id = $request->id;
        $nombre = $request->nombre;
        $identificacion = $request->identificacion;
        $numero = $request->numero;
        $cvc = strval($request->cvc);
        $mes = strval($request->mes);
        $ano = strval($request->ano);

        $usuario = User::find($id);

        $apiUrl = "https://production.wompi.co/v1/tokens/cards";

        $data = [
            "number" => strval($numero),
            "cvc" => $cvc,
            "exp_month" => $mes,
            "exp_year" => $ano,
            "card_holder" => $nombre
        ];

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer pub_prod_k3EGLrTVqzDhXKogfQwL8PGo080sw5K0',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($result);

        $switch = 0;
        try {
            if ($result->status === 'CREATED') {
                $switch = 0;
            }
        } catch (\Exception $e) {
            $switch = 1;
        }

        if ($switch === 0) {

            $llave = 'pub_prod_k3EGLrTVqzDhXKogfQwL8PGo080sw5K0';
            $response = file_get_contents('https://production.wompi.co/v1/merchants/' . $llave . '');
            $response = json_decode($response);
            $acceptance_token = $response->data->presigned_acceptance->acceptance_token;

            $token_tarjeta = $result->data->id;
            $apiUrlFuente = "https://production.wompi.co/v1/payment_sources";

            $datas = [
                "type" => "CARD",
                "token" => $token_tarjeta,
                "customer_email" => $usuario->email,
                "acceptance_token" => $acceptance_token,
            ];

            $headers = [
                'Accept: application/json',
                'Authorization: Bearer prv_prod_WJ9PkFir6uPwOPwstzfI8LsfPPedpRda',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrlFuente);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datas));
            $resultados = curl_exec($ch);
            $resultados = json_decode($resultados);
            $id_fuente_pago = $resultados->data->id;

            $expiration = $result->data->exp_year . '/' . $result->data->exp_month;

            $token = new TokenPayU;
            $token->creditCardTokenId = $result->data->id;
            $token->fuente_pago = $id_fuente_pago;
            $token->identificationNumber = $identificacion;
            $token->paymentMethod = $result->data->brand;
            $token->maskedNumber = $result->data->bin . '*****' . $result->data->last_four;
            $token->lastFour = $result->data->last_four;
            $token->name = strtoupper($result->data->card_holder);
            $token->expirationDate = $expiration;
            $token->payerId = $id;
            $token->valido = 1;
            $token->save();

            return Response::json([
                'respuesta' => true,
                'token_card' => $result
            ]);

        } else {

            return Response::json([
                'respuesta' => false,
                'token_card' => $result
            ]);

        }

    }

    // Validar error 500
    public function calificacionderuta(Request $request)
    {

        $tipo = $request->valor;
        $id = $request->id;
        $comentarios = $request->comentarios;

        $servicio = DB::table('pasajeros_rutas_qr')
            ->where('id', $id)
            ->update([
                'rate' => $tipo,
                'comentarios' => trim(strtoupper($comentarios))
            ]);

        return Response::json([
            'respuesta' => true,
            'tipo' => $tipo,
            'id' => $id
        ]);

    }
    public function confirmardireccion(Request $request)
    {

        $id = $request->id;
        $direccion = $request->direccion;
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        $update = PasajeroRuta::find($id);

        if ($update->confirmado == 1) {

            return Response::json([
                'response' => false
            ]);

        } else {

            $update->direccion = $direccion;
            $update->latitude = $latitude;
            $update->longitude = $longitude;
            $update->confirmado = 1;
            $update->save();

            return Response::json([
                'response' => true
            ]);

        }

    }
    public function consultarcodigo(Request $request)
    {
        try {

            $id = $request->id;

            $viaje = DB::table('viajes')
                ->select('id', 'codigo_viaje')
                ->where('id', $id)
                ->first();

            $codigo = $viaje->codigo_viaje;

            return Response::json([
                'response' => true,
                'codigo' => $codigo
            ]);

        } catch (\Exception $e) {
            // Registrar el error en el log
            Log::error('Error al consultar el codigo: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString() // Guarda el trace del error
            ]);

            // Responder con un error genérico
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'Ocurrió un error al editar el lugar. Por favor, inténtalo de nuevo.'
            ]);
        }

    }
    public function consultartarjetas(Request $request)
    {

        $id = $request->id;

        $consulta = DB::table('tokens_payu')
            ->where('payerId', $id)
            ->get();

        if (!$consulta->isEmpty()) {

            return Response::json([
                'respuesta' => true,
                'tarjetas' => $consulta
            ]);

        } else {

            return Response::json([
                'respuesta' => false
            ]);

        }

    }
    public function calculartarifaservicio(Request $request)
    {

        $id_usuario = $request->id;
        $distancia = $request->distancia; //Distacia en metros (Ejemplo: 4291 = 4,3 km)
        $tiempo = $request->tiempo; //Tiempo en segundos (Ejemolo: 863 = 14mins)
        $fecha = $request->fecha;
        $hora = $request->hora;
        $tipo_vehiculo = $request->tipo_vehiculo;

        $valor = 2800;

        $status = true;

        /*Código para calcular tarifa*/

        /*Cálculo del tiempo*/
        //SI EL SERVICIO NO SOBREPASA LOS 10 MINUTOS Y LA DISTANCIA ES MENOR A LOS 3KM, SE COBRA LA TARIFA MÍNIMA.
        if ($tiempo <= 600 and $distancia < 3000) {
            $valor_tarifa = 25000;
            //SI EL SERVICIO PASA LOS 10 MINUTOS(600seg), PERO NO HA RECORRIDO LOS 3KM(300m), SE COBRA LA TARIFA MÍNIMA, Y SE ADICIONAN 600 PESOS POR CADA MINUTO.
        } else if ($tiempo > 600 and $distancia <= 3000) {
            /*START COBRO TARIFA MÍNIMA MÁS (600*MIN)*/
            $valor_tarifa = 9000 + (($tiempo - 600) * 5);
            if ($valor_tarifa < 25000) {
                $valor_tarifa = 25000;
            }
            /*END COBRO TARIFA MÍNIMA MÁS (600*MIN)*/
            //SI EL SERVICIO PASÓ LOS 3KM SE COBRA EL VALOR DE LA TARIFA MÍNIMA, Y SE ADICIONAN 300 PESOS POR CADA 100 METROS (CONTANDO DESPUÉS DE LOS 3KM)
        } else if ($distancia > 3000) {
            //CALCULAR DATOS DE TIEMPO NEW START
            $valor_tiempo = ($tiempo - 600) * 5;
            /* START COBRO TARIFA MÍNIMA MÁS (300*100M)*/
            $valor_tarifa = 9000 + ((($distancia - 3000) * 3));
            $valor_tarifa = $valor_tarifa + $valor_tiempo;

            if ($valor_tarifa < 25000) {
                $valor_tarifa = 25000;
            }

            /* END COBRO TARIFA MÍNIMA MÁS (300*100M)*/
        }
        /*Cálculo por tiempo*/

        return Response::json([
            'response' => $status,
            'distancia' => $distancia,
            'tiempo' => $tiempo,
            'valor' => intval($valor_tarifa)
        ]);

    }
    public function cambiaridioma(Request $request)
    {
        $id = $request->id;
        $idIdioma = $request->idIdioma;

        // Obtener el idioma del usuario
        $ActualizarIdioma = DB::table('users')
            ->where('id', $id) // Asegúrate de que $id es correcto
            ->select('idioma') // Selecciona el campo 'idioma'
            ->first(); // Obtiene el primer registro

        // Verificar si el usuario existe
        if (!$ActualizarIdioma) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
        }

        if ($ActualizarIdioma->idioma != $idIdioma) {
            $ActualizarIdioma = DB::table('users')
                ->where('id', $id) // Asegúrate de que $id es correcto
                ->update(['idioma' => $idIdioma]); // Obtiene el primer registro

            $nuevoIdioma = DB::table('users')
                ->where('id', $id) // Asegúrate de que $id es correcto
                ->select('idioma')
                ->first();

            $tipo = Tipo::obtenerTipoPorCodigoYId($nuevoIdioma->idioma);

            return response()->json([
                'CODE' => "CHANGE_LANGUAGE",
                'lenguaje' => $tipo->nombre,
                'id-lenguaje' => $tipo->id,
                'codigo-lenguaje' => $tipo->codigo,
            ]);

        } else {

            $tipo = Tipo::obtenerTipoPorCodigoYId($ActualizarIdioma->idioma);

            return response()->json([
                'CODE' => "SAME_LANGUAGE",
                'idioma' => $tipo->nombre,
                'id-lenguaje' => $tipo->id,
                'codigo-lenguaje' => $tipo->codigo,
            ]);
        }

    }
    public function contactos(Request $request)
    {

        $barranquilla_movil = DB::table('contactos')
            ->where('ciudad', 'BARRANQUILLA')
            ->where('tipo', 'movil')
            ->get();

        $barranquilla_email = DB::table('contactos')
            ->where('ciudad', 'BARRANQUILLA')
            ->where('tipo', 'email')
            ->get();

        $bogota_movil = DB::table('contactos')
            ->where('ciudad', 'BOGOTA')
            ->where('tipo', 'movil')
            ->get();

        $bogota_email = DB::table('contactos')
            ->where('ciudad', 'BOGOTA')
            ->where('tipo', 'email')
            ->get();

        return Response::json([
            'respuesta' => true,
            'barranquilla_movil' => $barranquilla_movil,
            'barranquilla_email' => $barranquilla_email,
            'bogota_movil' => $bogota_movil,
            'bogota_email' => $bogota_email
        ]);

    }
    public function editardatos(Request $request)
    {

        $empresa = $request->empresa;
        $id_empleado = $request->id_empleado;
        $user_id = $request->user_id;

        $user = User::find($user_id);
        $user->empresa = trim(strtoupper(strtolower($empresa)));
        $user->id_empleado = $id_empleado;
        $user->save();

        return Response::json([
            'response' => true
        ]);

    }
    public function editarlugar(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $id = $request->id;
            $nombre = $request->nombre;
            $direccion = $request->direccion;
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            // Buscar el lugar por ID
            $lugar = LugarF::find($id);

            // Verificar si se encontró el lugar
            if ($lugar) {
                // Actualizar los campos
                $lugar->nombre = $nombre;
                $lugar->direccion = $direccion;
                $lugar->latitude = $latitude;  // Asegúrate de asignar los valores correctos
                $lugar->longitude = $longitude;
                $lugar->save();

                // Respuesta exitosa
                return response()->json([
                    'respuesta' => true
                ]);
            } else {
                // Si no se encuentra el lugar, devolver falso
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => 'Lugar no encontrado'
                ]);
            }

        } catch (\Exception $e) {
            // Registrar el error en el log
            Log::error('Error al editar el lugar: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString() // Guarda el trace del error
            ]);

            // Responder con un error genérico
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'Ocurrió un error al editar el lugar. Por favor, inténtalo de nuevo.'
            ]);
        }
    }
    public function eliminarlugar(Request $request)
    {

        $id = $request->id;

        $consulta = DB::table('lugares')
            ->where('id', $id)
            ->delete();

        if ($consulta) {

            return Response::json([
                'respuesta' => true
            ]);

        } else {

            return Response::json([
                'respuesta' => false
            ]);

        }

    }
    public function eliminartoken(Request $request)
    {

        $id = $request->id;
        $token_id = $request->id_token;

        $consulta = DB::table('tokens_payu')
            ->where('id', $token_id)
            ->delete();

        return Response::json([
            'respuesta' => true
        ]);

    }
    public function guardaridregistration(Request $request)
    {

        $id = $request->id_usuario;
        $registration_id = $request->registrationid;
        $device = $request->device;

        $registration = DB::table('users')
            ->where('id', $id)
            ->update([
                'idregistrationdevice' => $registration_id,
                'device' => $device
            ]);

        return Response::json([
            'response' => true,
            'registrationid' => $registration_id,
            'version' => 1
        ]);

    }
    public function guardarlugar(Request $request)
    {
        try {
            $nombre = $request->nombre;
            $direccion = $request->direccion;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $id = $request->id;

            $lugar = new LugarF;
            $lugar->nombre = $nombre;
            $lugar->direccion = $direccion;
            $lugar->latitude = $latitude;
            $lugar->longitude = $longitude;
            $lugar->id = $id;

            $lugar->save();

            return Response::json([
                'respuesta' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('error' . $e->getMessage());
        }

    }
    public function listaridiomas(Request $request)
    {
        // Llama al método que obtiene los tipos filtrados por el código
        $tipos = Tipo::obtenerTiposPorCodigo('IDI');

        return response()->json($tipos);
    }
    public function listarlugares(Request $request)
    {
        try {
            $id = $request->id;

            $consulta = DB::table('lugares')
                ->where('id', $id)
                ->get();

            if ($consulta) {

                return Response::json([
                    'respuesta' => true,
                    'lugares' => $consulta
                ]);

            } else {

                return Response::json([
                    'respuesta' => false,
                    'lugares' => $consulta
                ]);

            }
        } catch (\Exception $e) {
            // Registrar el error en el log
            Log::error('Error al editar el lugar: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString() // Guarda el trace del error
            ]);

        }



    }
    public function misviajes(Request $request)
    {

        $id = $request->id;
        $fecha = $request->fecha;

        $consulta = "SELECT
		v.id, v.fecha_viaje, v.hora_viaje, v.recoger_en, v.dejar_en, v.codigo, v.detalle_recorrido, v.fk_estado, v.recoger_pasajero,
        c.nombres, c.apellidos, c.celular,
        v.placa, v.marca, v.modelo, v.clase, v.ano, v.color,
        JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) as destinos,
        FROM
            viajes v
        left JOIN conductores c on c.id = v.fk_conductor
        left JOIN vehiculos veh on veh.id = v.fk_vehiculo
        WHERE v.fecha_viaje = '" . $fecha . "' and v.estado_eliminacion is null and v.estado_papelera is null and app_user_id = " . $id . "
        GROUP BY v.id order by v.fecha_viaje asc, v.hora_viaje asc limit 1";

        $servicios = DB::select($consulta);

        if (count($servicios)) {

            return Response::json([
                'response' => true,
                'servicios' => $servicios,
            ]);

        } else {

            return Response::json([
                'response' => false,
            ]);
        }

    }
    public function obtenerusuario(Request $request)
    {
        // Obtenemos el ID del usuario desde la solicitud
        $id = $request->id_usuario;

        try {
            // Buscamos el usuario en la tabla 'users'
            $search = DB::table('users')->where('id', $id)->first();

            // Verificamos si el usuario fue encontrado
            if (!$search) {
                return Response::json([
                    'code' => 'USER_NOT_FOUND'
                ]);
            }

            // Comprobamos si el usuario está baneado
            if ($search->baneado == 1) {
                return Response::json([
                    'code' => 'DISABLED_USER'
                ]);
            }

            // Si el usuario está activo, devolvemos la información
            return Response::json([
                'code' => 'SUCCESSFULLY',
                'json' => $search
            ]);

        } catch (\Exception $e) {
            \Log::error("message", [
                'error' => $e->getMessage()
            ]);
            // Manejo de excepciones: devolvemos un error genérico
            return Response::json([
                'code' => 'FAILS',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    public function proximasrutas(Request $request)
    {

        $user = $request->id;
        $us = User::find($user);

        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i');

        $diezdias = strtotime('4 day', strtotime($fechaActual));
        $diezdias = date('Y-m-d', $diezdias);

        $consulta = "SELECT
		v.id,
		v.fk_estado,
		v.detalle_recorrido,
		v.fecha_viaje as fecha_servicio,
		v.hora_viaje as hora_servicio,
        c.razonsocial,
        est.nombre as nombre_estado,
        est.codigo as codigo_estado,
        v.tipo_traslado,
        t.nombre as nombre_tipo_traslado,
        t.codigo as codigo_tipo_traslado,
        v.tipo_ruta,
        v.recoger_pasajero,
        t2.nombre as tipo_de_ruta,
        t2.codigo as codigo_tipo_ruta,
        sub.coords,
        JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) as destinos,
        (SELECT COUNT(*) FROM viajes v3 left join pasajeros_rutas_qr pax on pax.fk_viaje = v3.id where v3.id = v.id) as total_pasajeros_ruta,
        (SELECT COUNT(*) FROM viajes v4 left join pasajeros_ejecutivos pass on pass.fk_viaje = v4.id where v4.id = v.id) as total_pasajeros_ejecutivos
        FROM
            viajes v
        left JOIN centrosdecosto c on c.id = v.fk_centrodecosto
        left JOIN subcentrosdecosto sub on sub.id = v.fk_subcentrodecosto
        left join destinos d on d.fk_viaje = v.id
        left join estados est on est.id = v.fk_estado
        left join tipos t on t.id = v.tipo_traslado
        left join tipos t2 on t2.id = v.tipo_ruta
        left join pasajeros_rutas_qr prq on prq.fk_viaje = v.id
        WHERE v.fecha_viaje between '" . $fechaActual . "' and '" . $diezdias . "' and prq.id_empleado = " . $us->identificacion . " AND v.fk_estado = 58 and v.estado_eliminacion is null and v.estado_papelera is null
        GROUP BY v.id order by v.fecha_viaje asc, v.hora_viaje asc limit 1";

        $servicios = DB::select($consulta);

        if (count($servicios)) {

            return Response::json([
                'respuesta' => true,
                'servicios' => $servicios,
                'user_id' => $user
            ]);

        } else {

            return Response::json([
                'respuesta' => false,
                'servicios' => $servicios,
                'user_id' => $user
            ]);

        }

    }
    public function reestablecercontrasenacliente(Request $request)
    {

        $password = $request->password;
        $user_id = $request->user_id;

        $user = User::find($user_id);
        $user->password = Hash::make($password);

        if ($user->save()) {

            return Response::json([
                'respuesta' => true,
                'mensaje' => 'Tu contraseña ha sido actualizada de forma exitosa.'
            ]);

        } else {

            return Response::json([
                'respuesta' => false
            ]);

        }

    }
    public function reintentarpago(Request $request)
    {

        $service_id = $request->servicios_aplicacion_id;
        $card_id = $request->id_tarjeta;
        $lastFour = $request->lastFour;
        $paymentMethod = $request->paymentMethod;

        $servicio = DB::table('viajes_aplicacion')
            ->where('id', $service_id)
            ->first();

        $queryCard = DB::table('tokens_payu')
            ->where('id', $card_id)
            ->first();

        //pago de servicio
        $id_usuario = $servicio->user_id;
        $valor_servicio = $servicio->valor;

        $usuario = User::find($id_usuario);

        $apiUrl = "https://sandbox.wompi.co/v1/transactions"; //URL DE PRUEBAS
        //$apiUrl = "https://production.wompi.co/v1/transactions"; //URL DE PRODUCCIÓN
        $valorReal = $valor_servicio . '00';

        //Asigno a psi lo que traigo de la consulta
        $psi = DB::table('viajes_aplicacion')
            ->where('id', $service_id)
            ->first();

        //Asigno a la variable lo que trae psi
        $pago_servicio_id = $psi->pago_servicio_id;

        $pending = DB::table('pago_servicios')
            ->where('id', $pago_servicio_id)
            ->update([
                'estado' => 'PENDING'
            ]);

        $referenceOld = DB::table('referencias_payu')
            ->where('servicio_aplicacion_id', $service_id)
            ->first();

        $data = [
            "amount_in_cents" => intval($valorReal),
            "currency" => "COP",
            "customer_email" => $usuario->email,
            "payment_method" => [
                "installments" => 1
            ],
            "reference" => $referenceOld->reference_code,
            "payment_source_id" => $queryCard->fuente_pago,
        ];

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer prv_test_aZ1VdvKqmz91uyEBsiqQAuswY2ZSpFIl', //links pruebas aotour
            //'Authorization: Bearer prv_prod_WJ9PkFir6uPwOPwstzfI8LsfPPedpRda', //links productivo aotour
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($result);

        $estado = $result->data->status;
        $transaction = $result->data->id;

        if ($estado === 'APPROVED') {

            $cardPay = $result->data->payment_method->extra->last_four;
            $cardType = $result->data->payment_method->extra->brand;

            $updatePago = DB::table('pago_servicios')
                ->where('id', $pago_servicio_id)
                ->update([
                    'order_id' => $result->transactionResponse->orderId,
                    'numero_tarjeta' => '************' . $lastFour,
                    'tipo_tarjeta' => $cardType,
                    'estado' => $estado
                ]);

            if ($updatePago) {

                return Response::json([
                    'response' => true,
                    'transaccion' => $result,
                    'estado' => $estado,
                    'transaccion' => $transaction
                ]);

            } else {

                return Response::json([
                    'response' => false,
                    'transaccion' => $result,
                    'estado' => $estado,
                    'transaccion' => $transaction
                ]);

            }

        } else {

            return Response::json([
                'response' => true,
                'transaccion' => $result,
                'estado' => $estado,
                'transaccion' => $transaction
            ]);

        }

    }

    // Revisar por erro en el condicional
    public function servicioactivo(Request $request)
    {

        $user = User::find($request->id);

        $fecha = date('Y-m-d');
        $diaanterior = strtotime('-1 day', strtotime($fecha));
        $diaanterior = date('Y-m-d', $diaanterior);

        $diasiguiente = strtotime('+1 day', strtotime($fecha));
        $diasiguiente = date('Y-m-d', $diasiguiente);

        $consulta = "SELECT
		v.id,
		v.fk_estado,
		v.detalle_recorrido,
		v.fecha_viaje as fecha_servicio,
		v.hora_viaje as hora_servicio,
        c.razonsocial,
        est.nombre as nombre_estado,
        est.codigo as codigo_estado,
        v.tipo_traslado,
        v.codigo,
        t.nombre as nombre_tipo_traslado,
        t.codigo as codigo_tipo_traslado,
        v.tipo_ruta,
        v.recoger_pasajero,
        t2.nombre as tipo_de_ruta,
        t2.codigo as codigo_tipo_ruta,
        sub.coords,
        cond.primer_nombre, cond.primer_apellido, cond.celular, cond.foto,
        veh.placa, veh.marca, veh.modelo, veh.clase, veh.ano, veh.color,
        ps.valor, ps.numero_tarjeta, ps.tipo_tarjeta, ps.estado,
        JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) as destinos,
        (SELECT COUNT(*) FROM viajes v3 left join pasajeros_rutas_qr pax on pax.fk_viaje = v3.id where v3.id = v.id) as total_pasajeros_ruta,
        (SELECT COUNT(*) FROM viajes v4 left join pasajeros_ejecutivos pass on pass.fk_viaje = v4.id where v4.id = v.id) as total_pasajeros_ejecutivos
        FROM
            viajes v
        left JOIN centrosdecosto c on c.id = v.fk_centrodecosto
        left JOIN subcentrosdecosto sub on sub.id = v.fk_subcentrodecosto
        left join destinos d on d.fk_viaje = v.id
        left join estados est on est.id = v.fk_estado
        left join tipos t on t.id = v.tipo_traslado
        left join tipos t2 on t2.id = v.tipo_ruta
        left join conductores cond on cond.id = v.fk_conductor
        left join vehiculos veh on veh.id = v.fk_vehiculo
        left join servicios_aplicacion sa on sa.servicio_id = viajes.id
        left join pago_servicios ps on ps.id = sa.pago_servicio_id
        WHERE v.fecha_viaje between '" . $diaanterior . "' and '" . $diasiguiente . "' AND v.fk_estado = 59 and v.estado_eliminacion is null and v.estado_papelera is null and v.app_user_id = " . $user->id . "
        GROUP BY v.id order by v.fecha_viaje asc, v.hora_viaje asc limit 1";
        $viajes = DB::select($consulta);

        $consulta2 = "SELECT
		v.id,
		v.fk_estado,
		v.detalle_recorrido,
		v.fecha_viaje as fecha_servicio,
		v.hora_viaje as hora_servicio,
        c.razonsocial,
        est.nombre as nombre_estado,
        est.codigo as codigo_estado,
        v.tipo_traslado,
        v.codigo,
        t.nombre as nombre_tipo_traslado,
        t.codigo as codigo_tipo_traslado,
        v.tipo_ruta,
        v.recoger_pasajero,
        t2.nombre as tipo_de_ruta,
        t2.codigo as codigo_tipo_ruta,
        sub.coords,
        cond.primer_nombre, cond.primer_apellido, cond.celular, cond.foto,
        veh.placa, veh.marca, veh.modelo, veh.clase, veh.ano, veh.color,
        JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) as destinos,
        (SELECT COUNT(*) FROM viajes v3 left join pasajeros_rutas_qr pax on pax.fk_viaje = v3.id where v3.id = v.id) as total_pasajeros_ruta,
        (SELECT COUNT(*) FROM viajes v4 left join pasajeros_ejecutivos pass on pass.fk_viaje = v4.id where v4.id = v.id) as total_pasajeros_ejecutivos
        FROM
            viajes v
        left JOIN centrosdecosto c on c.id = v.fk_centrodecosto
        left JOIN subcentrosdecosto sub on sub.id = v.fk_subcentrodecosto
        left join destinos d on d.fk_viaje = v.id
        left join estados est on est.id = v.fk_estado
        left join tipos t on t.id = v.tipo_traslado
        left join tipos t2 on t2.id = v.tipo_ruta
        left join conductores cond on cond.id = v.fk_conductor
        left join vehiculos veh on veh.id = v.fk_vehiculo
        WHERE v.fecha_viaje between '" . $diaanterior . "' and '" . $diasiguiente . "' AND v.fk_estado = 60 and v.estado_eliminacion is null and v.estado_papelera is null and v.app_user_id = " . $user->id . "
        GROUP BY v.id order by v.fecha_viaje asc, v.hora_viaje asc limit 1";
        $servicios_calificar = DB::select($consulta2);

        $notificacion = DB::table('notificaciones')
            ->where('id_usuario', $request->user_id)
            ->whereNull('leido')
            ->get();

        $notificacion = count($notificacion);

        /*    if (count($servicios_activos)) {

                return Response::json([
                    'response' => true,
                    'servicios' => $servicios_activos,
                    'notificacion' => $notificacion,
                    'calificar' => $servicios_calificar
                ]);

            }else {

                return Response::json([
                    'response' => false,
                    'notificacion' => $notificacion,
                    'calificar' => $servicios_calificar
                ]);

            }*/

    }
    public function serviciospedidos(Request $request)
    {

        $user_id = $request->id;

        $user = User::find($user_id);

        $consulta = "SELECT
		v.id,
		v.fk_estado,
		v.detalle_recorrido,
		v.fecha_viaje as fecha_servicio,
		v.hora_viaje as hora_servicio,
        c.razonsocial,
        est.nombre as nombre_estado,
        est.codigo as codigo_estado,
        v.tipo_traslado,
        v.codigo,
        t.nombre as nombre_tipo_traslado,
        t.codigo as codigo_tipo_traslado,
        v.tipo_ruta,
        v.recoger_pasajero,
        t2.nombre as tipo_de_ruta,
        t2.codigo as codigo_tipo_ruta,
        sub.coords,
        sa.user_id,
        cond.primer_nombre, cond.primer_apellido, cond.celular, cond.foto,
        veh.placa, veh.marca, veh.modelo, veh.clase, veh.ano, veh.color,
        ps.valor, ps.numero_tarjeta, ps.tipo_tarjeta, ps.estado,
        sa.
        JSON_ARRAYAGG(JSON_OBJECT('direccion', d.direccion, 'coordenadas', d.coordenadas, 'orden', d.orden)) as destinos,
        (SELECT COUNT(*) FROM viajes v3 left join pasajeros_rutas_qr pax on pax.fk_viaje = v3.id where v3.id = v.id) as total_pasajeros_ruta,
        (SELECT COUNT(*) FROM viajes v4 left join pasajeros_ejecutivos pass on pass.fk_viaje = v4.id where v4.id = v.id) as total_pasajeros_ejecutivos
        FROM
            viajes v
        left JOIN centrosdecosto c on c.id = v.fk_centrodecosto
        left JOIN subcentrosdecosto sub on sub.id = v.fk_subcentrodecosto
        left join destinos d on d.fk_viaje = v.id
        left join estados est on est.id = v.fk_estado
        left join tipos t on t.id = v.tipo_traslado
        left join tipos t2 on t2.id = v.tipo_ruta
        left join conductores cond on cond.id = v.fk_conductor
        left join vehiculos veh on veh.id = v.fk_vehiculo
        left join viajes_aplicacion sa on sa.servicio_id = viajes.id
        left join pago_servicios ps on ps.id = sa.pago_servicio_id
        WHERE sa.user_id = " . $user_id . " AND v.fk_estado = 58 and v.estado_eliminacion is null and v.estado_papelera is null and sa.cancelado is null
        GROUP BY v.id order by v.fecha_viaje asc, v.hora_viaje asc limit 1";

        $servicios = DB::select($consulta);

        if (count($servicios)) {

            return Response::json([
                'respuesta' => true,
                'servicios' => $servicios,
                'user_id' => $user_id
            ]);

        } else {

            return Response::json([
                'respuesta' => false
            ]);

        }

    }
}
