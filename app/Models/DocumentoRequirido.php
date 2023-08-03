<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentoRequirido extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'documentos',
        'documentos_id'
        // 'curriculum_vitae',
        // 'cedula_identidad',
        // 'seguro_social',
        // 'titulos_certificados',
        // 'otros_documentos',
    ];

    protected $casts = [
        'documentos' => 'array'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
