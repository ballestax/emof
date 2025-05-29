<?php

namespace App\Livewire\File;

// Keep existing use statements
use App\Imports\GlossImport;
use App\Imports\PayImport;
use App\Models\File;
use App\Models\GlossRegister; 
use App\Models\PayRegister; 
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public File $file;
    public $isFullVersion = false;
    public $perPage = 15; // Can use different perPage for glosses if needed

    public string $activeTab = 'all';
    public int $allCount = 0;
    public int $glossedCount = 0;
    public int $paidCount = 0;

    // Listeners (optional, adjust as needed)
    protected $listeners = [
        'importSuccess' => 'handleImportSuccess',
        'registerModalClosed' => 'closeRegisterModal',
        'fileUpdated' => 'handleFileUpdated',
    ];

    public function mount(File $file)
    {
        $this->file = $file;
        if (!$this->file) {
            abort(404, 'Archivo no encontrado');
        }
        $this->loadCounts();
    }

    public function toggleTableVersion()
    {
        $this->isFullVersion = !$this->isFullVersion;
        // Reset page only if relevant for the current view (registers V1/V2)
        // if ($this->activeTab !== 'glossed') {
        //     $this->resetPage($this->getPageNameForTab($this->activeTab));
        // }
    }

    protected function getTableName(): string
    {
        return $this->file->esquema == 'nuevo' ? 'registers_v2' : 'registers_v1';
    }

    public function setTab($tab)
    {
        if (in_array($tab, ['all', 'glossed', 'paid'])) {
            $this->activeTab = $tab;
            // Reset pagination for the specific tab's page name
            $this->resetPage($this->getPageNameForTab($tab));
        } else {
            $this->activeTab = 'all';
            $this->resetPage($this->getPageNameForTab('all'));
        }
    }

    protected function getPageNameForTab(string $tab): string
    {
        return match ($tab) {
            'glossed' => 'glossPage',
            'paid' => 'paidPage',
            default => 'allPage',
        };
    }

    public function showRegisterDetails($registerId)
    {
        $tableName = $this->getTableName();
        $idCanasta = $this->file->idCanasta;
        Log::info("Detail: Showing modal for register ID {$registerId} from table {$tableName}");
        $this->selectedRegisterId = $registerId;
        $this->showingRegisterModal = true;

        // **** ACTUALIZAR EL NAMESPACE AQUÍ ****
        $this->dispatch('showRegisterModal', 
                registerId: $registerId, 
                tableName: $tableName,
                idCanasta: $idCanasta
        )->to(\App\Livewire\Modals\RegisterDetail::class); // <-- Cambiado a Modals\RegisterDetailModal
    }

    public function closeRegisterModal()
    {
        Log::info("Detail: Closing register modal");
        $this->showingRegisterModal = false;
        $this->selectedRegisterId = null;
    }

    public function loadCounts()
    {
        Log::info('Loading counts');
        $tableName = $this->getTableName();
        $baseQueryRegisters = DB::table($tableName . ' as main_registers')->where('main_registers.idFile', $this->file->id);

        Log::info('Loading counts for File ID: ' . $this->file->id . ' (Canasta ID: ' . $this->file->idCanasta . ') using table: ' . $tableName);

        try {
            // Count for 'all' tab (from registers_vX)
            $this->allCount = (clone $baseQueryRegisters)->count();

            // --- Glossed Count Calculation (Based on idCanasta) ---
            $this->glossedCount = GlossRegister::where('id_register', $this->file->idCanasta)->count();
            // --- End Glossed Count ---

            // --- Glossed Count Calculation (Based on idCanasta) ---
            $this->paidCount = PayRegister::where('id_register', $this->file->idCanasta)->count();
            // --- End Glossed Count ---
           
            Log::info('Counts loaded successfully', [
                'file_id' => $this->file->id,
                'idCanasta' => $this->file->idCanasta,
                'all' => $this->allCount,
                'glossed' => $this->glossedCount, 
                'paid' => $this->paidCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading counts for File ID: ' . $this->file->id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->allCount = $this->glossedCount = $this->paidCount = 0;
            session()->flash('error', 'Error al cargar los contadores de registros.');
        }
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $tableName = $this->getTableName();
            $fileId = $this->file->id;
            $idCanasta = $this->file->idCanasta; // Get Canasta ID before potentially deleting file

            Log::warning('Attempting to delete file and related records.', [
                'file_id' => $fileId,
                'idCanasta' => $idCanasta,
                'table_registers' => $tableName,
                'table_gloss' => 'gloss_registers'
            ]);

            // 1. Delete related gloss records using idCanasta
            //    Be cautious if multiple files share the same idCanasta - this will delete glosses for ALL of them!
            //    Consider if glosses should only be deleted if this is the *last* file for that idCanasta.
            //    For now, implementing the direct deletion based on the current file's idCanasta:
            GlossRegister::where('id_register', $idCanasta)->delete();
            Log::info('Deleted gloss records linked to Canasta ID.', ['idCanasta' => $idCanasta]);

            // 2. Delete related register records (v1/v2) linked to this file
            DB::table($tableName)->where('idFile', $fileId)->delete();
            Log::info('Deleted register records.', ['file_id' => $fileId, 'table' => $tableName]);

            // 3. Delete the file record itself
            $this->file->delete();
            Log::info('Deleted file record.', ['file_id' => $fileId]);

            // 4. Optionally delete physical file
            // ...

            DB::commit();

            session()->flash('message', 'Archivo y registros asociados (incluyendo glosas de la canasta) eliminados correctamente.');
            return redirect()->route('files.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting file: ' . $e->getMessage(), [
                'file_id' => $fileId ?? $this->file->id ?? null,
                'idCanasta' => $idCanasta ?? $this->file->idCanasta ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }

    // --- Import Methods (Placeholders - Adapt as needed) ---
    public function handleImportSuccess()
    {
        Log::info('¡Listener handleImportSuccess EJECUTADO!'); // <--- Añadir log aquí
        Log::info('Import successful, refreshing counts and resetting page.', ['file_id' => $this->file->id]);
        $this->loadCounts();
        $this->resetPage($this->getPageNameForTab($this->activeTab));
        session()->flash('message', 'Archivo importado con éxito.');
    }

    public function handleFileUpdated()
    {
        Log::info("Detail Component: Evento fileUpdated recibido. Refrescando datos del archivo.");
        $this->file->refresh(); // Recarga el modelo $file desde la base de datos
        $this->loadCounts(); // Recarga los contadores si es necesario
        // No necesitas $this->render() explícitamente, Livewire lo hará.
        // Si quieres forzar un re-render de todo el componente: $this->dispatch('$refresh');
        // pero refrescar el modelo y los contadores debería ser suficiente.
    }

    public function importFileGloss(string $filePath)
    {
        Log::info('Attempting gloss import for File ID: ' . $this->file->id . ' (linked to idCanasta: ' . $this->file->idCanasta . ')', ['filePath' => $filePath]);
        try {
            if (!Storage::exists($filePath)) {
                 throw new \Exception('Uploaded gloss file not found at path: ' . $filePath);
             }
            // Pass idCanasta to the importer if it needs it to set the id_register field
            Excel::import(new GlossImport($this->file->idCanasta), $filePath); // Assuming importer takes idCanasta

            $this->dispatch('importSuccess');

         } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             Log::error('Gloss import validation error', ['file_id' => $this->file->id, 'errors' => $e->failures()]);
             session()->flash('error', 'Error de validación al importar glosas. Revise el archivo.');
         } catch (\Exception $e) {
             Log::error('Gloss import general error', ['file_id' => $this->file->id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
             session()->flash('error', 'Error general al importar glosas: ' . $e->getMessage());
         } finally {
             if (isset($filePath) && Storage::exists($filePath)) { Storage::delete($filePath); }
         }
    }

    public function importFilePays(string $filePath) // Asume que el modal solo pasa la ruta
    {
        $idCanasta = $this->file->idCanasta;
        $esquema = $this->file->esquema; // <-- OBTENER ESQUEMA DEL ARCHIVO ACTUAL

        // Validación básica del esquema obtenido
        if (!in_array($esquema, ['nuevo', 'anterior'])) {
            Log::error('Esquema inválido encontrado en archivo File ID: ' . $this->file->id, ['esquema' => $esquema]);
            session()->flash('error', 'El archivo principal tiene un esquema desconocido.');
            if (Storage::disk('public')->exists($filePath)) { Storage::disk('public')->delete($filePath); } // Corregido disco si es necesario
            return;
        }

        // Mapear 'nuevo' a 'tipo_2' y 'anterior' a 'tipo_1' si PayImport usa esos strings
        $tipoArchivoParaImporter = ($esquema === 'nuevo') ? 'tipo_2' : 'tipo_1';

        Log::info("Attempting PAY import for File ID: {$this->file->id}, Canasta: {$idCanasta}, Esquema Detectado: {$esquema}, Tipo para Importer: {$tipoArchivoParaImporter}", ['filePath' => $filePath]);

        try {
            // Usa la ruta correcta dependiendo de tu disco de storage
            $fullPath = Storage::disk('public')->path($filePath); // O solo $filePath si ya es la ruta absoluta/correcta

            if (!file_exists($fullPath)) {
                throw new \Exception('Uploaded pay file not found at path: ' . $fullPath);
            }

            // *** PASAR EL TIPO CORRECTO AL IMPORTER ***
            Excel::import(new PayImport($tipoArchivoParaImporter, $idCanasta), $fullPath);

            $this->dispatch('importSuccess'); // Para refrescar contadores, etc.

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // ... manejo de error validación ...
            session()->flash('error', 'Error de validación al importar pagos.');
        } catch (\Exception $e) {
            // ... manejo de error general ...
            session()->flash('error', 'Error general al importar pagos: ' . $e->getMessage());
        } finally {
            // Borrar archivo temporal si aún existe
            if (isset($fullPath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info('Archivo temporal de pago eliminado: ' . $filePath);
            }
        }
    }

    // Renders the component view with the appropriate data based on the active tab
    public function render(): Renderable
    {
        $tableName = $this->getTableName();
        $pageName = $this->getPageNameForTab($this->activeTab);
        $fileId = $this->file->id;

        // Initialize data variables
        $registers = null;
        $glossedData = null;
        $paidData = null;

        Log::debug('Rendering File Detail', [ /* ... logging info ... */ ]);

        try {
            if ($this->activeTab === 'glossed') {
                $idCanastaActual = $this->file->idCanasta; // Guarda el valor para logs
                Log::info('DEBUG: Intentando buscar glosas para idCanasta: ' . $idCanastaActual);

                $glossQuery = GlossRegister::query()
                    ->where('gloss_registers.id_register', $idCanastaActual); // SOLO el filtro base

                $selectColumns = ['gloss_registers.*'];

                if ($this->file->esquema == 'nuevo') { 
                    $selectColumns[] = $tableName . '.no_factura as register_no_factura';
                    $selectColumns[] = $tableName . '.nit as register_nit_prestador';
                    $selectColumns[] = $tableName . '.nombre as register_nombre_prestador';
                    $selectColumns[] = $tableName . '.valor as register_valor';

                    $glossQuery->leftJoin($tableName, function ($join) use ($tableName, $fileId) {
                        $join->on('gloss_registers.consecutivo', '=', $tableName . '.consecutivo')
                             ->on('gloss_registers.tipo_documento', '=', $tableName . '.tipo_documento')
                             ->on('gloss_registers.numero_documento', '=', $tableName . '.numero_documento')
                             ->where($tableName . '.idFile', '=', $fileId); // Crucial: Unir solo con registros de ESTE archivo
                    });
                } else {
                    $selectColumns[] = $tableName . '.nombre_ips_tomo_muestra as register_ips_toma';
                    $selectColumns[] = $tableName . '.nombre_laboratorio_procesamiento as register_ips_proc';
                    $selectColumns[] = $tableName . '.no_factura_muestra as register_no_factura_toma';
                    $selectColumns[] = $tableName . '.no_factura_procesamiento as register_no_factura_proc';
                    $selectColumns[] = $tableName . '.valor_toma_muestra_a_cobrar_adres as register_valor_toma';
                    $selectColumns[] = $tableName . '.valor_procesamiento_a_cobrar_adres as register_valor_proc';
                    //$selectColumns[] = $tableName . '.valor as register_valor';

                    $glossQuery->leftJoin($tableName, function ($join) use ($tableName, $fileId) {
                        $join->on('gloss_registers.consecutivo', '=', $tableName . '.consecutivo')
                             ->on('gloss_registers.tipo_documento', '=', $tableName . '.tipo_documento')
                             ->on('gloss_registers.numero_documento', '=', $tableName . '.numero_documento')
                             ->where($tableName . '.idFile', '=', $fileId); // Crucial: Unir solo con registros de ESTE archivo
                    });
                    Log::info('DEBUG: Esquema no es nuevo, no se une para no_factura.');
                }

                $glossQuery->select($selectColumns);
                $glossQuery->orderBy('gloss_registers.consecutivo', 'asc');

                $glossedData = $glossQuery->paginate($this->perPage, $selectColumns, $pageName);

                Log::info('DEBUG: Datos de glosa paginados (con JOIN si aplica).', [
                    'idCanasta_usado' => $idCanastaActual,
                    'fileType' => $this->file->esquema,
                    'columns_selected_count' => count($selectColumns),
                    'registros_en_pagina' => $glossedData->count(),
                    'total_registros' => $glossedData->total()
                ]);
                
            } else if ($this->activeTab === 'paid') {
                Log::info('Paid: '.$this->file->idCanasta);
                $payQuery = PayRegister::where('id_register', $this->file->idCanasta);

                $selectColumns = ['pay_registers.*'];

                if ($this->file->esquema == 'nuevo') { 
                    $selectColumns[] = $tableName . '.no_factura as register_no_factura';
                    $selectColumns[] = $tableName . '.nit as register_nit_prestador';
                    $selectColumns[] = $tableName . '.nombre as register_nombre_prestador';
                    $selectColumns[] = $tableName . '.valor as register_valor';

                    $payQuery->leftJoin($tableName, function ($join) use ($tableName, $fileId) {
                        $join->on('pay_registers.consecutivo', '=', $tableName . '.consecutivo')
                             // Descomenta las siguientes líneas si 'consecutivo' solo no es único por archivo:
                             // ->on('gloss_registers.tipo_documento', '=', $tableName . '.tipo_documento')
                             // ->on('gloss_registers.numero_documento', '=', $tableName . '.numero_documento')
                             ->where($tableName . '.idFile', '=', $fileId); // Crucial: Unir solo con registros de ESTE archivo
                    });
                } else {
                    $selectColumns[] = $tableName . '.nombre_ips_tomo_muestra as register_ips_toma';
                    $selectColumns[] = $tableName . '.nombre_laboratorio_procesamiento as register_ips_proc';
                    $selectColumns[] = $tableName . '.no_factura_muestra as register_no_factura_toma';
                    $selectColumns[] = $tableName . '.no_factura_procesamiento as register_no_factura_proc';
                    $selectColumns[] = $tableName . '.valor_toma_muestra_a_cobrar_adres as register_valor_toma';
                    $selectColumns[] = $tableName . '.valor_procesamiento_a_cobrar_adres as register_valor_proc';

                    $payQuery->leftJoin($tableName, function ($join) use ($tableName, $fileId) {
                        $join->on('pay_registers.consecutivo', '=', $tableName . '.consecutivo')
                             // Descomenta las siguientes líneas si 'consecutivo' solo no es único por archivo:
                             // ->on('gloss_registers.tipo_documento', '=', $tableName . '.tipo_documento')
                             // ->on('gloss_registers.numero_documento', '=', $tableName . '.numero_documento')
                             ->where($tableName . '.idFile', '=', $fileId); // Crucial: Unir solo con registros de ESTE archivo
                    });
                }

                // Add any desired ordering for glosses, e.g., by date
                $payQuery->select($selectColumns);
                $payQuery->orderBy('pay_registers.consecutivo', 'asc');

                $paidData = $payQuery->paginate($this->perPage, $selectColumns, $pageName);
                Log::debug('Fetched pay data directly.', ['count' => $paidData->count(), 'total' => $paidData->total()]);
                // --- End Fetch Gloss Data ---

            } else {
                // --- Fetch Register Data (for 'all' or 'paid') ---
                $query = DB::table($tableName . ' as main_registers')
                           ->where('main_registers.idFile', $this->file->id);

                $query->orderBy('main_registers.consecutivo', 'asc'); // Order registers
                $registers = $query->paginate($this->perPage, ['main_registers.*'], $pageName);
                Log::debug('Fetched register data.', ['count' => $registers->count(), 'total' => $registers->total()]);
                 // --- End Fetch Register Data ---
            }

        } catch (\Exception $e) {
            Log::error('Error fetching data for render', [
                'file_id' => $this->file->id,
                'idCanasta' => $this->file->idCanasta,
                'active_tab' => $this->activeTab,
                'message' => $e->getMessage()
            ]);
            // Return empty pagination objects on error
            $registers = $registers ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage, 1, ['pageName' => $this->getPageNameForTab('all')]);
            $glossedData = $glossedData ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage, 1, ['pageName' => $this->getPageNameForTab('glossed')]);
            $paidData = $paidData ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage, 1, ['pageName' => $this->getPageNameForTab('paid')]);
            session()->flash('error', 'Error al cargar los datos de la pestaña.');
        }

        // Pass all potential data variables to the view
        return view('livewire.file.detail', [
            'registers' => $registers,
            'glossedData' => $glossedData,
            'paidData' => $paidData,
            'fileType' => $this->file->esquema,
        ]);
    }
}