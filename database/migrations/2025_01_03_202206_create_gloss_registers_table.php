<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gloss_registers', function (Blueprint $table) {
            $table->id();
            $table->integer('id_register')->nullable();
            $table->integer('consecutivo')->nullable();
            $table->integer('tipo_presentacion')->nullable();
            $table->string('id_user')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->timestamp('fecha_registro')->nullable();
            $table->date('bdua_fecha_nacimiento')->nullable();
            $table->integer('validado_bdua_nombres_documento')->nullable();
            $table->integer('validado_bdua_renec')->nullable();
            $table->integer('validado_bdua_eps')->nullable();
            $table->integer('validado_bdua_renec_vigencia')->nullable();
            $table->date('validado_bdua_fecha')->nullable();
            $table->integer('validado_bdua_ftoma_fdefuncion')->nullable();
            $table->integer('validado_bdua_codmunicipio_afiliacion')->nullable();
            $table->integer('validado_bdua_codigo_eps_afiliacion_toma')->nullable();
            $table->integer('validado_sismuestra_nodoc_tipdoc')->nullable();
            $table->integer('validado_sismuestra_fecha_toma')->nullable();
            $table->integer('validado_sismuestra_fecha_resultado')->nullable();
            $table->integer('validado_sismuestra_fecha')->nullable();
            $table->integer('validado_cons_cups_anticuerpo')->nullable();
            $table->integer('validado_cons_cups2_anticuerpo')->nullable();
            $table->integer('validado_cons_cups_vs_cups2_anticuerpo')->nullable();
            $table->integer('validado_cons_cups_vs_cups2_anticuerpo_resultado')->nullable();
            $table->integer('validado_cons_codigo_habilitacion')->nullable();
            $table->integer('validado_cons_nit')->nullable();
            $table->integer('validado_cons_nit_vs_codigo_habilitacion')->nullable();
            $table->integer('validado_cons_no_presentado_anterior_con_pago')->nullable();
            $table->integer('validado_cons_duplicado_misma_ventana')->nullable();
            $table->integer('validado_cons_valor_registro')->nullable();
            $table->integer('validado_cons_id_muestra')->nullable();
            $table->integer('validado_cons_no_pagado_historico_por_id_muestra_concepto')->nullable();
            $table->integer('validado_cons_no_pagado_historico_por_id_muestra_concepto_doc')->nullable();
            $table->integer('validado_cons_no_pagado_historico_por_doc_fecha_toma_cups_concepto')->nullable();
            $table->integer('validado_cons_fecha_toma')->nullable();
            $table->integer('validado_cons_fecha_toma_vs_fecha_resultado')->nullable();
            $table->integer('validado_cons_codigo_habilitacion_toma_pros')->nullable();
            $table->integer('validado_cons_nit_ips_procesamiento')->nullable();
            $table->integer('validado_cons_compra_prueba')->nullable();
            $table->integer('validado_cons_prueba_et')->nullable();
            $table->date('validado_cons_fecha')->nullable();
            $table->integer('validado_cons_supera_valor')->nullable();
            $table->integer('validado_cons_nit_toma_vs_nit_proceso')->nullable();
            $table->integer('validado_cons_duplicado')->nullable();
            $table->integer('validado_cons_nit_toma_vs_codigohab_toma')->nullable();
            $table->integer('validado_cons_nit_proc_vs_codigohab_proc')->nullable();
            $table->integer('validado_cons_valor_vs_tipo_procedimiento')->nullable();
            $table->integer('validado_cons_pt5_solo_procesamiento')->nullable();
            $table->integer('validado_cons_pt5_solo_toma')->nullable();
            $table->integer('validado_cons_pt5_solo_toma_pros_pagado')->nullable();
            $table->integer('validado_cons_pt5_solo_toma_pros_pagado_doc')->nullable();
            $table->integer('validado_cons_pt5_codigo_habilitacion_toma_pros')->nullable();
            $table->integer('validado_cons_pt5_nit_proc_vs_codigohab_proc')->nullable();
            $table->integer('validado_cons_pt6_solo_toma')->nullable();
            $table->integer('validado_cons_pt6_solo_procesamiento')->nullable();
            $table->integer('validado_cons_pt6_solo_procesamiento_toma_pagado')->nullable();
            $table->integer('validado_cons_pt6_solo_procesamiento_toma_pagado_doc')->nullable();
            $table->integer('validado_cons_pt6_nit_toma_vs_codigohab_toma')->nullable();
            $table->integer('validado_cons_pt6_codigo_habilitacion_toma_pros')->nullable();
            $table->integer('validado_cons_tipo_present_proces_arc2')->nullable();
            $table->integer('validado_cons_tipo_presentacion_toma')->nullable();
            $table->integer('validado_cons_tipo_presentacion_procesamiento')->nullable();
            $table->integer('aplica_diferencial_2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gloss_registers');
    }
};
