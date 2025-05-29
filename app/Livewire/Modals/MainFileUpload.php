<?php

namespace App\Livewire\Modals; 

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RegistersImport;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Storage;

class MainFileUpload extends Component
{
    use WithFileUploads;

    // Propiedades del formulario (de file-form.blade.php)
    #[Validate('required|file|mimes:xlsx,xls,csv,txt|max:102400')] 
    public $file;

    #[Validate('required|integer')]
    public $idCanasta;

    #[Validate('required|in:nuevo,anterior')]
    public $esquema;

    // #[Validate('required')] // No es necesario si se asigna por defecto
    public $estado = 1; // Asignar 'Cargado' por defecto

    #[Validate('required|date')]
    public $fechaCargue;

    #[Validate('required|integer|min:0')]
    public $registros;

    // Control del modal
    public bool $showModal = false;

    protected $listeners = ['showMainFileUpload' => 'openModal'];

    // Inicializar valores por defecto
    public function mount()
    {
        $this->fechaCargue = now()->format('Y-m-d');
        $this->estado = 1; // Asegura el estado inicial
    }

    // Abrir y resetear el modal
    public function openModal()
    {
        Log::info('Abriendo modal de carga de archivo principal...');
        $this->resetValidation();
        $this->resetExcept('showModal', 'fechaCargue', 'estado'); // Resetea todo excepto estos
        $this->fechaCargue = now()->format('Y-m-d'); // Asegura fecha actual al abrir
        $this->estado = 1; // Asegura estado 1 al abrir
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->resetExcept('showModal'); // Limpia todo al cerrar
    }
   
    public function updatedFile($file)
    {
        $this->resetValidation(['registros', 'esquema', 'file']); // Limpiar errores previos
        $this->registros = null;
        $this->esquema = null; // Resetear esquema detectado

        if ($file) {
            $filePath = $file->getRealPath();
            Log::info("Procesando archivo seleccionado: " . $file->getClientOriginalName());

            // --- 1. Contar Registros ---
            try {
                $sheets = Excel::toArray(new \stdClass(), $filePath);
                if (!empty($sheets[0])) {
                    $rowCount = count($sheets[0]) - 1;
                    $this->registros = max(0, $rowCount);
                    Log::info("Filas contadas: " . $this->registros);
                } else {
                    $this->registros = 0;
                    $this->addError('file', 'El archivo parece estar vacío.');
                    Log::warning("Archivo vacío: " . $file->getClientOriginalName());
                    return; // Detener si está vacío
                }
            } catch (\Exception $e) {
                 Log::error("Error contando filas: " . $e->getMessage());
                 $this->registros = null;
                 $this->addError('file', 'No se pudo leer el número de registros. Verifique el formato.');
                 return; // Detener si no se pueden contar filas
            }

            // --- 2. Detectar Esquema por Cabeceras ---
            try {
                 // Leer solo la primera fila (cabeceras)
                $headings = (new HeadingRowImport)->toArray($filePath)[0][0] ?? [];
                // Convertir a snake_case y minúsculas para comparación robusta
                $processedHeadings = collect($headings)
                                        ->map(fn($h) => Str::snake(strtolower(trim($h))))
                                        ->filter() // Quitar vacíos
                                        ->all();

                Log::debug('Cabeceras procesadas: ', $processedHeadings);

                // Definir columnas clave ÚNICAS para cada esquema (en snake_case)
                $tipo2UniqueHeaders = ['concepto_presentacion', 'no_factura', 'id_examen']; // Cabeceras que SOLO están en v2
                $tipo1UniqueHeaders = ['no_factura_muestra', 'no_factura_procesamiento', 'tipo_procedimiento', 'nit_ips_tomo_muestra', 'valor_prueba']; // Cabeceras que SOLO están en v1

                $foundTipo2 = false;
                foreach ($tipo2UniqueHeaders as $header) {
                    if (in_array($header, $processedHeadings)) {
                        $foundTipo2 = true;
                        break;
                    }
                }

                if ($foundTipo2) {
                    $this->esquema = 'nuevo';
                } else {
                    $foundTipo1 = false;
                    foreach ($tipo1UniqueHeaders as $header) {
                        if (in_array($header, $processedHeadings)) {
                            $foundTipo1 = true;
                            break;
                        }
                    }
                    if ($foundTipo1) {
                        $this->esquema = 'anterior';
                    }
                }

                // Validar detección
                if ($this->esquema) {
                     Log::info("Esquema detectado automáticamente: " . $this->esquema);
                     // Validar explícitamente el valor detectado (opcional pero bueno)
                     $this->validateOnly('esquema', ['esquema' => ['required', Rule::in(['nuevo', 'anterior'])]]);
                } else {
                     Log::warning("No se pudo detectar el esquema del archivo basado en cabeceras.", ['headers' => $processedHeadings]);
                     $this->addError('esquema', 'Esquema no detectado. Verifique las columnas del archivo.');
                }

            } catch (\Exception $e) {
                Log::error("Error detectando esquema: " . $e->getMessage());
                $this->addError('file', 'Error al leer las cabeceras del archivo para detectar el esquema.');
            }

        } else {
             $this->registros = null;
             $this->esquema = null;
        }
    }

    public function save()
    {
        // Validar todo ANTES de guardar, incluyendo el esquema detectado
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:102400',
            'idCanasta' => 'required|integer',
            'esquema' => ['required', Rule::in(['nuevo', 'anterior'])], // Validar el esquema detectado
            'fechaCargue' => 'required|date',
            'registros' => 'required|integer|min:0',
         ]);

        Log::info("Guardando archivo: ID Canasta {$this->idCanasta}, Esquema DETECTADO {$this->esquema}, Registros CONTADOS {$this->registros}");

        $fileRecord = null;
        DB::beginTransaction();

        try {
            $originalName = $this->file->getClientOriginalName();
            $path = $this->file->store('uploads', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // 1. Crear registro en 'files'
            $fileRecord = File::create([
                'idCanasta' => $this->idCanasta,
                'GUID' => (string) Str::uuid(),
                'tipoArchivo' => $originalName,
                'esquema' => $this->esquema, // Usar el esquema detectado
                'registros' => $this->registros, // Usar los registros contados
                'estado' => 1, // Cargado
                'fechaCargue' => $this->fechaCargue,
                'file' => $path,
            ]);

            Log::info("Registro 'File' creado con ID: " . $fileRecord->id . ". Iniciando importación...");

            // 2. Llamar al Importer
            Excel::import(new RegistersImport($fileRecord->esquema, $fileRecord->id), $fullPath);

            Log::info("Importación completada para File ID: " . $fileRecord->id);
            DB::commit();

            session()->flash('message', 'Archivo cargado y registros procesados con éxito.');
            $this->dispatch('mainFileUploaded');
            $this->closeModal();

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             DB::rollBack();
             // ... (Manejo error validación Excel) ...
             session()->flash('error', 'Error de validación en el archivo Excel.');
             if ($fileRecord) $fileRecord->update(['estado' => 4]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error general durante la carga/importación: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Error general al procesar el archivo: ' . $e->getMessage());
            if ($fileRecord) $fileRecord->update(['estado' => 4]);
        }
    }

    public function render()
    {
        return view('livewire.modals.main-file-upload');
    }

}
