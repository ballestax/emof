<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Historial de Factura</h1>

    {{-- Sección de Búsqueda --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 mb-6">
         <form wire:submit.prevent="searchHistory">
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                 {{-- Búsqueda por Factura --}}
                 <div>
                     <label for="search_invoice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Factura <span class="text-red-500">*</span></label>
                     <input wire:model="searchInvoiceNumber" type="text" id="search_invoice" required
                             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 @error('searchInvoiceNumber') border-red-500 @enderror"
                             placeholder="Factura a buscar...">
                     @error('searchInvoiceNumber') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                 </div>

                 {{-- Filtro por NIT --}}
                 <div>
                     <label for="search_nit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIT Prestador (Opcional)</label>
                     <input wire:model="searchProviderNit" type="text" id="search_nit"
                             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 @error('searchProviderNit') border-red-500 @enderror"
                             placeholder="Filtrar por NIT...">
                      @error('searchProviderNit') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                 </div>

                 {{-- Botón Buscar --}}
                 <div>
                      <button type="submit"
                              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full md:w-auto"
                              wire:loading.attr="disabled" wire:target="searchHistory">
                          <span wire:loading.remove wire:target="searchHistory">Buscar Historial</span>
                          <span wire:loading wire:target="searchHistory">
                              <i class="fas fa-spinner fa-spin mr-2"></i> Buscando...
                          </span>
                      </button>
                 </div>
             </div>
         </form>
         {{-- Mensaje de error general --}}
         @if (session()->has('error'))
             <div class="mt-4 text-sm text-red-600 dark:text-red-400">
                 {{ session('error') }}
             </div>
         @endif
    </div>

    {{-- Sección de Resultados --}}
    @if($searched)
         <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mt-6">
             <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                      Historial de Cargas para Factura: <strong>{{ $searchInvoiceNumber }} {{ $searchProviderNit ? '(NIT: '.$searchProviderNit.')' : '' }} </strong>
                  </h3>
             </div>
             <div class="overflow-x-auto">
                 <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                     <thead class="bg-gray-50 dark:bg-gray-700">
                         <tr>
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Cargue</th>
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Canasta</th>
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo Factura Encontrada</th> {{-- New Header --}}
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. Factura</th> {{-- New Header --}}
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prestador Asociado</th> {{-- Modified/New Header --}}
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo Archivo</th>
                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado en Carga</th>
                             {{-- Puedes añadir más columnas si las seleccionaste en el PHP, ej: Consecutivo --}}
                         </tr>
                     </thead>
                     <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                         @forelse($historyResults as $history)
                              @php $item = (object) $history; // Convertir array a objeto para acceso más fácil @endphp
                              <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                       {{ isset($item->fechaCargue) ? \Carbon\Carbon::parse($item->fechaCargue)->format('d/m/Y') : 'N/A' }}
                                  </td>
                                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->idCanasta ?? 'N/A' }}</td>

                                  {{-- New Cell for Matched Invoice Type --}}
                                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                      @if(isset($item->matched_invoice_type))
                                          <span @class([
                                              'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                              'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' => $item->matched_invoice_type === 'Muestra',
                                              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $item->matched_invoice_type === 'Procesamiento',
                                              'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' => $item->matched_invoice_type === 'V2',
                                          ])>
                                              {{ $item->matched_invoice_type }}
                                          </span>
                                      @else
                                          <span class="text-gray-400">-</span>
                                      @endif
                                  </td>

                                  {{-- New Cell for Matched Invoice Number --}}
                                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                      {{ $item->matched_invoice_number ?? 'N/A' }}
                                  </td>

                                  {{-- New Cell for Corresponding Provider Name --}}
                                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                      @if($item->matched_invoice_type === 'Muestra')
                                          {{ $item->provider_name_muestra ?? 'N/A' }}
                                      @elseif($item->matched_invoice_type === 'Procesamiento')
                                          {{ $item->provider_name_procesamiento ?? 'N/A' }}
                                      @elseif($item->matched_invoice_type === 'V2')
                                           {{ $item->name_v2 ?? 'N/A' }}
                                      @else
                                          N/A
                                      @endif
                                  </td>

                                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                      <span @class([
                                           'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                           'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' => $item->esquema == 'anterior', // Tipo 1
                                           'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' => $item->esquema == 'nuevo', // Tipo 2
                                       ])>
                                           {{ $item->esquema == 'anterior' ? 'Tipo 1' : 'Tipo 2' }}
                                      </span>
                                  </td>
                                  <td class="px-4 py-3 whitespace-nowrap text-sm">
                                       @if(isset($item->status))
                                           <span @class([
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $item->status === 'Pagado',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $item->status === 'Glosado',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' => $item->status === 'Pendiente',
                                            ])>
                                                {{ $item->status }}
                                            </span>
                                       @else
                                           <span class="text-gray-400">-</span>
                                       @endif
                                  </td>
                                  {{-- Ejemplo: Celda para consecutivo si lo seleccionaste --}}
                                  {{-- <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->consecutivo ?? 'N/A' }}</td> --}}
                              </tr>
                         @empty
                              <tr>
                                  <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400"> {{-- Updated colspan --}}
                                       No se encontró historial para esta factura con los filtros aplicados.
                                  </td>
                              </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
         </div>
    @elseif($loading)
         <div class="text-center mt-6 text-gray-500 dark:text-gray-400">
             <i class="fas fa-spinner fa-spin text-2xl"></i> Buscando historial...
         </div>
    @endif
</div>