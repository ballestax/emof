<?php

namespace App\Livewire\Modals;

use App\Imports\GlossImport;
use App\Imports\PayImport;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FileUpload extends Component
{
    use WithFileUploads;

    public $id;
    public $title;
    public $actionMethod;
    public $tipoArchivo;
    public $fileId;

    public $file1;

    public $open = false;

    // Propiedades para exponer mensajes flash a Alpine
    public $sessionMessage = '';
    public $sessionError = '';

    protected $listeners = ['openFileUploadModal' => 'openModal'];

    public function dehydrate() // Se ejecuta antes de enviar el estado al frontend
    {
        // Leer mensajes flash y ponerlos en propiedades públicas
        $this->sessionMessage = session('message');
        $this->sessionError = session('error');
        // Opcional: Limpiar flash aquí si no se necesita más
        // session()->forget(['message', 'error']);
    }


    public function updatedFile1()
    {
        // Resetear validación específica al actualizar el archivo
        $this->resetValidation('file1');
        $this->validate([
            'file1' => 'required|file|mimes:csv,xlsx,txt|max:10240', // Ajusta el tamaño máximo si es necesario
        ]);
    }

    public function openModal($id, $title, $fileId, $actionMethod = null, $tipoArchivo = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->actionMethod = $actionMethod;
        $this->tipoArchivo = $tipoArchivo; // Asegúrate que este valor se pasa correctamente desde detail.blade.php
        $this->fileId = $fileId;

        // Resetear estado al abrir
        $this->reset('file1');      // Resetea la propiedad del archivo en Livewire
        $this->clearMyValidation(); // Llama al método renombrado para limpiar errores anteriores
        $this->sessionMessage = ''; // Resetea mensajes
        $this->sessionError = '';   // Resetea errores

        $this->open = true;         // Abre el modal

        logger()->info('Modal opened & state reset. ID:'.$this->id.' FileID:'.$this->fileId.' Type:'.$this->tipoArchivo);
    }

    // --- MÉTODO PÚBLICO RENOMBRADO ---
    // Este método es seguro llamarlo desde Alpine porque es público y no colisiona
    public function clearMyValidation()
    {
        $this->resetValidation('file1'); // Llama al método protegido/trait de Livewire
        logger()->info('Ejecutando clearMyValidation para file1 en componente ID: '.$this->id);
    }
    // --- FIN MÉTODO RENOMBRADO ---

    public function closeModal()
    {
        logger()->info("Closing modal: ".$this->id);
        $this->open = false;
        // Opcional: Resetear estado aquí también por si acaso
        // $this->reset('file1');
        // $this->clearMyValidation();
    }

    // Método centralizado para manejar la finalización de la importación
    private function handleImportCompletion($message = null, $error = null) {
        if ($error) {
            session()->flash('error', $error);
            Log::error("Error en importación (Componente ID: {$this->id}): " . $error);
        } else {
            $successMessage = $message ?? 'Archivo importado correctamente.';
            session()->flash('message', $successMessage);
            Log::info("Importación completada (Componente ID: {$this->id}): ".$successMessage);
        }

        // El dispatch 'close-modal' ahora es manejado por Alpine con delay si hay mensaje
        $this->dispatch('close-modal', $this->id);

        // Notificar a otros componentes si es necesario (ej. para refrescar datos)
        $this->dispatch('fileUploaded', ['id' => $this->id]);

        // Resetear el input de archivo en Livewire (Alpine ya lo hace visualmente)
        $this->reset('file1');
    }


    public function importFileGloss()
    {
        Log::info("Intentando importar Glosas para FileID: {$this->fileId}, TipoArchivo: {$this->tipoArchivo}");
        $validatedData = $this->validate([
             'file1' => 'required|file|mimes:csv,xlsx,txt|max:10240',
        ]);

        try {
            Log::info('Archivo de Glosas recibido: ' . $this->file1->getClientOriginalName());
            $path = $this->file1->store('uploads/gloss', 'public'); // Almacena el archivo

            // Obtener la ruta completa
            $fullPath = Storage::disk('public')->path($path);

            Log::info("TipoArchivo pasado a GlossImport: " . $this->tipoArchivo . " para FileID: " . $this->fileId);
            Excel::import(new GlossImport($this->tipoArchivo, $this->fileId), $fullPath); // Usar la ruta completa

            $this->handleImportCompletion('Archivo de Glosas importado correctamente.');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Error de validación al importar:';
            foreach ($failures as $failure) {
                $errorMsg .= " Fila {$failure->row()}: " . implode(', ', $failure->errors());
                Log::error("Error de validación en importación Glosas: Fila {$failure->row()}: " . implode(', ', $failure->errors()));
            }
            $this->handleImportCompletion(null, $errorMsg);
        } catch (\Exception $e) {
             $this->handleImportCompletion(null, 'Error general al importar Glosas: ' . $e->getMessage());
        }
    }


    public function importFilePays()
    {
         Log::info("Intentando importar Pagos para FileID: {$this->fileId}, TipoArchivo: {$this->tipoArchivo}");
         $validatedData = $this->validate([
             'file1' => 'required|file|mimes:csv,xlsx,txt|max:10240',
         ]);

        try {
            Log::info('Archivo de Pagos recibido: ' . $this->file1->getClientOriginalName());
            $path = $this->file1->store('uploads/pays', 'public'); // Almacena el archivo

            $fullPath = Storage::disk('public')->path($path);

            Log::info("TipoArchivo pasado a PayImport: " . $this->tipoArchivo . " para FileID: " . $this->fileId);
            Excel::import(new PayImport($this->tipoArchivo, $this->fileId), $fullPath);

             $this->handleImportCompletion('Archivo de Pagos importado correctamente.');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
            $errorMsg = 'Error de validación al importar:';
            foreach ($failures as $failure) {
                $errorMsg .= " Fila {$failure->row()}: " . implode(', ', $failure->errors());
                Log::error("Error de validación en importación Pagos: Fila {$failure->row()}: " . implode(', ', $failure->errors()));
            }
            $this->handleImportCompletion(null, $errorMsg);
        } catch (\Exception $e) {
             $this->handleImportCompletion(null, 'Error general al importar Pagos: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.modals.file-upload');
    }

}