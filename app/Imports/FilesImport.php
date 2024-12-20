<?php

namespace App\Imports;

use App\Models\File;
use Maatwebsite\Excel\Concerns\ToModel;

class FilesImport implements ToModel
{
    public function model(array $row)
    {
        return new File([
            'consecutivo' => $row[0],
            'tipo_documento' => $row[1],
            'numero_documento' => $row[2],
            'primer_nombre' => $row[3],
            'segundo_nombre' => $row[4],
            'primer_apellido' => $row[5],
            'segundo_apellido' => $row[6],
            'codigo_cups' => $row[7],
            'codigo_cups2_anticuerpos' => $row[8],
            'registro_sanitario_prueba' => $row[9],
            'codigo_eps' => $row[10],
            'nombre_eps' => $row[11],
            'concepto_presentacion' => $row[12],
            'nit' => $row[13],
            'nombre' => $row[14],
            'codigo_habilitacion' => $row[15],
            'valor' => $row[16],
            'no_factura' => $row[17],
            'fecha_toma' => $row[18],
            'fecha_resultado' => $row[19],
            'resultado_prueba' => $row[20],
            'id_examen' => $row[21],
        ]);
    }
}