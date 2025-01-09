<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'idCanasta',
        'GUID',
        'tipoArchivo',
        'esquema',
        'registros',
        'estado',
        'fechaCargue',
        'file',
    ];

    public function getFechaCargueAttribute($value)
    {
        return Carbon::parse($value);
    }
}
