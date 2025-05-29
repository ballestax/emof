<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PayRegister;
use App\Models\GlossRegister;
use Illuminate\Support\Collection; // Para usar Colecciones

class InvoiceHistory extends Component
{
    public $searchInvoiceNumber = '';
    public $searchProviderNit = '';
    // public $searchProviderName = ''; // Puedes añadir filtro por nombre si lo deseas

    public array $historyResults = []; // Guarda el historial encontrado
    public bool $searched = false;      // Para saber si ya se buscó algo
    public bool $loading = false;       // Para feedback visual

    public function render()
    {
        return view('livewire.search.invoice-history')
               ->layout('layouts.app'); // Asume layout base
    }

    public function searchHistory()
    {
        $this->validate([
            'searchInvoiceNumber' => 'required|string|min:1',
            'searchProviderNit' => 'nullable|string|min:3',
        ]);

        $this->loading = true;
        $this->searched = true;
        $this->historyResults = []; // Limpiar resultados anteriores

        $invoiceNum = trim($this->searchInvoiceNumber);
        $providerNit = trim($this->searchProviderNit);

        try {
            // --- 1. Encontrar instancias de la factura en registers_v1 (Muestra) ---
            $queryV1_muestra = DB::table('registers_v1 as r1')
                ->join('files', 'files.id', '=', 'r1.idFile')
                ->select(
                    'files.idCanasta', 'files.fechaCargue', 'files.esquema',
                    'r1.consecutivo', 'r1.tipo_documento', 'r1.numero_documento',
                    'r1.no_factura_muestra as matched_invoice_number', // Alias to indicate which matched
                    'r1.no_factura_procesamiento as other_invoice_number', // The other V1 invoice number
                    DB::raw("'Muestra' as matched_invoice_type"), // Tag the type of match
                    'r1.nit_ips_tomo_muestra as provider_nit_muestra', // Muestra provider NIT
                    'r1.nombre_ips_tomo_muestra as provider_name_muestra', // Muestra provider Name
                    'r1.nit_laboratorio_procesamiento as provider_nit_procesamiento', // Procesamiento provider NIT
                    'r1.nombre_laboratorio_procesamiento as provider_name_procesamiento', // Procesamiento provider Name
                    DB::raw("NULL as nit_v2"), // V2 specific fields (null for V1)
                    DB::raw("NULL as name_v2") // V2 specific fields (null for V1)
                    // Add other relevant fields from r1 here if needed
                )
                ->where('r1.no_factura_muestra', $invoiceNum)
                ->when($providerNit, function ($query, $nit) {
                    $query->where('r1.nit_ips_tomo_muestra', $nit); // Only filter by muestra NIT for muestra match
                });

            // --- 2. Encontrar instancias de la factura en registers_v1 (Procesamiento) ---
            $queryV1_procesamiento = DB::table('registers_v1 as r1')
                ->join('files', 'files.id', '=', 'r1.idFile')
                ->select(
                    'files.idCanasta', 'files.fechaCargue', 'files.esquema',
                    'r1.consecutivo', 'r1.tipo_documento', 'r1.numero_documento',
                    'r1.no_factura_procesamiento as matched_invoice_number', // Alias to indicate which matched
                    'r1.no_factura_muestra as other_invoice_number', // The other V1 invoice number
                    DB::raw("'Procesamiento' as matched_invoice_type"), // Tag the type of match
                    'r1.nit_ips_tomo_muestra as provider_nit_muestra',
                    'r1.nombre_ips_tomo_muestra as provider_name_muestra',
                    'r1.nit_laboratorio_procesamiento as provider_nit_procesamiento',
                    'r1.nombre_laboratorio_procesamiento as provider_name_procesamiento',
                    DB::raw("NULL as nit_v2"),
                    DB::raw("NULL as name_v2")
                    // Add other relevant fields from r1 here if needed (must match queryV1_muestra select)
                )
                ->where('r1.no_factura_procesamiento', $invoiceNum)
                ->when($providerNit, function ($query, $nit) {
                    $query->where('r1.nit_laboratorio_procesamiento', $nit); // Only filter by procesamiento NIT for procesamiento match
                });


            // --- 3. Encontrar instancias de la factura en registers_v2 ---
            $queryV2 = DB::table('registers_v2 as r2')
                ->join('files', 'files.id', '=', 'r2.idFile')
                ->select(
                    'files.idCanasta', 'files.fechaCargue', 'files.esquema',
                    'r2.consecutivo', 'r2.tipo_documento', 'r2.numero_documento',
                    'r2.no_factura as matched_invoice_number', // V2 only has one invoice
                    DB::raw("NULL as other_invoice_number"), // No other invoice in V2
                    DB::raw("'V2' as matched_invoice_type"), // Tag as V2 match
                    DB::raw("NULL as nit_muestra"), // V1 specific fields (null for V2)
                    DB::raw("NULL as name_muestra"),
                    DB::raw("NULL as nit_procesamiento"),
                    DB::raw("NULL as name_procesamiento"),
                    'r2.nit as nit_v2', // V2 provider NIT
                    'r2.nombre as name_v2' // V2 provider Name
                    // Add other relevant fields from r2 here if needed (must match V1 selects structure)
                )
                ->where('r2.no_factura', $invoiceNum)
                ->when($providerNit, function ($query, $nit) {
                    $query->where('r2.nit', $nit); // Filter by V2 NIT
                });

            // --- 4. Unir los resultados de V1 (Muestra), V1 (Procesamiento) y V2 ---
            // Using unionAll to keep potential duplicates if a V1 record matches *both* invoice numbers
            // and concat V2 results. Ensure select columns are consistent.
            $submissions = $queryV1_muestra
                        ->unionAll($queryV1_procesamiento)
                        ->get() // Get results from the union of V1 queries
                        ->concat($queryV2->get()); // Concatenate with V2 results

            // --- 5. Obtener estado para cada submission (based on unique idCanasta+consecutivo) ---
            if ($submissions->isNotEmpty()) {
                // The fetchStatusesForSubmissions function will work correctly
                // on this combined collection, adding the 'status' property
                // to each object based on the unique idCanasta/consecutivo pair.
                $this->fetchStatusesForSubmissions($submissions);
            }

            // --- 6. Ordenar y asignar resultados ---
            // The collection now has the 'status' property added by fetchStatusesForSubmissions
            $this->historyResults = $submissions->sortBy('fechaCargue')->values()->toArray();

        } catch (\Exception $e) {
            Log::error("Error buscando historial de factura: " . $e->getMessage(), [
                'invoice' => $invoiceNum, 'nit' => $providerNit
            ]);
            session()->flash('error', 'Ocurrió un error al buscar el historial.');
            $this->historyResults = [];
        } finally {
            $this->loading = false;
        }
    }

