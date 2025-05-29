<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class PayRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'idFile',
        'id_archivo_detalle',
        'id_register',
        'consecutivo',
        'tipo_documento',
        'numero_documento',
        'fecha_registro',
        'fecha_pago',
        'id_proceso'
    ];


    protected function fechaRegistroFormatted(): Attribute // Nombre del accessor
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['fecha_registro'])) {
                    try {
                        return Carbon::createFromFormat('m/d/Y H:i:s', $attributes['fecha_registro'])->format('d/m/Y');
                    } catch (\Exception $e) {
                         try {
                            return Carbon::parse($attributes['fecha_registro'])->format('d/m/Y');
                         } catch(\Exception $e2) {
                            return 'N/A'; // Error en ambos parseos
                         }
                    }
                }
                return 'N/A'; 
            }
        );
    }

    protected function fechaPagoFormatted(): Attribute // Nombre del accessor
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['fecha_pago'])) {
                    try {
                        return Carbon::createFromFormat('m/d/Y H:i:s', $attributes['fecha_pago'])->format('d/m/Y');
                    } catch (\Exception $e) {
                         try {
                            return Carbon::parse($attributes['fecha_pago'])->format('d/m/Y');
                         } catch(\Exception $e2) {
                            return 'N/A'; // Error en ambos parseos
                         }
                    }
                }
                return 'N/A'; 
            }
        );
    }
}
