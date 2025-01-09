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
            $table->integer('id_register');
            $table->date('fecha_registro');
            $table->integer('validado_bdua_nombres_documento')->nullable();
            $table->integer('validado_bdua_eps')->nullable();
            $table->integer('validado_bdua_renec')->nullable();
            $table->integer('validado_bdua_renec_vigencia')->nullable();
            $table->integer('validado_bdua_ftoma_fdefuncion')->nullable();
            $table->integer('validado_sismuestra_nodoc_tipdoc')->nullable();
            $table->integer('validado_sismuestra_fecha_toma')->nullable();
            $table->integer('validado_sismuestra_fecha_resultado')->nullable();
            $table->integer('validado_cons_fecha_toma')->nullable();
            $table->integer('validado_cons_fecha_toma_vs_fecha_resultado')->nullable();
            $table->integer('validado_cons_codigo_habilitacion_toma_pros')->nullable();
            $table->integer('validado_cons_nit_ips_procesamiento')->nullable();
            $table->integer('validado_cons_compra_prueba')->nullable();
            $table->integer('validado_cons_prueba_et')->nullable();
            $table->date('validado_cons_fecha')->nullable();
            $table->integer('validado_cons_no_presentado_anterior_con_pago')->nullable();
            $table->integer('validado_cons_duplicado')->nullable();
            $table->integer('aplica_diferencial')->nullable();
            $table->integer('validado_cons_nit_toma_vs_codigohab_toma')->nullable();
            $table->integer('validado_cons_nit_proc_vs_codigohab_proc')->nullable();
            $table->integer('validado_cons_valor_vs_tipo_procedimiento')->nullable();
            $table->integer('validado_cons_id_muestra')->nullable();
            $table->integer('validado_cons_pt5_solo_procesamiento')->nullable();
            $table->integer('validado_cons_pt5_solo_toma')->nullable();
            $table->integer('validado_cons_pt5_solo_toma_pros_pagado')->nullable();
            $table->integer('validado_cons_pt5_solo_toma_pros_pagado_doc')->nullable();
            $table->integer('validado_cons_pt5_codigo_habilitacion_toma_pros')->nullable();
            $table->integer('validado_cons_pt5_nit_proc_vs_codigohab_proc')->nullable();
            $table->string('tipo_presentacion')->nullable();
            $table->integer('id_user')->nullable();
            $table->date('bdua_fecha_nacimiento')->nullable();
            $table->integer('validado_bdua_codmunicipio_afiliacion')->nullable();
            $table->integer('validado_bdua_codigo_eps_afiliacion_toma')->nullable();
            $table->date('validado_sismuestra_fecha')->nullable();
            $table->integer('validado_cons_cups_anticuerpo')->nullable();
            $table->integer('validado_cons_cups2_anticuerpo')->nullable();
            $table->integer('validado_cons_cups_vs_cups2_anticuerpo')->nullable();
            $table->integer('validado_cons_cups_vs_cups2_anticuerpo_resultado')->nullable();
            $table->integer('validado_cons_codigo_habilitacion')->nullable();
            $table->integer('validado_cons_nit')->nullable();
            $table->integer('validado_cons_nit_vs_codigohabilitacion')->nullable();
            $table->integer('validado_cons_duplicado_misma_ventana')->nullable();
            $table->integer('validado_cons_valor_registro')->nullable();
            $table->integer('validado_cons_no_pagado_historico_por_id_muestra_concepto')->nullable();
            $table->integer('validado_cons_no_pagado_historico_por_id_muestra_concepto_doc')->nullable();
            $table->integer('validado_cons_no_pagado_historico_por_doc_fecha_toma_cups_concepto')->nullable();
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
