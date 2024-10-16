<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    // Si la tabla no sigue la convención plural, puedes especificarla:
        protected $table = 'users'; // Nombre de la tabla

        public $incrementing = true;
    
        protected $fillable = ['nombre', 'email'];
}
