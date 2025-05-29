<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Dashboard</h1>

    {{-- 1. Sección de Estadísticas --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        {{-- Tarjeta Total Archivos --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-alt fa-2x text-indigo-500 dark:text-indigo-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Archivos Cargados</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalFiles) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('files.index') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300"> Ver todos </a>
                </div>
            </div>
        </div>

        {{-- Tarjeta Total Registros --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
             <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                         <i class="fas fa-list-ol fa-2x text-blue-500 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Registros Procesados</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalRegisters) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
             {{-- Puedes quitar el link inferior si no hay vista específica para registros --}}
             {{-- <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3"></div> --}}
        </div>

         {{-- Tarjeta Registros Glosados --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
             <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                         <i class="fas fa-exclamation-triangle fa-2x text-red-500 dark:text-red-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Registros Glosados (Únicos)</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalGlossedUnique) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            {{-- <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3"></div> --}}
        </div>

         {{-- Tarjeta Registros Pagados --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
             <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                         <i class="fas fa-check-circle fa-2x text-green-500 dark:text-green-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Registros Pagados (Únicos)</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalPaidUnique) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
             {{-- <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3"></div> --}}
        </div>
    </div>

    {{-- 2. Sección Archivos Recientes y Acciones Rápidas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Columna Archivos Recientes --}}
        <div class="lg:col-span-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Archivos Cargados Recientemente</h2>
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                 <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Canasta</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Cargue</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Esquema</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registros</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="relative px-4 py-3"><span class="sr-only">Ver</span></th>
                            </tr>
                        </thead>
                         <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                             @forelse($recentFiles as $file)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $file->idCanasta }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $file->fechaCargue ? $file->fechaCargue->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                         <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-blue-100 text-blue-800' => $file->esquema == 'anterior',
                                            'bg-purple-100 text-purple-800' => $file->esquema == 'nuevo',
                                        ])>
                                            {{ ucfirst($file->esquema) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($file->registros) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                         {{-- Asumiendo estos estados numéricos --}}
                                         @php
                                            $statusText = match($file->estado) {
                                                1 => 'Cargado',
                                                2 => 'Procesando',
                                                3 => 'Validado',
                                                4 => 'Error',
                                                5 => 'Completado',
                                                default => 'Desconocido',
                                            };
                                            $statusColor = match($file->estado) {
                                                1 => 'bg-blue-100 text-blue-800',
                                                2 => 'bg-yellow-100 text-yellow-800 animate-pulse',
                                                3 => 'bg-cyan-100 text-cyan-800',
                                                4 => 'bg-red-100 text-red-800',
                                                5 => 'bg-green-100 text-green-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                         @endphp
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ $statusText }}
                                         </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('files.show', $file->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Ver</a>
                                    </td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No hay archivos recientes.</td>
                                </tr>
                             @endforelse
                         </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Columna Acciones Rápidas --}}
        <div>
             <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Acciones Rápidas</h2>
             <div class="space-y-4">
                {{-- Botón Cargar Archivo (Necesita Ruta) --}}
                <div>
                    <button wire:click="openUploadModal" {{-- Llama al método en Dashboard.php --}}
                        type="button"
                        class="w-full flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-upload fa-fw mr-2"></i> Cargar Nuevo Archivo
                    </button>
                </div>
                {{-- Botón Ver Lista Archivos --}}
                <div>
                     <a href="{{ route('files.index') }}"
                        class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-list mr-2"></i> Ver Lista de Archivos
                    </a>
                </div>
                 {{-- Botón Buscar Facturas --}}
                <div>
                     <a href="{{ route('invoices.search') }}"
                        class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                         <i class="fas fa-search-dollar mr-2"></i> Buscar Facturas
                    </a>
                </div>
                 {{-- Botón Historial Facturas --}}
                 <div>
                     <a href="{{ route('invoices.history') }}"
                        class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-history mr-2"></i> Ver Historial Factura
                    </a>
                </div>
             </div>
        </div>
    </div>

    <livewire:modals.main-file-upload wire:key="dashboard-main-upload" />


     {{-- Puedes añadir más secciones aquí, como gráficos, alertas, etc. --}}

</div>