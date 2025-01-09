<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlossRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'idFile',
        'id_archivo_detalle',
        'id_archivo',
        'serial',
        'fecha_registro',
        'validado_bdua_nombres_documento',
        'validado_bdua_eps',
        'validado_bdua_renec',
        'validado_bdua_renec_vigencia',
        'validado_bdua_ftoma_fdefuncion',
        'validado_sismuestra_nodoc_tipdoc',
        'validado_sismuestra_fecha_toma',
        'validado_sismuestra_fecha_resultado',
        'validado_cons_fecha_toma',
        'validado_cons_fecha_toma_vs_fecha_resultado',
        'validado_cons_codigo_habilitacion_toma_pros',
        'validado_cons_nit_ips_procesamiento',
        'validado_cons_compra_prueba',
        'validado_cons_prueba_et',
        'validado_cons_fecha',
        'validado_cons_no_presentado_anterior_con_pago',
        'validado_cons_duplicado',
        'aplica_diferencial',
        'validado_cons_nit_toma_vs_codigohab_toma',
        'validado_cons_nit_proc_vs_codigohab_proc',
        'validado_cons_valor_vs_tipo_procedimiento',
        'validado_cons_id_muestra',
        'validado_cons_pt5_solo_procesamiento',
        'validado_cons_pt5_solo_toma',
        'validado_cons_pt5_solo_toma_pros_pagado',
        'validado_cons_pt5_solo_toma_pros_pagado_doc',
        'validado_cons_pt5_codigo_habilitacion_toma_pros',
        'validado_cons_pt5_nit_proc_vs_codigohab_proc',
    ];
}
