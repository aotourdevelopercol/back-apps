<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class estados extends Model
{

    use HasFactory;
    protected $table = 'estados';

    // funcion para obtener todos los estados pasando el estado maestro
    public static function getEstados($estado)
    {
        return DB::table('estados as e')
            ->leftJoin('estados_maestros as em', 'em.id', '=', 'e.fk_estados_maestros')
            ->where('em.codigo', $estado)
            ->select('e.*')
            ->get();

    }
}
