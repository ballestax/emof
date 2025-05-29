<?php

namespace App\Imports;

use App\Models\GlossRegister;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GlossImport implements ToModel, WithHeadingRow
{
    protected $tipoArchivo;
    protected $fileId;

    protected $table = 'gloss_registers';

    public function __construct($tipoArchivo, $fileId)
    {
        $this->tipoArchivo = $tipoArchivo;
        $this->fileId = $fileId;
    }

    public function prepareForValidation(array $row): array
    {
        $processedRow = [];
        foreach ($row as $key => $value) {
            // Convert each header key to lowercase using mb_strtolower for UTF-8 safety
            $processedRow[mb_strtolower($key, 'UTF-8')] = $value;
        }
        return $processedRow;
    }

    public function model(array $row)
    {
        Log::debug("Fila completa recibida: " . json_encode($row)); // Loguea toda la fila
        Log::debug("Tipo de archivo para esta fila: " . $this->tipoArchivo);

        if ($this->tipoArchivo == 'tipo_1') {
            Log::debug("Procesando como tipo_1. Valor para validado_cons_fecha_toma: " . json_encode($row['validado_cons_fecha_toma'] ?? 'NO ENCONTRADO EN FILA'));
            return $this->procesarTipo1($row);
        } elseif ($this->tipoArchivo == 'tipo_2') {
            Log::debug("Procesando como tipo_2. ¿Existe validado_cons_fecha_toma?: " . (isset($row['validado_cons_fecha_toma']) ? 'Sí' : 'No'));
            if (isset($row['validado_cons_fecha_toma'])) {
                Log::debug("Valor para validado_cons_fecha_toma en tipo_2: " . json_encode($row['validado_cons_fecha_toma']));
            }
            return $this->procesarTipo2($row, $this->fileId);
        } else {
            Log::warning("Tipo de archivo no reconocido: " . $this->tipoArchivo);
            return null;
        }
    }

    private function procesarTipo1(array $row)
    {
         Log::debug("Procesando tipo 1:: ".  json_encode($row));
        return new GlossRegister([
            'id_register' => $row['id_archivo'],
            //'id_user' => $row[25] ?? null,
            //'fecha_registro' => $row[30] ?? null,
            'consecutivo' => $row['consecutivo'] ?? null,
            'tipo_documento' => $row['tipo_documento'] ?? null,
            'numero_documento' => $row['numero_documento'] ?? null,
            'validado_bdua_nombres_documento' => $row['validado_bdua_nombres_documento'] ?? null,
            'validado_bdua_eps' => $row['validado_bdua_eps'] ?? null,
            'validado_bdua_renec' => $row['validado_bdua_renec'] ?? null,
            'validado_bdua_renec_vigencia' => $row['validado_bdua_renec_vigencia'] ?? null,
            'validado_bdua_ftoma_fdefuncion' => $row['validado_bdua_ftoma_fdefuncion'] ?? null,
            'validado_sismuestra_nodoc_tipdoc' => $row['validado_sismuestra_nodoc_tipdoc'] ?? null,
            'validado_sismuestra_fecha_toma' => $row['validado_sismuestra_fecha_toma'] ?? null,
            'validado_sismuestra_fecha_resultado' => $row['validado_sismuestra_fecha_resultado'] ?? null,
            'validado_sismuestra_fecha' => $row['validado_sismuestra_fecha'] ?? null,
            'validado_cons_fecha_toma_vs_fecha_resultado' => $row['validado_cons_fecha_toma_vs_fecha_resultado'] ?? null,
            'validado_cons_fecha_toma' => $row['validado_cons_fecha_toma'],
            'validado_cons_codigo_habilitacion_toma_pros' => $row['validado_cons_codigo_habilitacion_toma_pros'] ?? null,
            'validado_cons_nit_ips_procesamiento' => $row['validado_cons_nit_ips_procesamiento'] ?? null,
            'validado_cons_compra_prueba' => $row['validado_cons_compra_prueba'] ?? null,
            'validado_cons_prueba_et' => $row['validado_cons_prueba_et'] ?? null,
            'validado_cons_fecha' => $row['validado_cons_fecha'] ?? null,
            'validado_cons_no_presentado_anterior_con_pago' => $row['validado_cons_no_presentado_anterior_con_pago'] ?? null,
            'validado_cons_supera_valor' => $row['validado_cons_supera_valor'] ?? null,
            'validado_cons_nit_toma_vs_nit_proceso' => $row['validado_cons_nit_toma_vs_nit_proceso'] ?? null,
            'validado_bdua_codmunicipio_afiliacion' => $row['validado_bdua_codmunicipio_afiliacion'] ?? null,
            'validado_cons_duplicado' => $row['validado_cons_duplicado'] ?? null,
            'validado_cons_nit_toma_vs_codigohab_toma' => $row['validado_cons_nit_toma_vs_codigohab_toma'] ?? null,
            'validado_cons_nit_proc_vs_codigohab_proc' => $row['validado_cons_nit_proc_vs_codigohab_proc'] ?? null,
            'validado_cons_valor_vs_tipo_procedimiento' => $row['validado_cons_valor_vs_tipo_procedimiento'] ?? null,
            'validado_cons_id_muestra' => $row['validado_cons_id_muestra'] ?? null,
            'validado_cons_pt5_solo_procesamiento' => $row['validado_cons_pt5_solo_procesamiento'] ?? null,
            'validado_cons_pt5_solo_toma' => $row['validado_cons_pt5_solo_toma'] ?? null,
            'validado_cons_pt5_solo_toma_pros_pagado' => $row['validado_cons_pt5_solo_toma_pros_pagado'] ?? null,
            'validado_cons_pt5_solo_toma_pros_pagado_doc' => $row['validado_cons_pt5_solo_toma_pros_pagado_doc'] ?? null,
            'validado_cons_pt5_codigo_habilitacion_toma_pros' => $row['validado_cons_pt5_codigo_habilitacion_toma_pros'] ?? null,
            'validado_cons_pt5_nit_proc_vs_codigohab_proc' => $row['validado_cons_pt5_nit_proc_vs_codigohab_proc'] ?? null,
            'validado_cons_pt6_solo_toma' => $row['validado_cons_pt6_solo_toma'] ?? null,
            'validado_cons_pt6_solo_procesamiento' => $row['validado_cons_pt6_solo_procesamiento'] ?? null,
            'validado_cons_pt6_solo_procesamiento_toma_pagado' => $row['validado_cons_pt6_solo_procesamiento_toma_pagado'] ?? null,
            'validado_cons_pt6_solo_procesamiento_toma_pagado_doc' => $row['validado_cons_pt6_solo_procesamiento_toma_pagado_doc'] ?? null,
            'validado_cons_pt6_nit_toma_vs_codigohab_toma' => $row['validado_cons_pt6_nit_toma_vs_codigohab_toma'] ?? null,
            'validado_cons_pt6_codigo_habilitacion_toma_pros' => $row['validado_cons_pt6_codigo_habilitacion_toma_pros'] ?? null,
            'validado_cons_cups_anticuerpo' => $row['validado_cons_cups_anticuerpo'] ?? null,
            'validado_cons_cups2_anticuerpo' => $row['validado_cons_cups2_anticuerpo'] ?? null,
            'validado_cons_cups_vs_cups2_anticuerpo' => $row['validado_cons_cups_vs_cups2_anticuerpo'] ?? null,
            'validado_cons_cups_vs_cups2_anticuerpo_resultado' => $row['validado_cons_cups_vs_cups2_anticuerpo_resultado'] ?? null,
            'validado_cons_tipo_present_proces_arc2' => $row['validado_cons_tipo_present_proces_arc2'] ?? null,
            'validado_cons_tipo_presentacion_toma' => $row['validado_cons_tipo_presentacion_toma'] ?? null,
            'validado_cons_tipo_presentacion_procesamiento' => $row['validado_cons_tipo_presentacion_procesamiento'] ?? null,
            'validado_bdua_codigo_eps_afiliacion_toma' => $row['validado_bdua_codigo_eps_afiliacion_toma'] ?? null,
            'aplica_diferencial_2' => $row['aplica_diferencial_2'] ?? null,

            ]);
    }
    
    private function procesarTipo2(array $row, $fileId)
    {
        return new GlossRegister([
            'id_register' => $row['id_archivo'],
            'consecutivo' => $row['consecutivo'] ?? null,
            'tipo_documento' => $row['tipo_documento'] ?? null,
            'numero_documento' => $row['numero_documento'] ?? null,
            
            'tipo_presentacion'  => $row['tipo_presentacion'] ?? null,
            'id_user' => $row['id_user'] ?? null,
            'fecha_registro' => $row['fecha_registro'] ?? null,
            'bdua_fecha_nacimiento' => $row['bdua_fecha_nacimiento'] ?? null,
            'validado_bdua_nombres_documento' => $row['validado_bdua_nombres_documento'] ?? null,
            'validado_bdua_renec' => $row['validado_bdua_renec'] ?? null,
            'validado_bdua_eps' => $row['validado_bdua_eps'] ?? null,
            'validado_bdua_renec_vigencia' => $row['validado_bdua_renec_vigencia'] ?? null,
            'validado_bdua_fecha' => $row['validado_bdua_fecha'] ?? null,
            'validado_bdua_ftoma_fdefuncion' => $row['validado_bdua_ftoma_fdefuncion'] ?? null,
            'validado_bdua_codmunicipio_afiliacion' => $row['validado_bdua_codmunicipio_afiliacion'] ?? null,
            'validado_bdua_codigo_eps_afiliacion_toma' => $row['validado_bdua_codigo_eps_afiliacion_toma'] ?? null,
            'validado_sismuestra_nodoc_tipdoc' => $row['validado_sismuestra_nodoc_tipdoc'] ?? null,
            'validado_sismuestra_fecha_toma' => $row['validado_sismuestra_fecha_toma'] ?? null,
            'validado_sismuestra_fecha_resultado' => $row['validado_sismuestra_fecha_resultado'] ?? null,
            'validado_sismuestra_fecha' => $row['validado_sismuestra_fecha'] ?? null,
            'validado_cons_cups_anticuerpo' => $row['validado_cons_cups_anticuerpo'] ?? null,
            'validado_cons_cups2_anticuerpo' => $row['validado_cons_cups2_anticuerpo'] ?? null,
            'validado_cons_cups_vs_cups2_anticuerpo' => $row['validado_cons_cups_vs_cups2_anticuerpo'] ?? null,
            'validado_cons_cups_vs_cups2_anticuerpo_resultado' => $row['validado_cons_cups_vs_cups2_anticuerpo_resultado'] ?? null,
            'validado_cons_codigo_habilitacion' => $row['validado_cons_codigo_habilitacion'] ?? null,
            'validado_cons_nit' => $row['validado_cons_nit'] ?? null,
            'validado_cons_nit_vs_codigo_habilitacion' => $row['validado_cons_nit_vs_codigo_habilitacion'] ?? null,
            'validado_cons_no_presentado_anterior_con_pago' => $row['validado_cons_no_presentado_anterior_con_pago'] ?? null,
            'validado_cons_duplicado_misma_ventana' => $row['validado_cons_duplicado_misma_ventana'] ?? null,
            'validado_cons_valor_registro' => $row['validado_cons_valor_registro'] ?? null,
            'validado_cons_id_muestra' => $row['validado_cons_id_muestra'] ?? null,
            'validado_cons_no_pagado_historico_por_id_muestra_concepto' => $row['validado_cons_no_pagado_historico_por_id_muestra_concepto'] ?? null,
            'validado_cons_no_pagado_historico_por_id_muestra_concepto_doc' => $row['validado_cons_no_pagado_historico_por_id_muestra_concepto_doc'] ?? null,
            'validado_cons_no_pagado_historico_por_doc_fecha_toma_cups_concepto' => $row['validado_cons_no_pagado_historico_por_doc_fecha_toma_cups_concepto'] ?? null

        ]);
    }     
}
