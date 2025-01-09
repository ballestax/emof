<?php

namespace App\Livewire;

use App\Models\File;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FilesImport;
use App\Models\Register;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function Ramsey\Uuid\v1;

class FileForm extends Component
{
    use WithPagination, WithFileUploads;

    public $idCanasta;
    public $guid;
    public $esquema;
    public $registros;
    public $estado;
    public $fechaCargue;
    public $fileId;
    public $file;
    public $tipoArchivo;

    protected $rules = [
        'idCanasta' => 'required|unique:files,idCanasta',
        'esquema' => 'required',
        'registros' => 'required',
        'estado' => 'required',
        'fechaCargue' => 'required|date',
        'file' => 'required|file|mimes:xlsx,xls'
    ];

    public function storeFile()
    {
        $this->validate();
        Log::info('Validated successfully');

        DB::beginTransaction();
        try {
            $path = $this->file->store('files'); // Guardamos el archivo
            $file = File::create([
                'idCanasta' => $this->idCanasta,
                'esquema' => $this->esquema,
                'registros' => $this->registros,
                'estado' => $this->estado,
                'fechaCargue' => $this->fechaCargue,
                'GUID' => uniqid(),
                'tipoArchivo' => 1, // Puedes cambiar el valor de tipoArchivo según el caso
                'file' => $path, // Guardamos la ruta del archivo en el campo "file"
            ]);

            $fileId = $file->id;

            Log::info('Archivo guardado con ID: ' . $fileId);

            // Llamamos a la función para importar el archivo
            $this->importFile($fileId);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            // Logueamos el error para más detalles
            Log::error('Error al guardar el archivo o los registros: ' . $e->getMessage());

            // Opcionalmente, puedes lanzar una excepción para manejar el error en el frontend
            throw $e;
        }

        $this->reset();
    }

