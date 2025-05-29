<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\File;
use App\Models\GlossRegister;
use App\Models\PayRegister;
use Illuminate\Support\Facades\DB; // Necesario para contar registros v1/v2
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    // Propiedades para las estadísticas
    public int $totalFiles = 0;
    public int $totalRegisters = 0;
    public int $totalGlossedUnique = 0; // Glosas únicas (canasta+consec)
    public int $totalPaidUnique = 0;    // Pagos únicos (canasta+consec)
    public int $filesWithError = 0;

    // Propiedad para archivos recientes
    public $recentFiles;

    // Cargar datos cuando el componente se monta
    public function mount()
    {
        $this->loadStats();
        $this->loadRecentFiles();
    }

    // Carga las estadísticas principales
    protected function loadStats()
    {
        $this->totalFiles = File::count();
        $this->filesWithError = File::where('estado', 4)->count(); // Asumiendo 4 = Error

        // Contar registros totales (sumando ambas tablas)
        $countV1 = DB::table('registers_v1')->count();
        $countV2 = DB::table('registers_v2')->count();
        $this->totalRegisters = $countV1 + $countV2;

        // Contar glosas únicas por canasta y consecutivo
        // NOTA: Esto asume que id_register = idCanasta. Si la lógica es diferente, ajustar.
        //$this->totalGlossedUnique = GlossRegister::distinct(['id_register', 'consecutivo'])->count(['id_register', 'consecutivo']);
        // Contar pagos únicos por canasta y consecutivo
       // $this->totalPaidUnique = PayRegister::distinct(['id_register', 'consecutivo'])->count(['id_register', 'consecutivo']);
       // Contar Glosas Únicas (Canasta + Consecutivo) usando Subconsulta
        try {
            $distinctGlossQuery = GlossRegister::select('id_register', 'consecutivo')->distinct();
            // Creamos una consulta que cuenta desde la subconsulta de distintos
            $this->totalGlossedUnique = DB::table(DB::raw("({$distinctGlossQuery->toSql()}) as sub"))
                                            ->mergeBindings($distinctGlossQuery->getQuery()) // IMPORTANTE: pasar bindings
                                            ->count();
        } catch (\Exception $e) {
            Log::error("Error contando glosas únicas: " . $e->getMessage());
            $this->totalGlossedUnique = 0; // Valor por defecto en caso de error
        }


        // Contar Pagos Únicos (Canasta + Consecutivo) usando Subconsulta
        try {
            $distinctPayQuery = PayRegister::select('id_register', 'consecutivo')->distinct();
            // Creamos una consulta que cuenta desde la subconsulta de distintos
            $this->totalPaidUnique = DB::table(DB::raw("({$distinctPayQuery->toSql()}) as sub"))
                                        ->mergeBindings($distinctPayQuery->getQuery()) // IMPORTANTE: pasar bindings
                                        ->count();
        } catch (\Exception $e) {
            Log::error("Error contando pagos únicos: " . $e->getMessage());
            $this->totalPaidUnique = 0; // Valor por defecto en caso de error
        }

    }

    public function openUploadModal()
    {
        Log::info('Dashboard: Abriendo modal MainFileUpload');
        // Emite el evento al componente modal específico para que se abra
        $this->dispatch('showMainFileUpload')->to(\App\Livewire\Modals\MainFileUpload::class);
    }

    public function refreshDashboardData()
    {
        Log::info('Dashboard: Refrescando datos después de carga de archivo...');
        $this->loadStats();        // Recalcular estadísticas
        $this->loadRecentFiles();  // Recargar archivos recientes
        // Livewire se encargará de re-renderizar la vista con los datos actualizados
         session()->flash('message', 'Archivo cargado y dashboard actualizado.'); // Mensaje opcional
    }

    // Carga los últimos archivos
    protected function loadRecentFiles()
    {
        // Ordena por 'created_at' (o 'fechaCargue' si prefieres) y toma 5
        $this->recentFiles = File::orderBy('created_at', 'desc')->take(5)->get();
    }

    public function render()
    {
        // El layout debe existir en resources/views/layouts/app.blade.php
        return view('livewire.dashboard')->layout('layouts.app');
    }
}