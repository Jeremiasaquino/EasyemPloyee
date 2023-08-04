<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamento';

    protected $fillable = [
      //  'user_id',
        'departamento',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function empleado()
{
    return $this->hasMany(Empleado::class);
}

public function delete()
{
    if ($this->empleado()->count() > 0) {
        // No permitir la eliminación si hay algún empleado asignado al departamento
        return false;
    }

    parent::delete();
}

    
}
