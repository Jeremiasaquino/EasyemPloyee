<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'cargo';
    protected $fillable = ['cargo'];

    public function empleado()
    {
        return $this->hasMany(Empleado::class);
    }
}
