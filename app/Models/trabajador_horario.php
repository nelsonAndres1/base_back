<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class trabajador_horario extends Model
{
    use HasFactory;

    protected $table = 'trabajador_horario';
    public $timestamps = false;
}
