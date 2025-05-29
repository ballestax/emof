<div> {{-- Div raíz --}}
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-2xl font-bold dark:text-gray-100">Archivos Cargados</h4>
        {{-- BOTÓN PARA ABRIR EL NUEVO MODAL --}}
        <button wire:click="openUploadModal" type="button" class="btn btn-primary"> {{-- Botón maryUI --}}
           <i class="fas fa-plus mr-2"></i> Cargar Archivo
        </button>
    </div>

    {{-- Mensajes Flash --}}
     @if (session()->has('message')) <div class="alert alert-success mb-4">{{ session('message') }}</div> @endif
     @if (session()->has('error')) <div class="alert alert-error mb-4">{{ session('error') }}</div> @endif

    {{-- QUITAR EL <form> ... </form> QUE ESTABA AQUÍ --}}

    {{-- MANTENER LA LISTA/TABLA DE ARCHIVOS --}}
    <div class="flex flex-col mt-6">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                 <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    {{-- ... thead y tbody de la lista como lo tenías ... --}}
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Canasta</th>
                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre Archivo</th>
                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Esquema</th>
                             <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registros</th>
                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Cargue</th>
                             <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Glosas/Pagos</th>                   
                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($files as $file)
                           <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $file->idCanasta }}</td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $file->GUID }}</td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $file->tipoArchivo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                     <span @class([/* Clases badge esquema */ 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full', 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' => $file->esquema == 'anterior', 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' => $file->esquema == 'nuevo',])>
                                        {{ ucfirst($file->esquema) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($file->registros) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $file->fechaCargue?->format('d/m/Y') ?? 'N/A' }}</td>                                

                                {{-- Combined Cell for Glosas / Pagos Status --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($file->gloss_count == 0 && $file->pay_count == 0)
                                        {{-- Orange badge for Pending state --}}
                                        <span class="px-2 inline-flex items-center leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 text-xs">
                                            <i class="fas fa-clock mr-1"></i> {{-- Icon for Pending --}}
                                            Pendiente
                                        </span>
                                    @elseif($file->gloss_count > 0 && $file->pay_count > 0)
                                        {{-- Blue badge for Both Loaded state, with colored counts --}}
                                        <span class="px-2 inline-flex items-center leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs" title="Glosas y Pagos Cargados">
                                            <i class="fas fa-check-double mr-1"></i> {{-- Icon for Both Loaded --}}
                                            G: <span class="text-red-800 dark:text-red-200 font-bold">{{ $file->gloss_count }}</span> / P: <span class="text-green-800 dark:text-green-200 font-bold">{{ $file->pay_count }}</span>
                                        </span>
                                    @elseif($file->gloss_count > 0)
                                        {{-- Red badge for Glosas Only state --}}
                                        <span class="px-2 inline-flex items-center leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs" title="Glosas Cargadas">
                                            <i class="fas fa-comment-dollar mr-1"></i> {{-- Icon for Glossed --}}
                                            {{ $file->gloss_count }}
                                        </span>
                                    @elseif($file->pay_count > 0)
                                        {{-- Green badge for Pays Only state --}}
                                        <span class="px-2 inline-flex items-center leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs" title="Pagos Cargados">
                                            <i class="fas fa-check-circle mr-1"></i> {{-- Icon for Paid --}}
                                            {{ $file->pay_count }}
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-3">
                                        {{-- Botón Ver --}}
                                        {{-- Ensure the route is correct for your detail page, likely 'files.show' or similar --}}
                                        <a href="{{ route('files.show', $file) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                         {{-- Botón Editar (si implementaste la lógica) --}}
                                         {{-- <button wire:click="edit({{ $file->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button> --}}
                                         {{-- Botón Eliminar (si implementaste la lógica) --}}
                                        {{-- <button wire:click="confirmDelete({{ $file->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button> --}}
                                    </div>
                                </td>
                            </tr>
                            @empty
                             <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"> {{-- Updated colspan --}}
                                    No hay archivos cargados.
                                </td>
                             </tr>                            
                            @endforelse
                        </tbody>
                 </table>
            </div>
             {{-- Paginación --}}
            <div class="mt-4">
                 {{ $files->links() }}
            </div>
        </div>
    </div>

    {{-- AÑADIR EL NUEVO MODAL DE CARGA PRINCIPAL --}}
    <livewire:modals.main-file-upload wire:key="main-file-upload-instance" />

</div>