<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tss extends Model
{
    use HasFactory;

    protected $table = 'tss';

    protected $fillable = ['nombre','porcentaje',];

}