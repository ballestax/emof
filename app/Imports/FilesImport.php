<?php

namespace App\Imports;

use App\Models\File;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Register;
use Illuminate\Support\Facades\Log;

class FilesImport implements ToModel
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
        if ($this->tipoArchivo == 'tipo_1') {
            // Procesar datos para el tipo 1
            return new Register([
                'idFile' => $this->fileId,
                'consecutivo' => $row[0] ?? null,
                'tipo_documento' => $row[1] ?? null,
                'numero_documento' => $row[2] ?? null,
                'primer_nombre' => $row[3] ?? null,
                'segundo_nombre' => $row[4] ?? null,
                'primer_apellido' => $row[5] ?? null,
                'segundo_apellido' => $row[6] ?? null,
                'codigo_cups' => $row[7] ?? null,
                'codigo_cups2_anticuerpos' => $row[8] ?? null,
                'registro_sanitario_prueba' => $row[9] ?? null,
                'codigo_eps' => $row[10] ?? null,
                'nombre_eps' => $row[11] ?? null,
                'concepto_presentacion' => $row[12] ?? null,
                'nit_ips_tomo_muestra' => $row[13] ?? null,
                'nombre_ips_tomo_muestra' => $row[14] ?? null,
                'codigo_habilitacion_ips_tomo_muestra' => $row[15] ?? null,
                'valor_toma_muestra_a_cobrar_adres' => $row[16] ?? null,
                'no_factura_muestra' => $row[17] ?? null,
                'fecha_toma' => $row[18] ?? null,
                'fecha_resultado' => $row[19] ?? null,
                'resultado_prueba' => $row[20] ?? null,
                'id_examen' => $row[21] ?? null,
                'tipo_archivo' => 'tipo_1',  // 'tipo_1' está fijo
            ]);
        } elseif ($this->tipoArchivo == 'tipo_2') {
            // Procesar datos para el tipo 2
            return new Register([
                'idFile' => $this->fileId,
                'consecutivo' => $row[0],  // $row[0] -> consecutivo
                'tipo_documento' => $row[1],  // $row[1] -> tipo_documento
                'numero_documento' => $row[2],  // $row[2] -> numero_documento
                'primer_nombre' => $row[3],  // $row[3] -> primer_nombre
                'segundo_nombre' => $row[4],  // $row[4] -> segundo_nombre
                'primer_apellido' => $row[5],  // $row[5] -> primer_apellido
                'segundo_apellido' => $row[6],  // $row[6] -> segundo_apellido
                'codigo_cups' => $row[7],  // $row[7] -> codigo_cups
                'registro_sanitario_prueba' => $row[8],  // $row[8] -> registro_sanitario_prueba
                'codigo_eps' => $row[9],  // $row[9] -> codigo_eps
                'nombre_eps' => $row[10],  // $row[10] -> nombre_eps
                'compra_masiva' => $row[11],  // $row[11] -> compra_masiva
                'valor_prueba' => $row[12],  // $row[12] -> valor_prueba
                'nit_ips_tomo_muestra' => $row[13],  // $row[13] -> nit_ips_tomo_muestra
                'nombre_ips_tomo_muestra' => $row[14],  // $row[14] -> nombre_ips_tomo_muestra
                'codigo_habilitacion_ips_tomo_muestra' => $row[15],  // $row[15] -> codigo_habilitacion_ips_tomo_muestra
                'valor_toma_muestra_a_cobrar_adres' => $row[16],  // $row[16] -> valor_toma_muestra_a_cobrar_adres
                'no_factura_muestra' => $row[17],  // $row[17] -> no_factura_muestra
                'nit_laboratorio_procesamiento' => $row[18],  // $row[18] -> nit_laboratorio_procesamiento
                'nombre_laboratorio_procesamiento' => $row[19],  // $row[19] -> nombre_laboratorio_procesamiento
                'codigo_habilitacion_procesamiento' => $row[20],  // $row[20] -> codigo_habilitacion_procesamiento
                'valor_procesamiento_a_cobrar_adres' => $row[21],  // $row[21] -> valor_procesamiento_a_cobrar_adres
                'no_factura_procesamiento' => $row[22],  // $row[22] -> no_factura_procesamiento
                'fecha_toma' => $row[23],  // $row[23] -> fecha_toma
                'resultado_prueba' => $row[24],  // $row[24] -> resultado_prueba
                'fecha_resultado' => $row[25],  // $row[25] -> fecha_resultado
                'tipo_archivo' => 'tipo_2',  // 'tipo_2' está fijo
            ]);
        }
        // Si el tipo de archivo no coincide, puedes manejarlo de otra forma o lanzar un error.
    }
}
