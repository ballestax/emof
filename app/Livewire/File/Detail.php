<?php

namespace App\Livewire\File;

use App\Imports\GlossImport;
use App\Models\File;
use App\Models\Register;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Detail extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public $file;
    public $isFullVersion = false; // Estado inicial es la versión completa
    public $perPage = 10; // Número de registros por página
   
    public function mount($file)
    {
        $this->file = File::find($file->id);
        if (!$this->file) {
            abort(404, 'Archivo no encontrado');
        }
    }

    public function toggleTableVersion()
    {
        $this->isFullVersion = !$this->isFullVersion; // Alterna entre versiones
        // $this->resetPage();
    }

    public function render(): Renderable
    {
        $tableName = $this->file->esquema == 'nuevo' ? 'registers_v2' : 'registers_v1';

        $registers = DB::table($tableName)
        ->where('idFile', $this->file->id)
        ->paginate($this->perPage);

        return view(
            'livewire.file.detail',
            [
                'registers' => $registers,
                'fileType' => $this->file->esquema,
            ]
        );
    }
   
}
