<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    protected $fillable = [
        'idFile',
        'consecutivo',
        'tipo_documento',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'codigo_cups',
        'codigo_cups2_anticuerpos',
        'registro_sanitario_prueba',
        'codigo_eps',
        'nombre_eps',
        'conmpra_masiva',
        'valor_prueba',
        'nit_ips_tomo_muestra',
        'nombre_ips_tomo_muestra',
        'codigo_habilitacion_ips_tomo_muestra',
        'valor_toma_muestra_a_cobrar_adres',
        'no_factura_muestra',
        'nit_laboratorio_procesamiento',
        'nombre_laboratorio_procesamiento',
        'codigo_habilitacion_procesamiento',
        'valor_procesamiento_a_cobrar_adres',
        'no_factura_procesamiento',
        'fecha_toma',
        'resultado_prueba',
        'fecha_resultado',
        'tipo_procedimiento',
        'concepto_presentacion',
        'id_examen',
        'tipo_archivo',
    ];
}
