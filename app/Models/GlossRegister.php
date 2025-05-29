<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class GlossRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_presentacion',
        'id_user',
        'fecha_registro',
        'bdua_fecha_nacimiento',
        'validado_bdua_nombres_documento',
        'validado_bdua_renec',
        'validado_bdua_eps',
        'validado_bdua_renec_vigencia',
        'validado_bdua_fecha',
        'validado_bdua_ftoma_fdefuncion',
        'validado_bdua_codmunicipio_afiliacion',
        'validado_bdua_codigo_eps_afiliacion_toma',
        'validado_sismuestra_nodoc_tipdoc',
        'validado_sismuestra_fecha_toma',
        'validado_sismuestra_fecha_resultado',
        'validado_sismuestra_fecha',
        'validado_cons_cups_anticuerpo',
        'validado_cons_cups2_anticuerpo',
        'validado_cons_cups_vs_cups2_anticuerpo',
        'validado_cons_cups_vs_cups2_anticuerpo_resultado',
        'validado_cons_codigo_habilitacion',
        'validado_cons_nit',
        'validado_cons_nit_vs_codigo_habilitacion',
        'validado_cons_no_presentado_anterior_con_pago',
        'validado_cons_duplicado_misma_ventana',
        'validado_cons_valor_registro',
        'validado_cons_id_muestra',
        'validado_cons_no_pagado_historico_por_id_muestra_concepto',
        'validado_cons_no_pagado_historico_por_id_muestra_concepto_doc',
        'validado_cons_no_pagado_historico_por_doc_fecha_toma_cups_concepto',
        
        'idFile',
        'id_archivo_detalle',
        'id_register',
        'consecutivo',
        'tipo_documento',
        'numero_documento',
        'validado_cons_fecha_toma',
        'validado_cons_fecha_toma_vs_fecha_resultado',
        'validado_cons_codigo_habilitacion_toma_pros',
        'validado_cons_nit_ips_procesamiento',
        'validado_cons_compra_prueba',
        'validado_cons_prueba_et',
        'validado_cons_fecha',
        'validado_cons_supera_valor',
        'validado_cons_nit_toma_vs_nit_proceso',
        'validado_cons_duplicado',
        'validado_cons_nit_toma_vs_codigohab_toma',
        'validado_cons_nit_proc_vs_codigohab_proc',
        'validado_cons_valor_vs_tipo_procedimiento',
        'validado_cons_pt5_solo_procesamiento',
        'validado_cons_pt5_solo_toma',
        'validado_cons_pt5_solo_toma_pros_pagado',
        'validado_cons_pt5_solo_toma_pros_pagado_doc',
        'validado_cons_pt5_codigo_habilitacion_toma_pros',
        'validado_cons_pt5_nit_proc_vs_codigohab_proc',
        'validado_cons_pt6_solo_toma',
        'validado_cons_pt6_solo_procesamiento',
        'validado_cons_pt6_solo_procesamiento_toma_pagado',
        'validado_cons_pt6_solo_procesamiento_toma_pagado_doc',
        'validado_cons_pt6_nit_toma_vs_codigohab_toma',
        'validado_cons_pt6_codigo_habilitacion_toma_pros',
        'validado_cons_tipo_present_proces_arc2',
        'validado_cons_tipo_presentacion_toma',
        'validado_cons_tipo_presentacion_procesamiento',
        'aplica_diferencial_2'   
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

    protected function bduaFechaNacimientoFormatted(): Attribute // Nombre del accessor
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['bdua_fecha_nacimiento'])) {
                    try {
                        return Carbon::createFromFormat('m/d/Y H:i:s', $attributes['bdua_fecha_nacimiento'])->format('d/m/Y');
                    } catch (\Exception $e) {
                         try {
                            return Carbon::parse($attributes['bdua_fecha_nacimiento'])->format('d/m/Y');
                         } catch(\Exception $e2) {
                            return 'N/A'; // Error en ambos parseos
                         }
                    }
                }
                return 'N/A'; 
            }
        );
    }

    protected function validadoBduaFechaFormatted(): Attribute // Nombre del accessor
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['validado_bdua_fecha'])) {
                    try {
                        return Carbon::createFromFormat('m/d/Y H:i:s', $attributes['validado_bdua_fecha'])->format('d/m/Y');
                    } catch (\Exception $e) {
                         try {
                            return Carbon::parse($attributes['validado_bdua_fecha'])->format('d/m/Y');
                         } catch(\Exception $e2) {
                            return 'N/A'; // Error en ambos parseos
                         }
                    }
                }
                return 'N/A'; 
            }
        );
    }

    protected function validadoSismuestraFechaFormatted(): Attribute // Nombre del accessor
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['validado_sismuestra_fecha'])) {
                    try {
                        return Carbon::createFromFormat('m/d/Y H:i:s', $attributes['validado_sismuestra_fecha'])->format('d/m/Y');
                    } catch (\Exception $e) {
                         try {
                            return Carbon::parse($attributes['validado_sismuestra_fecha'])->format('d/m/Y');
                         } catch(\Exception $e2) {
                            return 'N/A'; // Error en ambos parseos
                         }
                    }
                }
                return 'N/A'; 
            }
        );
    }

    protected function validadoConsFechaFormatted(): Attribute // Nombre del accessor
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['validado_cons_fecha'])) {
                    try {
                        return Carbon::createFromFormat('m/d/Y H:i:s', $attributes['validado_cons_fecha'])->format('d/m/Y');
                    } catch (\Exception $e) {
                         try {
                            return Carbon::parse($attributes['validado_cons_fecha'])->format('d/m/Y');
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
