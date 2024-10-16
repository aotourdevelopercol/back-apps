<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject; // Importar la interfaz JWTSubject

class Users extends Authenticatable implements MustVerifyEmail, JWTSubject // Implementar JWTSubject
{
    use Notifiable, HasApiTokens;
    public function isAdmin()
    {
        return $this->role === 'admin'; // Ajusta esto si tu lógica es diferente
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Si tienes un campo de rol
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Obtiene el identificador único para el usuario que se utiliza en el JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Devuelve la clave primaria del usuario
    }

    /**
     * Obtiene el conjunto de reclamaciones para el JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // Puedes agregar reclamos personalizados si lo deseas
    }
}