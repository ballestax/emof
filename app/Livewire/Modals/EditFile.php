<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\File; // Asegúrate de importar tu modelo File
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EditFile extends Component
{
    public bool $showModal = false;
    public ?File $fileInstance = null;

    // Propiedades para el formulario (bindeadas con wire:model)
    public string $idCanasta = '';
    public string $esquema = '';
    public string $fechaCargue = '';
    // Agrega aquí otras propiedades del modelo File que quieras editar

    // ID del archivo que se está editando
    public ?int $editingFileId = null;

    protected function rules(): array
    {
        return [
            'idCanasta' => 'required|string|max:255',
            'esquema' => 'required|string|in:nuevo,anterior',
            'fechaCargue' => 'required|date'
            // Agrega reglas de validación para otros campos
        ];
    }

    protected $messages = [
        'idCanasta.required' => 'El ID Canasta es obligatorio.',
        'esquema.required' => 'El Esquema es obligatorio.',
        'fechaCargue.required' => 'La fecha de cargue es obligatoria.',
        'esquema.in' => 'El Esquema seleccionado no es válido.',
        'fechaCargue.in' => 'La fecha seleccionado no es válida.',
    ];

    protected $listeners = [
        'openEditModal' => 'handleOpenEditModal'
    ];

    public function handleOpenEditModal($fileId)
    {
        Log::info("EditFileModal: Abriendo modal para File ID: {$fileId}");
        $this->editingFileId = $fileId;
        $this->resetValidation(); // Limpiar validaciones previas
        $this->loadFileData();
        $this->showModal = true;
    }

    public function loadFileData()
    {
        if ($this->editingFileId) {
            $this->fileInstance = File::find($this->editingFileId);
            if ($this->fileInstance) {
                $this->idCanasta = $this->fileInstance->idCanasta;
                $this->esquema = $this->fileInstance->esquema;
                $this->fechaCargue = $this->fileInstance->fechaCargue
                                    ? $this->fileInstance->fechaCargue->format('Y-m-d') // <--- CORREGIDO: Formato YYYY-MM-DD
                                    : null;
                // Asigna aquí otros campos del modelo a las propiedades públicas
            } else {
                Log::error("EditFileModal: No se encontró el archivo con ID {$this->editingFileId}");
                $this->closeModalOnError(); // Cierra si no se encuentra el archivo
            }
        }
    }

    public function updateFile()
    {
        $this->validate();

        if ($this->fileInstance) {
            $this->fileInstance->idCanasta = $this->idCanasta;
            $this->fileInstance->esquema = $this->esquema;
            $this->fileInstance->fechaCargue = $this->fechaCargue ? Carbon::parse($this->fechaCargue) : null;
            // Actualiza otros campos aquí

            // Lógica adicional si 'tipoArchivo' depende de 'esquema'
            // if ($this->esquema === 'nuevo') {
            //     $this->fileInstance->tipoArchivo = 'tipo_2'; // Ejemplo
            // } else {
            //     $this->fileInstance->tipoArchivo = 'tipo_1'; // Ejemplo
            // }

            $this->fileInstance->save();

            $this->showModal = false;
            $this->dispatch('fileUpdated'); // Evento para notificar al componente padre (Detail)
            $this->dispatch('showFlashMessage', ['message' => 'Archivo actualizado correctamente.', 'type' => 'success']);

        } else {
            Log::error("EditFileModal: Intento de actualizar un archivo nulo.");
            $this->dispatch('showFlashMessage', ['message' => 'Error: No se pudo encontrar el archivo para actualizar.', 'type' => 'error']);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    private function closeModalOnError()
    {
        $this->showModal = false;
        $this->resetForm();
        // Puedes despachar un mensaje de error si lo deseas
        $this->dispatch('showFlashMessage', ['message' => 'Error: No se pudo cargar el archivo para editar.', 'type' => 'error']);
    }

    private function resetForm()
    {
        $this->reset(['idCanasta', 'esquema', 'fechaCargue', 'editingFileId', 'fileInstance']);
        $this->resetValidation();
    }

    public function render()
    {
        // No pasamos $fileInstance directamente aquí, ya que se carga y usa internamente
        return view('livewire.modals.edit-file');
    }
}