<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Empleado;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'codigo_empleado',
        'empleado_id',
        'api_token',
        'email',
        'password',
        'role', // Nuevo campo de roles
        'estado', // Nuevo campo de estados
        'foto',
        'foto_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role'  => 'string', 
        'estado'  => 'string',
    ];

    // public function tokens()
    // {
    //     return $this->hasMany(Empleado::class);
    // }

    /**
     * Obtener el empleado asociado a este usuario.
     */

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function role()
    {
        return $this->belongsToMany(Empleado::class);
    }
}
