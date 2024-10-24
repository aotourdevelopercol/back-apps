<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tipo extends Model
{
    use HasFactory;

    protected $table = 'tipos';

    // Método para obtener tipos con LEFT JOIN
    public static function obtenerTiposPorCodigo($codigo)
    {
        return DB::table('tipos as t')
            ->leftJoin('tipo_maestros as tm', 'tm.id', '=', 't.fk_tipo_maestros')
            ->where('tm.codigo', $codigo)
            ->select('t.*') // Selecciona todas las columnas de 'tipos'
            ->get();
    }


    public static function obtenerTipoPorCodigoYId($id)
    {
        return DB::table('tipos as t')
            ->leftJoin('tipo_maestros as tm', 'tm.id', '=', 't.fk_tipo_maestros')
            ->where('tm.codigo', 'IDI')
            ->where('t.id', $id)
            ->select('t.*') // Selecciona todas las columnas de 'tipos'
            ->first(); // Devuelve el primer resultado
    }

    // Método para obtener todos los tipos estado pasando estado maestro
    public static function obtenerTiposPorEstadoMaestro($estado)
    {
        return DB::table('tipos as t')
            ->leftJoin('tipo_maestros as tm', 'tm.id', '=', 't.fk_tipo_maestros')
            ->where('tm.codigo', $estado) // Filtro por el código "TIP_VIAJE"
            ->select('t.*') // Selecciona todas las columnas de la tabla 'tipos'
            ->get(); // Obtén los resultados
    }
}