<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PayRegister;
use App\Models\GlossRegister;

class InvoiceSearch extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $filterNit = '';
    public $filterName = '';
    public $perPage = 15;

    protected $queryString = [
        'searchTerm' => ['except' => '', 'as' => 'factura'],
        'filterNit' => ['except' => '', 'as' => 'nit'],
        'filterName' => ['except' => '', 'as' => 'nombre'],
        'page' => ['except' => 1],
    ];

    public function updatingSearchTerm() { $this->resetPage(); }
    public function updatingFilterNit() { $this->resetPage(); }
    public function updatingFilterName() { $this->resetPage(); }

    public function render()
    {
        // Realizar la búsqueda principal
        $results = $this->performSearch();

        // Si hay resultados, obtener su estado (post-procesamiento)
        if ($results && $results->total() > 0) {
            $this->fetchInvoiceStatuses($results);
        }

        return view('livewire.search.invoice-search', [
            'results' => $results,
        ])->layout('layouts.app');
    }

    protected function performSearch()
    {
        if (empty(trim($this->searchTerm)) && empty(trim($this->filterNit)) && empty(trim($this->filterName))) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }

        $searchTerm = trim($this->searchTerm);
        $filterNit = trim($this->filterNit);
        $filterName = trim($this->filterName);

        // --- Query para registers_v1 ---
        $queryV1 = DB::table('registers_v1 as r1')
            ->join('files', 'files.id', '=', 'r1.idFile')
            ->select( // **** AÑADIR files.fechaCargue AQUÍ ****
                'r1.id', 'r1.idFile', 'files.idCanasta', 'files.fechaCargue', // <-- Añadido
                DB::raw("'registers_v1' as source_table"),
                'r1.consecutivo', 'r1.tipo_documento', 'r1.numero_documento',
                'r1.no_factura_muestra', 'r1.no_factura_procesamiento',
                DB::raw("COALESCE(r1.nit_ips_tomo_muestra, r1.nit_laboratorio_procesamiento) as provider_nit"),
                DB::raw("COALESCE(r1.nombre_ips_tomo_muestra, r1.nombre_laboratorio_procesamiento) as provider_name")
            )
            // WHEN clauses completas como en la versión anterior
             ->when($searchTerm, function ($query, $term) { $query->where(function($q) use ($term) { $q->where('r1.no_factura_muestra', 'like', '%'.$term.'%')->orWhere('r1.no_factura_procesamiento', 'like', '%'.$term.'%'); }); })
             ->when($filterNit, function ($query, $nit) { $query->where(function($q) use ($nit) { $q->where('r1.nit_ips_tomo_muestra', 'like', '%'.$nit.'%')->orWhere('r1.nit_laboratorio_procesamiento', 'like', '%'.$nit.'%'); }); })
             ->when($filterName, function ($query, $name) { $query->where(function($q) use ($name) { $q->where('r1.nombre_ips_tomo_muestra', 'like', '%'.$name.'%')->orWhere('r1.nombre_laboratorio_procesamiento', 'like', '%'.$name.'%'); }); });


        // --- Query para registers_v2 ---
        $queryV2 = DB::table('registers_v2 as r2')
             ->join('files', 'files.id', '=', 'r2.idFile')
             ->select( // **** AÑADIR files.fechaCargue AQUÍ ****
                'r2.id', 'r2.idFile', 'files.idCanasta', 'files.fechaCargue', // <-- Añadido
                DB::raw("'registers_v2' as source_table"),
                'r2.consecutivo', 'r2.tipo_documento', 'r2.numero_documento',
                'r2.no_factura as no_factura_muestra',
                DB::raw("NULL as no_factura_procesamiento"),
                'r2.nit as provider_nit',
                'r2.nombre as provider_name'
             )
              // WHEN clauses completas como en la versión anterior
             ->when($searchTerm, function ($query, $term) { $query->where('r2.no_factura', 'like', '%'.$term.'%'); })
             ->when($filterNit, function ($query, $nit) { $query->where('r2.nit', 'like', '%'.$nit.'%'); })
             ->when($filterName, function ($query, $name) { $query->where('r2.nombre', 'like', '%'.$name.'%'); });


        // --- Combinar Queries ---
        $unionQuery = $queryV1->unionAll($queryV2);

        $finalQuery = DB::query()->fromSub($unionQuery, 'union_results')
                        ->orderBy('consecutivo', 'asc'); // O por fechaCargue? ->orderBy('fechaCargue', 'desc')

        try {
            return $finalQuery->paginate($this->perPage);
        } catch (\Exception $e) {
             Log::error("Error en paginación de búsqueda de factura: " . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    // *** NUEVA FUNCIÓN PARA OBTENER ESTADOS ***
    protected function fetchInvoiceStatuses($results)
    {
        // Obtener los identificadores (idCanasta, consecutivo, tipo_doc, num_doc) de los resultados actuales
        $identifiers = $results->getCollection()->map(function ($item) {
            return [
                'idCanasta' => $item->idCanasta,
                'consecutivo' => $item->consecutivo,
                // Descomenta si necesitas precisión extra y seleccionaste estos campos:
                // 'tipo_documento' => $item->tipo_documento,
                // 'numero_documento' => $item->numero_documento,
            ];
        })->filter()->unique(function ($item) {
            // Clave única para evitar consultas duplicadas si el mismo registro aparece por filtros laxos
             return $item['idCanasta'] . '-' . $item['consecutivo']; // Ajusta si usas más campos
        });

        if ($identifiers->isEmpty()) {
            return;
        }

        // Consultar pagos y glosas para estos identificadores
        // Construye arrays para cláusulas WHERE IN / WHERE multi-columna
        $paidChecks = [];
        $glossChecks = [];
        foreach($identifiers as $id) {
            $paidChecks[] = ['id_register' => $id['idCanasta'], 'consecutivo' => $id['consecutivo']];
            $glossChecks[] = ['id_register' => $id['idCanasta'], 'consecutivo' => $id['consecutivo']];
            // Si usas más campos, añádelos aquí y ajusta las queries de abajo
        }

        // Buscar registros pagados (crea una clave única para mapeo)
        $paid = PayRegister::query()
            ->where(function ($query) use ($paidChecks) {
                foreach ($paidChecks as $check) {
                    $query->orWhere(function ($subQuery) use ($check) {
                        $subQuery->where('id_register', $check['id_register'])
                                 ->where('consecutivo', $check['consecutivo']);
                                // ->where('tipo_documento', $check['tipo_documento']) // si es necesario
                                // ->where('numero_documento', $check['numero_documento']); // si es necesario
                    });
                }
            })
            ->get(['id_register', 'consecutivo', 'tipo_documento', 'numero_documento']) // Selecciona columnas clave
            ->keyBy(function ($item) {
                // Clave consistente con $identifiers
                 return $item->id_register . '-' . $item->consecutivo; // Ajusta si usas más campos
            });


        // Buscar registros glosados
        $glossed = GlossRegister::query()
             ->where(function ($query) use ($glossChecks) {
                foreach ($glossChecks as $check) {
                    $query->orWhere(function ($subQuery) use ($check) {
                        $subQuery->where('id_register', $check['id_register'])
                                 ->where('consecutivo', $check['consecutivo']);
                                // ->where('tipo_documento', $check['tipo_documento']) // si es necesario
                                // ->where('numero_documento', $check['numero_documento']); // si es necesario
                    });
                }
            })
            ->get(['id_register', 'consecutivo', 'tipo_documento', 'numero_documento']) // Selecciona columnas clave
             ->keyBy(function ($item) {
                // Clave consistente con $identifiers
                return $item->id_register . '-' . $item->consecutivo; // Ajusta si usas más campos
            });


        // Asignar estado a cada resultado en la colección paginada
        $results->getCollection()->transform(function ($result) use ($paid, $glossed) {
            $key = $result->idCanasta . '-' . $result->consecutivo; // Ajusta si usas más campos
            if ($paid->has($key)) {
                $result->invoice_status = 'Pagado';
            } elseif ($glossed->has($key)) {
                $result->invoice_status = 'Glosado';
            } else {
                $result->invoice_status = 'Pendiente';
            }
            return $result;
        });
    }


     // Método para mostrar el modal (sin cambios, pero asegúrate que el modal no necesite obligatoriamente idCanasta)
    public function showRegisterDetailsInModal($registerId, $tableName, $idCanasta = null)
    {
         // El $idCanasta que tenemos aquí es el de la fila del resultado, podríamos pasarlo
         // si el modal lo necesita para *su propia* búsqueda de estado.
        Log::info("InvoiceSearch: Showing modal for register ID {$registerId} from table {$tableName}. Canasta ID: {$idCanasta}");

        $this->dispatch('showRegisterModal',
            registerId: $registerId,
            tableName: $tableName,
            idCanasta: $idCanasta // <-- Pasar el idCanasta obtenido
        )->to(\App\Livewire\Modals\RegisterDetail::class);
    }

}