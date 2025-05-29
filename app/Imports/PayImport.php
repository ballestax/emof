<?php

namespace App\Imports;

use App\Models\PayRegister;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PayImport implements ToModel, WithHeadingRow
{
    protected $tipoArchivo;
    protected $fileId;

    protected $table = 'pay_registers';

    public function __construct($tipoArchivo, $fileId)
    {
        $this->tipoArchivo = $tipoArchivo;
        $this->fileId = $fileId;
    }

    public function model(array $row)
    {   
        Log::Debug('Tipo Archivo: '. $this->tipoArchivo);
        if ($this->tipoArchivo == 'tipo_1') {
            return $this->procesarTipo1($row, $this->fileId);
        } elseif ($this->tipoArchivo == 'tipo_2') {
            return $this->procesarTipo2($row, $this->fileId);
        } else {
            Log::warning("Tipo de archivo no reconocido: " . $this->tipoArchivo);
            return null;
        }
    }

    private function procesarTipo1(array $row,$fileId)
    {
        return new PayRegister([
            // 'id' => $this->fileId,
            // 'id_archivo_detalle' => $row[0] ?? null,
            'id_register' => $row['id_archivo'],
            'consecutivo' => $row['consecutivo'] ?? null,
            'tipo_documento' => $row['tipo_documento'] ?? null,
            'numero_documento' => $row['numero_documento'] ?? null,
            'fecha_registro' => $row['fecha_registro'] ?? null,
            'fecha_pago' => $row['fecha_pago'] ?? null,
            'id_proceso' => $row['id_proceso'] ?? null
            ]);
    }
    private function procesarTipo2(array $row, $fileId)
    {
        return new PayRegister([
            //'id' => $this->fileId,
            //'id_archivo_detalle' => $row[0] ?? null,
            'id_register' => $row['id_archivo'],
            //'id_user' => $row[25] ?? null,
            //'fecha_registro' => $row[30] ?? null,
            'consecutivo' => $row['consecutivo'] ?? null,
            'tipo_documento' => $row['tipo_documento'] ?? null,
            'numero_documento' => $row['numero_documento'] ?? null,
            'fecha_registro' => $row['fecha_registro'] ?? null,
            'fecha_pago' => $row['fecha_pago'] ?? null,
            'id_proceso' => $row['id_proceso'] ?? null
        ]);
    }     
}
