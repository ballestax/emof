<?php

namespace App\Imports;

use App\Models\GlossRegister;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;

class GlossImport implements ToModel
{
    protected $tipoArchivo;
    protected $fileId;

    public function __construct($tipoArchivo, $fileId)
    {
        $this->tipoArchivo = $tipoArchivo;
        $this->fileId = $fileId;
    }


    public function model(array $row)
    {

        Log::info('FileID: ' . $this->fileId);
        // Procesar los datos según el tipo de archivo
        if ($this->tipoArchivo == 'tipo_2') {
            // Procesar datos para el tipo 1
            return new GlossRegister([
                'idFile' => $this->fileId,
                'id_archivo_detalle' => $row[0] ?? null,
                'id_archivo' => $row[1] ?? null,
                'serial' => $row[2] ?? null,
                'fecha_registro' => $row[30] ?? null,
                'validado_bdua_nombres_documento' => $row[31] ?? null,
                'validado_bdua_eps' => $row[32] ?? null,
                'validado_bdua_renec' => $row[33] ?? null,
                'validado_bdua_renec_vigencia' => $row[34] ?? null,
                'validado_bdua_ftoma_fdefuncion' => $row[35] ?? null,
                'validado_sismuestra_nodoc_tipdoc' => $row[36] ?? null,
                'validado_sismuestra_fecha_toma' => $row[37] ?? null,
                'validado_sismuestra_fecha_resultado' => $row[38] ?? null,
                'validado_cons_fecha_toma' => $row[39] ?? null,
                'validado_cons_fecha_toma_vs_fecha_resultado' => $row[40] ?? null,
                'validado_cons_codigo_habilitacion_toma_pros' => $row[41] ?? null,
                'validado_cons_nit_ips_procesamiento' => $row[42] ?? null,
                'validado_cons_compra_prueba' => $row[43] ?? null,
                'validado_cons_prueba_et' => $row[44] ?? null,
                'validado_cons_fecha' => $row[45] ?? null,
                'validado_cons_no_presentado_anterior_con_pago' => $row[46] ?? null,
                'validado_cons_duplicado' => $row[47] ?? null,
                'aplica_diferencial' => $row[48] ?? null,
                'validado_cons_nit_toma_vs_codigohab_toma' => $row[49] ?? null,
                'validado_cons_nit_proc_vs_codigohab_proc' => $row[50] ?? null,
                'validado_cons_valor_vs_tipo_procedimiento' => $row[51] ?? null,
                'validado_cons_id_muestra' => $row[52] ?? null,
                'validado_cons_pt5_solo_procesamiento' => $row[53] ?? null,
                'validado_cons_pt5_solo_toma' => $row[54] ?? null,
                'validado_cons_pt5_solo_toma_pros_pagado' => $row[55] ?? null,
                'validado_cons_pt5_solo_toma_pros_pagado_doc' => $row[56] ?? null,
                'validado_cons_pt5_codigo_habilitacion_toma_pros' => $row[57] ?? null,
                'validado_cons_pt5_nit_proc_vs_codigohab_proc' => $row[58] ?? null,
            ]);
        } elseif ($this->tipoArchivo == 'tipo_1') {
            // Procesar datos para el tipo 2
            return new GlossRegister([
                'idFile' => $this->fileId,
                'id_archivo_detalle' => $row[0] ?? null,
                'id_archivo' => $row[1] ?? null,
                'id_user' => $row[25] ?? null,
                'fecha_registro' => $row[30] ?? null,
                'fecha_registro' => $row[26] ?? null,
                'bdua_fecha_nacimiento' => $row[27] ?? null,
                'validado_bdua_nombres_documento' => $row[28] ?? null,
                'validado_bdua_renec' => $row[29] ?? null,
                'validado_bdua_eps' => $row[30] ?? null,
                'validado_bdua_renec_vigencia' => $row[31] ?? null,
                'validado_bdua_fecha' => $row[32] ?? null,
                'validado_bdua_ftoma_fdefuncion' => $row[33] ?? null,
                'validado_bdua_codmunicipio_afiliacion' => $row[34] ?? null,
                'validado_bdua_codigo_eps_afiliacion_toma' => $row[35] ?? null,
                'validado_sismuestra_nodoc_tipdoc' => $row[36] ?? null,
                'validado_sismuestra_fecha_toma' => $row[37] ?? null,
                'validado_sismuestra_fecha_resultado' => $row[38] ?? null,
                'validado_sismuestra_fecha' => $row[39] ?? null,
                'validado_cons_cups_anticuerpo' => $row[40] ?? null,
                'validado_cons_cups2_anticuerpo' => $row[41] ?? null,
                'validado_cons_cups_vs_cups2_anticuerpo' => $row[42] ?? null,
                'validado_cons_cups_vs_cups2_anticuerpo_resultado' => $row[43] ?? null,
                'validado_cons_codigo_habilitacion' => $row[44] ?? null,
                'validado_cons_nit' => $row[45] ?? null,
                'validado_cons_nit_vs_codigo_habilitacion' => $row[46] ?? null,
                'validado_cons_no_presentado_anterior_con_pago' => $row[47] ?? null,
                'validado_cons_duplicado_misma_ventana' => $row[48] ?? null,
                'validado_cons_valor_registro' => $row[49] ?? null,
                'validado_cons_id_muestra' => $row[50] ?? null,
                'validado_cons_no_pagado_historico_por_id_muestra_concepto' => $row[51] ?? null,
                'validado_cons_no_pagado_historico_por_id_muestra_concepto_doc' => $row[52] ?? null,
                'validado_cons_no_pagado_historico_por_doc_fecha_toma_cups_concepto' => $row[53] ?? null,

            ]);
        }
        // Si el tipo de archivo no coincide, puedes manejarlo de otra forma o lanzar un error.
    }
}