    // Función auxiliar para buscar estados (similar a la anterior pero adaptada)
    protected function fetchStatusesForSubmissions(Collection $submissions)
    {
         // Extraer identificadores únicos (idCanasta, consecutivo)
         $identifiers = $submissions->map(function ($item) {
            return [
                'idCanasta' => $item->idCanasta,
                'consecutivo' => $item->consecutivo,
                // Añade tipo/num documento si es necesario para identificar estado
                // 'tipo_documento' => $item->tipo_documento,
                // 'numero_documento' => $item->numero_documento,
            ];
        })->unique(function ($item) {
            return $item['idCanasta'] . '-' . $item['consecutivo'];
        });

         if ($identifiers->isEmpty()) { return; }

         // Pre-cargar estados Pagados y Glosados para optimizar
         $paidChecks = []; $glossChecks = [];
         foreach($identifiers as $id) {
             $paidChecks[] = ['id_register' => $id['idCanasta'], 'consecutivo' => $id['consecutivo']];
             $glossChecks[] = ['id_register' => $id['idCanasta'], 'consecutivo' => $id['consecutivo']];
         }

         $paid = PayRegister::query()
             ->where(function ($query) use ($paidChecks) { /* ... where OR ... */ })
             ->get(['id_register', 'consecutivo'])
             ->keyBy(fn($item) => $item->id_register . '-' . $item->consecutivo);

          $glossed = GlossRegister::query()
             ->where(function ($query) use ($glossChecks) { /* ... where OR ... */ })
             ->get(['id_register', 'consecutivo'])
             ->keyBy(fn($item) => $item->id_register . '-' . $item->consecutivo);

         // Añadir el estado a cada submission en la colección original
         $submissions->each(function ($item) use ($paid, $glossed) {
            $key = $item->idCanasta . '-' . $item->consecutivo;
             if ($paid->has($key)) {
                 $item->status = 'Pagado';
             } elseif ($glossed->has($key)) {
                 $item->status = 'Glosado';
             } else {
                 $item->status = 'Pendiente'; // O 'Procesado sin Pago/Glosa'
             }
         });
    }
}