    public function importFile($fileId)
    {
        Log::info('Iniciando importación del archivo');
        Log::info('Archivo a importar con ID: ' . $fileId);

        // Guardar el archivo en el almacenamiento
        $path = $this->file->store('files'); // Guardamos el archivo

        // Cargar el archivo y verificar el tipo
        $data = Excel::toArray(new \stdClass, $path); // Cargar el archivo completo en un array

        // Suponemos que la primera fila contiene los encabezados
        $headers = $data[0][0]; // Primera fila con los encabezados
        $columnCount = count($headers);

        // Verificación de tipo de archivo
        if ($this->isTipo1($headers, $columnCount)) {
            $this->tipoArchivo = 'tipo_1'; // Establecer el tipo de archivo como 'tipo_1'
        } elseif ($this->isTipo2($headers, $columnCount)) {
            $this->tipoArchivo = 'tipo_2'; // Establecer el tipo de archivo como 'tipo_2'
        } else {
            Log::error('Archivo con formato desconocido o inválido');
            Storage::delete($path); // Eliminar el archivo después de la verificación
            return; // No continuar si el formato no es reconocido
        }

        // Obtener la cantidad de filas para mostrar los registros importados
        $rowCount = count($data[0]); // Obtener el número total de filas
        $this->registros = $rowCount - 1; // Restar 1 para omitir la fila del encabezado
        Log::info('Número de registros a importar: ' . $this->registros);

        // Empezamos a procesar desde la segunda fila (omitiendo el encabezado)
        $rows = array_slice($data[0], 1); // Omitir la primera fila (encabezado)

        // Si el archivo es de tipo 1, insertamos en la tabla "registers" según su estructura
        if ($this->tipoArchivo === 'tipo_1') {
            foreach ($rows as $row) {
                Register::create([
                    'idFile' => $fileId,
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
            }
        }

        // Si el archivo es de tipo 2, insertamos en la tabla "registers" según su estructura
        if ($this->tipoArchivo === 'tipo_2') {
            foreach ($rows as $row) {
                Register::create([
                    'idFile' => $fileId,
                    'consecutivo' => $row[0] ?? null,
                    'tipo_documento' => $row[1] ?? null,
                    'numero_documento' => $row[2] ?? null,
                    'primer_nombre' => $row[3] ?? null,
                    'segundo_nombre' => $row[4] ?? null,
                    'primer_apellido' => $row[5] ?? null,
                    'segundo_apellido' => $row[6] ?? null,
                    'codigo_cups' => $row[7] ?? null,
                    'registro_sanitario_prueba' => $row[8] ?? null,
                    'codigo_eps' => $row[9] ?? null,
                    'nombre_eps' => $row[10] ?? null,
                    'compra_masiva' => $row[11] ?? null,
                    'valor_prueba' => $row[12] ?? null,
                    'nit_ips_tomo_muestra' => $row[13] ?? null,
                    'nombre_ips_tomo_muestra' => $row[14] ?? null,
                    'codigo_habilitacion_ips_tomo_muestra' => $row[15] ?? null,
                    'valor_toma_muestra_a_cobrar_adres' => $row[16] ?? null,
                    'no_factura_muestra' => $row[17] ?? null,
                    'nit_laboratorio_procesamiento' => $row[18] ?? null,
                    'nombre_laboratorio_procesamiento' => $row[19] ?? null,
                    'codigo_habilitacion_procesamiento' => $row[20] ?? null,
                    'valor_procesamiento_a_cobrar_adres' => $row[21] ?? null,
                    'no_factura_procesamiento' => $row[22] ?? null,
                    'fecha_toma' => $row[23] ?? null,
                    'resultado_prueba' => $row[24] ?? null,
                    'fecha_resultado' => $row[25] ?? null,
                    'tipo_procedimiento' => $row[26] ?? null,
                    'tipo_archivo' => 'tipo_2',  // 'tipo_1' está fijo
                ]);
            }
        }

        // Eliminar el archivo después de la importación
        Storage::delete($path);

        Log::info('Importación completada y registros asociados al archivo');
    }

    public function updatedFile(){
        $path = $this->file;
        $data = Excel::toArray(new \stdClass, $path);

        $rowCount = count($data[0]);
        $this->registros = $rowCount - 1;
        Log::info('Número de registros a importar: ' . $this->registros);
        return $rowCount;
    }

    private function isTipo1($headers, $columnCount)
    {
        // Verificar si el archivo corresponde al tipo 1 según los encabezados y el número de columnas
        $expectedHeadersTipo1 = [
            'CONSECUTIVO',
            'TIPO_DOCUMENTO',
            'NUMERO_DOCUMENTO',
            'PRIMER_NOMBRE',
            'SEGUNDO_NOMBRE',
            'PRIMER_APELLIDO',
            'SEGUNDO_APELLIDO',
            'CODIGO_CUPS',
            'CODIGO_CUPS2_ANTICUERPOS',
            'REGISTRO_SANITARIO_PRUEBA',
            'CODIGO_EPS',
            'NOMBRE_EPS',
            'CONCEPTO_PRESENTACION',
            'NIT',
            'NOMBRE',
            'CODIGO_HABILITACION',
            'VALOR',
            'NO_FACTURA',
            'FECHA_TOMA',
            'FECHA_RESULTADO',
            'RESULTADO_PRUEBA',
            'ID_EXAMEN'
        ];

        return $columnCount == count($expectedHeadersTipo1) && $headers == $expectedHeadersTipo1;
    }

    private function isTipo2($headers, $columnCount)
    {
        // Verificar si el archivo corresponde al tipo 2 según los encabezados y el número de columnas
        $expectedHeadersTipo2 = [
            'CONSECUTIVO',
            'TIPO_DOCUMENTO',
            'NUMERO_DOCUMENTO',
            'PRIMER_NOMBRE',
            'SEGUNDO_NOMBRE',
            'PRIMER_APELLIDO',
            'SEGUNDO_APELLIDO',
            'CODIGO_CUPS',
            'REGISTRO_SANITARIO_PRUEBA',
            'CODIGO_EPS',
            'NOMBRE_EPS',
            'COMPRA_MASIVA',
            'VALOR_PRUEBA',
            'NIT_IPS_TOMO_MUESTRA',
            'NOMBRE_IPS_TOMO_MUESTRA',
            'CODIGO_HABILITACION_IPS_TOMO_MUESTRA',
            'VALOR_TOMA_MUESTRA_A_COBRAR_ADRES',
            'NO_FACTURA_MUESTRA',
            'NIT_LABORATORIO_PROCESAMIENTO',
            'NOMBRE_LABORATORIO_PROCESAMIENTO',
            'CODIGO_HABILITACION_PROCESAMIENTO',
            'VALOR_PROCESAMIENTO_A_COBRAR_ADRES',
            'NO_FACTURA_PROCESAMIENTO',
            'FECHA_TOMA',
            'RESULTADO_PRUEBA',
            'FECHA_RESULTADO',
            'TIPO_PROCEDIMIENTO'

        ];

        return $columnCount == count($expectedHeadersTipo2) && $headers == $expectedHeadersTipo2;
    }

    public function edit($id)
    {
        $file = File::find($id);
        $this->fileId = $file->id;
        $this->idCanasta = $file->idCanasta;
        $this->guid = $file->GUID;
        $this->esquema = $file->esquema;
        $this->registros = $file->registros;
        $this->estado = $file->estado;
        $this->fechaCargue = $file->fechaCargue;
    }

    public function update()
    {
        $this->validate();
        File::updateOrCreate(
            ['id' => $this->file_id],
            [
                'idCanasta' => $this->idCanasta,
                'GUID' => $this->guid,
                'esquema' => $this->esquema,
                'registros' => $this->registros,
                'estado' => $this->estado,
                'fechaCargue' => $this->fechaCargue,
            ]
        );

        $this->reset();
    }

    public function destroy($id)
    {
        File::destroy($id);
    }

    public function render():Renderable
    {
        return view('livewire.file-form', ['files' => File::latest()->paginate(10)]);
    }
  
}
