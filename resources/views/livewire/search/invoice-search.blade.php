<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Buscador de Facturas</h1>

    {{-- Sección de Filtros --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Búsqueda por Factura --}}
            <div>
                <label for="search_term" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Factura</label>
                <input wire:model.live.debounce.500ms="searchTerm" type="text" id="search_term"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600"
                       placeholder="Buscar factura...">
            </div>

            {{-- Filtro por NIT --}}
            <div>
                <label for="filter_nit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por NIT</label>
                <input wire:model.live.debounce.500ms="filterNit" type="text" id="filter_nit"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600"
                       placeholder="NIT prestador...">
            </div>

            {{-- Filtro por Nombre --}}
            <div>
                <label for="filter_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Nombre</label>
                <input wire:model.live.debounce.500ms="filterName" type="text" id="filter_name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600"
                       placeholder="Nombre prestador...">
            </div>
        </div>
         {{-- Indicador de carga --}}
         <div wire:loading wire:target="searchTerm, filterNit, filterName" class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            <i class="fas fa-spinner fa-spin mr-2"></i> Buscando...
        </div>
    </div>

    {{-- Sección de Resultados --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Canasta</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Factura(s)</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prestador</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">NIT</th>   
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Origen</th>
                        <th scope="col" class="relative px-4 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @if($results && $results->count() > 0)
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{-- ... (Celdas existentes: consecutivo, facturas, prestador, nit) ... --}}
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $result->idCanasta ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{-- Formatear la fecha - Asume que $result->fechaCargue es string/date --}}
                                    {{ isset($result->fechaCargue) ? \Carbon\Carbon::parse($result->fechaCargue)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @if($result->source_table == 'registers_v2')
                                        {{ $result->no_factura_muestra ?? 'N/A' }}
                                    @else
                                        M: {{ $result->no_factura_muestra ?? 'N/A' }} <br>
                                        P: {{ $result->no_factura_procesamiento ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-100">{{ $result->provider_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-100">{{ $result->provider_nit ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if(isset($result->invoice_status))
                                        <span @class([
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $result->invoice_status === 'Pagado',
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $result->invoice_status === 'Glosado',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' => $result->invoice_status === 'Pendiente',
                                        ])>
                                            {{ $result->invoice_status }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                {{-- **** FIN NUEVAS CELDAS **** --}}

                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                     <span @class([ /* ... clases para origen ... */ ])>
                                        {{ $result->source_table == 'registers_v1' ? 'Tipo 1' : 'Tipo 2' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- Pasar idCanasta al modal --}}
                                    <button wire:click="showRegisterDetailsInModal({{ $result->id }}, '{{ $result->source_table }}', {{ $result->idCanasta ?? 'null' }})" type="button"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Ver Detalles
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                     @elseif(!empty(trim($this->searchTerm)) || !empty(trim($this->filterNit)) || !empty(trim($this->filterName)))
                        <tr>
                            {{-- Ajustar colspan al nuevo número de columnas (ahora 8) --}}
                            <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron facturas que coincidan con los criterios de búsqueda.
                            </td>
                        </tr>
                    @else
                         <tr>
                             {{-- Ajustar colspan al nuevo número de columnas (ahora 8) --}}
                            <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Ingrese un término de búsqueda o filtro.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{-- Paginación --}}
        @if($results && $results->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
             {{ $results->links() }}
        </div>
        @endif
    </div>

    {{-- Incluir el modal para reutilizarlo --}}
    <livewire:modals.register-detail wire:key="invoice-search-register-detail" />

</div>