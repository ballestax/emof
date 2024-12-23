<?php

namespace App\Livewire;

use App\Models\File;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FilesImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileForm extends Component
{
    use WithPagination, WithFileUploads;

    public $idCanasta;
    public $guid;
    public $esquema;
    public $registros;
    public $estado;
    public $fechaCargue;
    public $file_id;
    public $file;

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
        $this->importFile();
        Log::info('File imported successfully');
        Log::info('Data to be saved', [
            'idCanasta' => $this->idCanasta,
            'esquema' => $this->esquema,
            'registros' => $this->registros,
            'estado' => $this->estado,
            'fechaCargue' => $this->fechaCargue
        ]);
        File::create([
            'idCanasta' => $this->idCanasta,
            'esquema' => $this->esquema,
            'registros' => $this->registros,
            'estado' => $this->estado,
            'fechaCargue' => $this->fechaCargue,
            'GUID' => uniqid(),
            'tipoArchivo' => 1,
        ]);
        Log::info('File saved to database');
        $this->reset();
    }

    public function updatedFile()
    {
        if ($this->file) {
            $this->importFile();
        }
    }

    public function importFile()
    {
        Log::info('Iniciando import');
        $path = $this->file->store('files');
        $rowCount = Excel::toCollection(new FilesImport, $path)->first()->count();
        $this->registros = $rowCount - 1; // Restar 1 para omitir la fila del encabezado
        Log::info('Número de registros importados: ' . $this->registros);
       // Excel::import(new FilesImport, $path);
        Storage::delete($path); // Eliminar el archivo después de la importación
    }

    public function edit($id)
    {
        $file = File::find($id);
        $this->file_id = $file->id;
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

    public function render()
    {
        return view('livewire.file-form', ['files' => File::latest()->paginate(10)]);
    }
}
