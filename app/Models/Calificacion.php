<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'calificacion_viajes';

    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'fk_viaje',
        'fk_user',
        'pasajero_ejecutivo_link',
        'pasajero_ruta_link',
        'calificacion',
        'comentario',
    ];

    // Las marcas de tiempo ya se gestionan automáticamente con $timestamps
}
