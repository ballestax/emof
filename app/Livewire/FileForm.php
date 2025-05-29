<?php

namespace App\Livewire;

use App\Models\File;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileForm extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['mainFileUploaded' => '$refresh']; // '$refresh' es un método mágico de Livewire

    // Método para abrir el nuevo modal
    public function openUploadModal()
    {
        Log::info('Abriendo modal MainFileUpload desde ' . self::class);
        $this->dispatch('showMainFileUpload')->to(\App\Livewire\Modals\MainFileUpload::class);
    }

    public function render()
    {
        $files = File::query()
            ->select(
                'files.*', 
                DB::raw('COUNT(DISTINCT gr.id) as gloss_count'),
                DB::raw('COUNT(DISTINCT pr.id) as pay_count')
            )
            ->leftJoin('gloss_registers as gr', 'gr.id_register', '=', 'files.idCanasta')
            ->leftJoin('pay_registers as pr', 'pr.id_register', '=', 'files.idCanasta')
            ->groupBy('files.id')
            ->orderBy('files.created_at', 'desc')
            ->paginate(10);

        // Asegúrate que el nombre de la vista aquí coincida con tu archivo Blade refactorizado
        return view('livewire.file-form', [
            'files' => $files
        ])->layout('layouts.app');
    }
}
