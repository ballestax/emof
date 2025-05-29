<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts; // Para insertar en lotes
use Carbon\Carbon;

class RegistersImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    private string $esquema;
    private int $idFile;
    private string $tableName;

    public function __construct(string $esquema, int $idFile)
    {
        $this->esquema = $esquema;
        $this->idFile = $idFile;
        // Determina la tabla destino basado en el esquema
        $this->tableName = ($this->esquema === 'nuevo') ? 'registers_v2' : 'registers_v1';
        Log::info("Importer inicializado para idFile: {$this->idFile}, esquema: {$this->esquema}, tabla: {$this->tableName}");
    }

    /**
     * Procesa la colección de filas leídas del archivo por chunks.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $insertData = [];
        Log::info("Procesando chunk de " . $rows->count() . " filas para tabla {$this->tableName}");

        // Define ALL columns for the target table beforehand
        $v1Columns = ['idFile', 'consecutivo', /* ... all v1 cols ... */ 'created_at', 'updated_at'];
        $v2Columns = ['idFile', 'consecutivo', /* ... all v2 cols including cups2 ... */ 'created_at', 'updated_at'];
        $targetColumns = ($this->tableName === 'registers_v2') ? $v2Columns : $v1Columns;

        foreach ($rows as $row) {
            $rowData = collect($row)->map(fn ($value) => trim($value ?? ''))->all(); // Trim values

            // Initialize data array with nulls for all target columns
            $finalData = array_fill_keys($targetColumns, null);

            // Populate common fields
            $finalData['idFile'] = $this->idFile;
            $finalData['consecutivo'] = $rowData['consecutivo'] ?? null;
            $finalData['tipo_documento'] = $rowData['tipo_documento'] ?? null;
            $finalData['numero_documento'] = $rowData['numero_documento'] ?? null;
            $finalData['primer_nombre'] = $rowData['primer_nombre'] ?? null;
            $finalData['segundo_nombre'] = $rowData['segundo_nombre'] ?? null;
            $finalData['primer_apellido'] = $rowData['primer_apellido'] ?? null;
            $finalData['segundo_apellido'] = $rowData['segundo_apellido'] ?? null;
            $finalData['codigo_cups'] = $rowData['codigo_cups'] ?? null;
            $finalData['codigo_cups2_anticuerpos'] = $rowData['codigo_cups2_anticuerpos'] ?? null;
            $finalData['registro_sanitario_prueba'] = $rowData['registro_sanitario_prueba'] ?? null;
            $finalData['codigo_eps'] = $rowData['codigo_eps'] ?? null;
            $finalData['nombre_eps'] = $rowData['nombre_eps'] ?? null;
            $finalData['fecha_toma'] = isset($rowData['fecha_toma']) ? $this->transformDate($rowData['fecha_toma']) : null;
            $finalData['fecha_resultado'] = isset($rowData['fecha_resultado']) ? $this->transformDate($rowData['fecha_resultado']) : null;
            $finalData['resultado_prueba'] = $rowData['resultado_prueba'] ?? null;
            if ($this->tableName === 'registers_v2') {  //Esquema nuevo            
                $finalData['concepto_presentacion'] = $rowData['concepto_presentacion'] ?? null;
                $finalData['nit'] = $rowData['nit'] ?? null;
                $finalData['nombre'] = $rowData['nombre'] ?? null;
                $finalData['valor'] = $rowData['valor'] ?? null;
                $finalData['codigo_habilitacion'] = $rowData['codigo_habilitacion'] ?? null;
                $finalData['no_factura'] = $rowData['no_factura'] ?? null;
                $finalData['id_examen'] = $rowData['id_examen'] ?? null;
            } elseif ($this->tableName === 'registers_v1') {    //Esquema anterior
                $finalData['compra_masiva'] = $rowData['compra_masiva'] ?? null;
                $finalData['valor_prueba'] = $rowData['valor_prueba'] ?? null;
                $finalData['nit_ips_tomo_muestra'] = $rowData['nit_ips_tomo_muestra'] ?? null;
                $finalData['nombre_ips_tomo_muestra'] = $rowData['nombre_ips_tomo_muestra'] ?? null;
                $finalData['codigo_habilitacion_ips_tomo_muestra'] = $rowData['codigo_habilitacion_ips_tomo_muestra'] ?? null;
                $finalData['valor_toma_muestra_a_cobrar_adres'] = $rowData['valor_toma_muestra_a_cobrar_adres'] ?? null;
                $finalData['no_factura_muestra'] = $rowData['no_factura_muestra'] ?? null;
                $finalData['nit_laboratorio_procesamiento'] = $rowData['nit_laboratorio_procesamiento'] ?? null;
                $finalData['nombre_laboratorio_procesamiento'] = $rowData['nombre_laboratorio_procesamiento'] ?? null;
                $finalData['codigo_habilitacion_procesamiento'] = $rowData['codigo_habilitacion_procesamiento'] ?? null;
                $finalData['valor_procesamiento_a_cobrar_adres'] = $rowData['valor_procesamiento_a_cobrar_adres'] ?? null;
                $finalData['no_factura_procesamiento'] = $rowData['no_factura_procesamiento'] ?? null;
                $finalData['tipo_procedimiento'] = $rowData['tipo_procedimiento'] ?? null;
            }

            // Add timestamps
            $finalData['created_at'] = now();
            $finalData['updated_at'] = now();

            // Add to batch if we have essential data (e.g., consecutivo)
            if (!empty($finalData['consecutivo'])) {
                $insertData[] = $finalData;
            } else {
                Log::warning("Fila omitida por falta de consecutivo.", ['rowData' => $rowData]);
            }
        }

        // Insert batch
        if (!empty($insertData)) {
            try {
                
                DB::table($this->tableName)->insert($insertData);

                Log::info("Chunk insertado en {$this->tableName}. Filas: " . count($insertData));
            } catch (\Exception $e) {
                Log::error("Error insertando chunk en {$this->tableName}: " . $e->getMessage(), ['sql' => $e instanceof \Illuminate\Database\QueryException ? $e->getSql() : 'N/A', 'bindings' => $e instanceof \Illuminate\Database\QueryException ? $e->getBindings() : 'N/A']);
                // throw $e;
            }
        }
    }

    // Tamaño del chunk (procesa N filas a la vez)
    public function chunkSize(): int
    {
        return 500; // Ajusta este número según el rendimiento/memoria
    }

     // Tamaño del lote para inserciones (inserta N filas en una sola query)
     public function batchSize(): int
    {
        return 500; // Ajusta este número
    }

    protected function transformDate($value, $format = 'd/m/Y')
    {
        // Limpiar espacios y verificar si está vacío
        $cleanedValue = trim($value ?? '');
        if (empty($cleanedValue)) {
            return null;
        }

        // 1. Intentar formato DD/MM/YYYY (el que parece dar problemas)
        try {
            // createFromFormat es estricto con el formato
            return Carbon::createFromFormat('d/m/Y', $cleanedValue)->format($format);
        } catch (\Exception $e1) {
            Log::debug("Valor '{$cleanedValue}' no coincide con d/m/Y. Intentando otros formatos...");
        }

        // 2. Intentar otros formatos comunes que Carbon::parse pueda manejar (Y-m-d, M/D/Y, etc.)
        try {
            // Carbon::parse es más flexible
            return Carbon::parse($cleanedValue)->format($format);
        } catch (\Exception $e2) {
            Log::warning("No se pudo parsear la fecha: {$cleanedValue}. Error: " . $e2->getMessage());
        }

        // 3. (Opcional) Intentar conversión de número de serie de Excel si es numérico
        // if (is_numeric($cleanedValue)) {
        //     try {
        //         // Requiere phpoffice/phpspreadsheet
        //         return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cleanedValue)->format($format);
        //     } catch (\Exception $e3) {
        //         Log::warning("Conversión de fecha numérica de Excel falló para: {$cleanedValue}. Error: " . $e3->getMessage());
        //     }
        // }

        // Si todos los intentos fallan, retornar null
        return null;
    }
}