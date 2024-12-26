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
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->integer('idFile')->nullable();
            $table->integer('consecutivo')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('primer_nombre')->nullable();
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('codigo_cups')->nullable();
            $table->string('codigo_cups2_anticuerpos')->nullable();
            $table->string('registro_sanitario_prueba')->nullable();
            $table->string('codigo_eps')->nullable();
            $table->string('nombre_eps')->nullable();
            $table->string('conmpra_masiva')->nullable();
            $table->string('valor_prueba')->nullable();
            $table->string('nit_ips_tomo_muestra')->nullable();
            $table->string('nombre_ips_tomo_muestra')->nullable();
            $table->string('codigo_habilitacion_ips_tomo_muestra')->nullable();
            $table->string('valor_toma_muestra_a_cobrar_adres')->nullable();
            $table->string('no_factura_muestra')->nullable();
            $table->string('nit_laboratorio_procesamiento')->nullable();
            $table->string('nombre_laboratorio_procesamiento')->nullable();
            $table->string('codigo_habilitacion_procesamiento')->nullable();
            $table->string('valor_procesamiento_a_cobrar_adres')->nullable();
            $table->string('no_factura_procesamiento')->nullable();
            $table->string('fecha_toma')->nullable();
            $table->string('resultado_prueba')->nullable();
            $table->string('fecha_resultado')->nullable();
            $table->string('tipo_procedimiento')->nullable();
            $table->string('concepto_presentacion')->nullable();
            $table->string('id_examen')->nullable();
            $table->string('tipo_archivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registers');
    }
};
