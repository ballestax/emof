<?php

namespace App\Models;

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
        'file'
    ];

}
