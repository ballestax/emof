{{-- resources/views/livewire/file/detail.blade.php --}}
<div class="container mx-auto px-4 sm:px-6 lg:px-4 py-6"> 

    {{-- Detalles del Archivo y Botones Principales --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-lg p-4 mb-4"> 
        <div class="flex flex-wrap items-start justify-between">
            {{-- Información del Archivo --}}
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4 sm:mb-0"> 
                <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('ID Canasta') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $file->idCanasta }}</p>
                </div>
                <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('GUID') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100 break-all">{{ $file->GUID }}</p>
                </div>
                <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('Tipo de Archivo') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $file->tipoArchivo }}</p>
                </div>
                 <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('Esquema') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $file->esquema }}</p>
                </div>
                <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('Registros Declarados') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($file->registros) }}</p>
                </div>
                <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('Fecha de Cargue') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ optional($file->fechaCargue)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('Cargado hace') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ optional($file->fechaCargue)->diffForHumans() ?? 'N/A' }}</p>
                </div>
                 <div>
                    <strong class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('Estado') }}</strong>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                         @switch($file->estado)
                            @case(1) Cargado @break
                            @case(2) Procesando @break
                            @case(3) Validado @break
                            @case(4) Error @break
                            @case(5) Completado @break
                            @default Desconocido ({{ $file->estado }})
                        @endswitch
                    </p>
                </div>
            </div>
            {{-- Botones de Acción --}}
            <div class="flex flex-col justify-start items-stretch space-y-2 w-full sm:w-auto sm:ml-6">
                <a href="{{ route('files.index') }}" class="inline-block w-full text-center px-3 py-1.5 bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-800 text-white font-bold rounded-md text-xs shadow-sm transition duration-150 ease-in-out"> {{-- Reducido px/py y text-sm a text-xs --}}
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Volver') }}
                </a>
                <button
+                   type="button"
+                   wire:click="$dispatch('openEditModal', { fileId: {{ $file->id }} })"
+                   class="inline-block w-full text-center px-3 py-1.5 bg-indigo-500 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-800 text-white font-bold rounded-md text-xs shadow-sm transition duration-150 ease-in-out"
+               >
+                   <i class="fas fa-edit mr-1"></i> {{ __('Editar') }}
+               </button>
                <button
                    wire:click.prevent="delete"
                    wire:confirm="¿Está seguro de que desea eliminar este archivo?\n\n¡ATENCIÓN!\nSe eliminarán TODOS los registros asociados (registers_vX) y TODAS las glosas y pagos asociados a la ID Canasta {{ $file->idCanasta }} (esto podría afectar a otros archivos si comparten la misma ID Canasta)."
                    class="inline-block w-full text-center px-3 py-1.5 bg-red-500 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 text-white font-bold rounded-md text-xs shadow-sm transition duration-150 ease-in-out">
                    <i class="fas fa-trash-alt mr-1"></i> {{ __('Eliminar') }}
                 </button>
            </div>
        </div>
    </div>

    {{-- Botones Cargar Glosas/Pagos --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-lg p-3 mb-4">
        <div class="flex flex-wrap justify-start gap-3">
            @php
                $expectedFileType = ($file->esquema === 'nuevo') ? 'tipo_2' : 'tipo_1';
            @endphp
            <button class="inline-flex items-center px-3 py-1.5 bg-orange-500 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-800 text-white font-bold rounded-md text-xs shadow-sm transition duration-150 ease-in-out" 
                    {{-- Se usa x-data solo para poder usar @click --}}
                    x-data
                     {{-- Despachar evento para abrir el modal, pasando el ID del modal --}}
                    @click="$dispatch('open-modal', { id: 'import-modal-1' })">
                <i class="fas fa-upload mr-1"></i> {{ __('Cargar Glosas') }}
            </button>
            <button class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-800 text-white font-bold rounded-md text-xs shadow-sm transition duration-150 ease-in-out"
                    x-data
                    @click="$dispatch('open-modal', { id: 'import-modal-2' })">
                <i class="fas fa-money-check-alt mr-1"></i> {{ __('Cargar Pagos') }}
            </button>
        </div>
    </div>
    {{-- Fin Botones Cargar --}}

    {{-- === INICIO SECCIÓN DETALLE REGISTROS CON PESTAÑAS === --}}
    <div class="flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-lg p-4">

        {{-- Título y Botón de Versión --}}
        <div class="pb-3 mb-3 border-b border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-center gap-3"> 
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ __('Detalle de Registros') }}</h2> 
            <button wire:click="toggleTableVersion"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-900 text-white font-bold rounded-md text-xs shadow-sm transition duration-150 ease-in-out {{-- Reducido px/py y text-sm a text-xs --}}
                           {{ $activeTab === 'glossed' ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $activeTab === 'glossed' ? 'disabled' : '' }}>
                @if($isFullVersion)
                    <i class="fas fa-compress-alt mr-1"></i> {{ __('Versión Corta') }}
                @else
                    <i class="fas fa-expand-alt mr-1"></i> {{ __('Versión Completa') }}
                @endif
            </button>
        </div>

        {{-- Pestañas de Navegación --}}
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex flex-wrap space-x-4 sm:space-x-6" aria-label="Tabs"> 
                {{-- Pestaña Todos los Registros --}}
                <button wire:click="setTab('all')"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-xs transition duration-150 ease-in-out 
                               {{ $activeTab === 'all' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                        aria-current="{{ $activeTab === 'all' ? 'page' : 'false' }}">
                    Registros ({{ number_format($allCount) }})
                </button>
                {{-- Pestaña Glosados --}}
                <button wire:click="setTab('glossed')"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-xs transition duration-150 ease-in-out 
                               {{ $activeTab === 'glossed' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                       aria-current="{{ $activeTab === 'glossed' ? 'page' : 'false' }}">
                    Glosas ({{ number_format($glossedCount) }})
                </button>
                {{-- Pestaña Pagados --}}
                <button wire:click="setTab('paid')"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-xs transition duration-150 ease-in-out 
                               {{ $activeTab === 'paid' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                        aria-current="{{ $activeTab === 'paid' ? 'page' : 'false' }}">
                    Pagados ({{ number_format($paidCount) }})
                </button>
            </nav>
        </div>

        {{-- Contenido: Tabla --}}
        {{-- Añadimos wire:key aquí si este div envuelve contenido dinámico afectado por setTab --}}
        <div wire:loading.class.delay="opacity-50" class="relative transition-opacity duration-300" wire:key="tab-content-{{ $activeTab }}">
             <div class="min-w-full overflow-x-auto border border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg">

                 {{-- === INICIO TABLAS REGISTROS V1/V2 ('all') === --}}
                 @if($activeTab === 'all')
                     @if($registers && $registers->isNotEmpty())
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-700">
                                 <tr>
                                     @if ($isFullVersion)
                                         @if($fileType == 'nuevo')
                                             @foreach(['Cons.', 'Tipo Documento', 'Número Documento', 'Primer Nombre','Segundo Nombre','Primer Apellido', 'Segundo Apellido', 'Código CUPS', 'Código CUPS2 Anticuerpos', 'Registro Sanitario Prueba', 'Código EPS', 'Nombre EPS', 'Concepto presentacion', 'NIT', 'Nombre', 'Codigo Habilitacion', 'Valor', 'No Factura', 'Fecha Toma', 'Fecha Resultado', 'Resultado', 'ID Examen'] as $header)
                                                 <th scope="col" class="px-2 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __($header) }}</th> {{-- Reducido px --}}
                                             @endforeach
                                         @else
                                             @foreach(['Cons.', 'Tipo Documento', 'Número Documento', 'Primer Nombre','Segundo Nombre','Primer Apellido', 'Segundo Apellido', 'Código CUPS','Código CUPS2 Anticuerpos', 'Registro Sanitario Prueba', 'Código EPS', 'Nombre EPS', 'Compra Masiva', 'Valor Prueba','NIT IPS Tomo Muestra', 'Nombre IPS Tomo Muestra','Codigo Habilitacion IPS', 'Valor Toma Muestra', 'Factura Muestra','NIT Laboratorio','Nombre Laboratorio','Codigo Habilitacion Lab.','Valor Procesamiento', 'Factura Procesamiento','Fecha Toma', 'Resultado Prueba', 'Fecha Resultado', 'Tipo Procedimiento'] as $header)
                                                 <th scope="col" class="px-2 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __($header) }}</th>
                                             @endforeach
                                         @endif
                                     @else
                                         @if($fileType == 'nuevo')
                                             @foreach(['Cons.', 'Doc', 'Número Doc', 'Nombre Completo', 'Factura','Valor', 'Fecha Toma','Fecha Res','Res','IPS'] as $header)
                                                 <th scope="col" class="px-1 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __($header) }}</th> {{-- Reducido px --}}
                                             @endforeach
                                         @else
                                             @foreach(['Cons.', 'Doc', 'Número Doc', 'Nombre Completo', 'IPS Toma','Fact Toma', 'Valor Toma', 'IPS Proc','Fact Proc', 'Valor Proc', 'Fecha Toma', 'Res'] as $header)
                                                 <th scope="col" class="px-1 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __($header) }}</th>
                                             @endforeach
                                         @endif
                                     @endif
                                     {{-- Columna Acción --}}
                                     <th scope="col" class="relative px-1 py-2"><span class="sr-only">Acciones</span></th>
                                 </tr>
                             </thead>
                             <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                 @foreach($registers as $register)
                                     {{-- Añadir wire:key a la fila --}}
                                     <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="register-row-{{ $register->id }}">
                                         @if ($isFullVersion)
                                             @if($fileType == 'nuevo') {{-- Celdas Completas para registers_v2 --}}
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->consecutivo }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->tipo_documento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->numero_documento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->primer_nombre }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->segundo_nombre }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->primer_apellido }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->segundo_apellido }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_cups }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_cups2_anticuerpos }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->registro_sanitario_prueba }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_eps }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre_eps }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->concepto_presentacion }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nit }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_habilitacion }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor ? '$' . number_format((float)$register->valor, 0, ',', '.') : '$0' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->no_factura ?? 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') : 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->resultado_prueba }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->id_examen }}</td>
                                             @else {{-- Celdas Completas para registers_v1 --}}
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->consecutivo }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->tipo_documento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->numero_documento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->primer_nombre }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->segundo_nombre }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->primer_apellido }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->segundo_apellido }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_cups }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_cups2_anticuerpos }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->registro_sanitario_prueba }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_eps }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre_eps }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->compra_masiva }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor_prueba ? '$' . number_format((float)$register->valor_prueba, 0, ',', '.') : '$0' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nit_ips_tomo_muestra }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre_ips_tomo_muestra }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_habilitacion_ips_tomo_muestra }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor_toma_muestra_a_cobrar_adres ? '$' . number_format((float)$register->valor_toma_muestra_a_cobrar_adres, 0, ',', '.') : '$0' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->no_factura_muestra }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nit_laboratorio_procesamiento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre_laboratorio_procesamiento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->codigo_habilitacion_procesamiento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor_procesamiento_a_cobrar_adres ? '$' . number_format((float)$register->valor_procesamiento_a_cobrar_adres, 0, ',', '.') : '$0' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->no_factura_procesamiento }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') :  'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->resultado_prueba }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->tipo_procedimiento }}</td>
                                             @endif
                                         @else {{-- Celdas Cortas --}}
                                             @if($fileType == 'nuevo') {{-- Celdas Cortas para registers_v2 --}}
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->consecutivo }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->tipo_documento }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->numero_documento }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ trim($register->primer_nombre . ' ' . $register->segundo_nombre . ' ' . $register->primer_apellido . ' ' . $register->segundo_apellido) }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->no_factura ?? 'N/A' }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor ? '$' . number_format((float)$register->valor, 0, ',', '.') : 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') : 'N/A' }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->resultado_prueba == 0 ? 'NEG' : ($register->resultado_prueba == 1 ? 'POS' : $register->resultado_prueba) }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre }}</td>
                                             @else {{-- Celdas Cortas para registers_v1 --}}
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->consecutivo }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->tipo_documento }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->numero_documento }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ trim($register->primer_nombre . ' ' . $register->segundo_nombre . ' ' . $register->primer_apellido . ' ' . $register->segundo_apellido) }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre_ips_tomo_muestra ?? '-'}}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->no_factura_muestra ?? '-'}}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor_toma_muestra_a_cobrar_adres ? '$' . number_format((float)$register->valor_toma_muestra_a_cobrar_adres, 0, ',', '.') : '$0' }}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->nombre_laboratorio_procesamiento ?? '-'}}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $register->no_factura_procesamiento ?? '-'}}</td>
                                                 <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $register->valor_procesamiento_a_cobrar_adres ? '$' . number_format((float)$register->valor_procesamiento_a_cobrar_adres, 0, ',', '.') : '$0' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                                                 <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') :  'N/A' }}</td>
                                             @endif
                                         @endif
                                         {{-- Celda Acción --}}
                                         <td class="px-1 py-2 whitespace-nowrap text-center text-xs font-medium">
                                             <button type="button" wire:click.prevent="showRegisterDetails({{ $register->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                 <i class="fas fa-eye"></i> {{-- Icono Ver --}}
                                             </button>
                                         </td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     @else
                         <div class="p-4 text-center text-gray-500 dark:text-gray-400"> 
                             @if($activeTab === 'all')
                                 {{ __('No se encontraron registros asociados a este archivo.') }}
                             @endif
                         </div>
                     @endif

                     @if($registers && $registers->hasPages())
                         <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"> 
                             {{ $registers->links(data: ['scrollTo' => false]) }}
                         </div>
                     @endif
                 @endif
                 {{-- === FIN TABLAS REGISTROS V1/V2 === --}}

                 {{-- === INICIO TABLA GLOSAS ('glossed') === --}}
                 @if($activeTab === 'glossed')
                     @if($glossedData && $glossedData->isNotEmpty())
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-700">
                                 <tr>
                                     @if($fileType == 'nuevo')
                                         @foreach(['Cons.', 'Doc', 'Num. Doc', 'ID Canasta', 'Factura Asoc', 'NIT Prestador', 'Nombre Prestador', 'Valor Reg.', 'Valor Glosado', 'Valor Levantado', 'Valor Aceptado IPS', 'Valor Aceptado EPS', 'Valor Pagado', 'Fecha Pago'] as $header)
                                            <th scope="col" class="px-1 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap" style="word-wrap: break-word; white-space: normal;">{{ __($header) }}</th>
                                         @endforeach
                                     @else
                                         @foreach(['Cons.', 'Doc', 'Num. Doc', 'ID Canasta', 'IPS Toma', 'Fact. Toma', 'Valor Toma', 'IPS Proc', 'Fact. Proc', 'Valor Proc','Val. BDUA Nom/Doc',
                                                'Val. BDUA EPS',                                             'Val. BDUA RENEC',                     'Val. BDUA RENEC Vig',
                                                'Val. BDUA F.Toma/F.Def',                                    'Val. Sism. Doc',                      'Val. Sism. F.Toma',
                                                'Val. Sism. F.Res',                                          'F. Val Sism.',
                                                'Val. Cons. F.Toma',                                         'Val. Cons. F.Toma/Res',
                                                'Val. Cons. Cód Hab',                                        'Val. Cons. NIT Proc',
                                                'Val. Cons. Compra Prueba',                                  'Val. Cons. Prueba ET',
                                                'Val. Cons. Fecha',                                          'Val. Cons. No Pres Ant Pago',
                                                'Val. Cons. Sup Valor',                                      'Val. Cons. NIT Toma/Proc',
                                                'Val. BDUA Cód Mun',                                         'Val. Cons. Dup',
                                                'Val. Cons. NIT/CódHab Toma',                                'Val. Cons. NIT/CódHab Proc',
                                                'Val. Cons. Valor/Tipo Proc',                                'Val. Cons. ID Muestra',
                                                'Val. Cons. PT5 Proc Solo',                                  'Val. Cons. PT5 Toma Solo',
                                                'Val. Cons. PT5 Toma+Proc+Pag',                              'Val. Cons. PT5 Toma+Proc+Pag+Doc',
                                                'Val. Cons. PT5 Cód Hab T/P',                                'Val. Cons. PT5 NIT/CódHab Proc',
                                                'Val. Cons. PT6 Toma Solo',                                  'Val. Cons. PT6 Proc Solo',
                                                'Val. Cons. PT6 Proc+Toma+Pag',                              'Val. Cons. PT6 Proc+Toma+Pag+Doc',
                                                'Val. Cons. PT6 NIT/CódHab Toma',                            'Val. Cons. PT6 Cód Hab T/P',
                                                'Val. Cons. CUPS Ac',                                        'Val. Cons. CUPS2 Ac',
                                                'Val. Cons. CUPS/CUPS2 Ac',                                  'Val. Cons. CUPS/CUPS2 Ac Res',
                                                'Val. Cons. Tipo Pres. ARC2',                                'Val. Cons. Tipo Pres. Toma',            'Val. Cons. Tipo Pres. Proc',
                                                'Val. BDUA Cód EPS'] as $header)
                                            <th scope="col" class="px-1 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap" style="word-wrap: break-word; white-space: normal;">{{ __($header) }}</th>
                                         @endforeach
                                     @endif
                                 </tr>
                             </thead>
                             <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                 @foreach($glossedData as $gloss)
                                     <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="gloss-row-{{ $gloss->id }}">
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->consecutivo }}</td>
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->tipo_documento }}</td>
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->numero_documento }}</td>                                         
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->id_register }}</td> {{-- Esto es idCanasta --}}
                                         @if($fileType == 'nuevo')
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_no_factura ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_nit_prestador ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_nombre_prestador ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $gloss->register_valor ? '$' . number_format((float)$gloss->register_valor, 0, ',', '.') : '$0' }}</td>
                                         @else {{-- Esquema 'anterior' --}}
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_ips_toma ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_no_factura_toma ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $gloss->register_valor_toma ? '$' . number_format((float)$gloss->register_valor_toma, 0, ',', '.') : '$0' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_ips_proc ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->register_no_factura_proc ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $gloss->register_valor_proc ? '$' . number_format((float)$gloss->register_valor_proc, 0, ',', '.') : '$0' }}</td>
                                         @endif
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_bdua_nombres_documento))
                                                <i class="fas {{ $gloss->validado_bdua_nombres_documento ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_bdua_nombres_documento == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_bdua_eps))
                                                <i class="fas {{ $gloss->validado_bdua_eps ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_bdua_eps == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_bdua_renec))
                                                <i class="fas {{ $gloss->validado_bdua_renec ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_bdua_renec == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                       
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_bdua_renec_vigencia))
                                                <i class="fas {{ $gloss->validado_bdua_renec_vigencia ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_bdua_renec_vigencia == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_bdua_ftoma_fdefuncion))
                                                <i class="fas {{ $gloss->validado_bdua_ftoma_fdefuncion ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_bdua_ftoma_fdefuncion == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                       
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_sismuestra_nodoc_tipdoc))
                                                <i class="fas {{ $gloss->validado_sismuestra_nodoc_tipdoc ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_sismuestra_nodoc_tipdoc == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_sismuestra_fecha_toma))
                                                <i class="fas {{ $gloss->validado_sismuestra_fecha_toma ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_sismuestra_fecha_toma == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_sismuestra_fecha_resultado))
                                                <i class="fas {{ $gloss->validado_sismuestra_fecha_resultado ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_sismuestra_fecha_resultado == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->validado_sismuestra_fecha_formatted }}</td>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_fecha_toma))
                                                <i class="fas {{ $gloss->validado_cons_fecha_toma ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_fecha_toma == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_fecha_toma_vs_fecha_resultado))
                                                <i class="fas {{ $gloss->validado_cons_fecha_toma_vs_fecha_resultado ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_fecha_toma_vs_fecha_resultado == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_codigo_habilitacion_toma_pros))
                                                <i class="fas {{ $gloss->validado_cons_codigo_habilitacion_toma_pros ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_codigo_habilitacion_toma_pros == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_nit_ips_procesamiento))
                                                <i class="fas {{ $gloss->validado_cons_nit_ips_procesamiento ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_nit_ips_procesamiento == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_compra_prueba))
                                                <i class="fas {{ $gloss->validado_cons_compra_prueba ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_compra_prueba == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_prueba_et))
                                                <i class="fas {{ $gloss->validado_cons_prueba_et ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_prueba_et == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->validado_cons_fecha_formatted }}</td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_no_presentado_anterior_con_pago))
                                                <i class="fas {{ $gloss->validado_cons_no_presentado_anterior_con_pago ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_no_presentado_anterior_con_pago == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_supera_valor))
                                                <i class="fas {{ $gloss->validado_cons_supera_valor ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_supera_valor == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_nit_toma_vs_nit_proceso))
                                                <i class="fas {{ $gloss->validado_cons_nit_toma_vs_nit_proceso ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_nit_toma_vs_nit_proceso == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $gloss->validado_bdua_codmunicipio_afiliacion }}</td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_duplicado))
                                                <i class="fas {{ $gloss->validado_cons_duplicado ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_duplicado == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_nit_toma_vs_codigohab_toma))
                                                <i class="fas {{ $gloss->validado_cons_nit_toma_vs_codigohab_toma ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_nit_toma_vs_codigohab_toma == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_nit_proc_vs_codigohab_proc))
                                                <i class="fas {{ $gloss->validado_cons_nit_proc_vs_codigohab_proc ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_nit_proc_vs_codigohab_proc == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>                                        
                                        <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                            @if(!is_null($gloss->validado_cons_valor_vs_tipo_procedimiento))
                                                <i class="fas {{ $gloss->validado_cons_valor_vs_tipo_procedimiento ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }}" title="{{ $gloss->validado_cons_valor_vs_tipo_procedimiento == 0 ?  'OK' : 'Error'}}"></i>
                                            @else <span class="text-gray-400">-</span> @endif
                                        </td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     @else
                         <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                             @if($activeTab === 'glossed')
                                 {{ __('No se encontraron glosas para la Canasta ID ') . $file->idCanasta . __('.') }}
                             @endif
                         </div>
                     @endif

                     @if($glossedData && $glossedData->hasPages())
                         <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                             {{ $glossedData->links(data: ['scrollTo' => false]) }}
                         </div>
                     @endif
                 @endif
                 {{-- === FIN TABLA GLOSAS === --}}

                 {{-- === INICIO TABLA PAGOS ('paid') === --}}
                 @if($activeTab === 'paid')
                     @if($paidData && $paidData->isNotEmpty())
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-700">
                                 <tr>
                                     @if($fileType == 'nuevo')
                                         @foreach(['Consecutivo', 'Doc', 'Num. Doc', 'Factura Asoc', 'NIT Prestador', 'Nombre Prestador', 'Valor Reg.', 'Fecha Pago', 'ID Proceso'] as $header)
                                             <th scope="col" class="px-1 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __($header) }}</th>
                                         @endforeach
                                     @else
                                         @foreach(['Consecutivo', 'Doc', 'Num. Doc', 'IPS Toma', 'Fact. Toma', 'Valor Toma', 'IPS Proc', 'Fact. Proc', 'Valor Proc', 'Fecha Pago', 'ID Proceso'] as $header)
                                             <th scope="col" class="px-1 py-2 text-left text-[10px] font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __($header) }}</th>
                                         @endforeach
                                     @endif
                                 </tr>
                             </thead>
                             <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:dividwire-gray-700">
                                 @foreach($paidData as $pay)
                                     <tr class="hover:bg-gray-50 dark:hover:bg-gray-700"  wire:key="pay-row-{{ $pay->id }}">
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->consecutivo }}</td>
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->tipo_documento }}</td>
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->numero_documento }}</td>
                                         @if($fileType == 'nuevo')
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_no_factura ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_nit_prestador ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_nombre_prestador ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $pay->register_valor ? '$' . number_format((float)$pay->register_valor, 0, ',', '.') : '$0' }}</td>
                                         @else {{-- Esquema 'anterior' --}}
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_ips_toma ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_no_factura_toma ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $pay->register_valor_toma ? '$' . number_format((float)$pay->register_valor_toma, 0, ',', '.') : '$0' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_ips_proc ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $pay->register_no_factura_proc ?? '-' }}</td>
                                             <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $pay->register_valor_proc ? '$' . number_format((float)$pay->register_valor_proc, 0, ',', '.') : '$0' }}</td>
                                         @endif
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $pay->fecha_pago ? \Carbon\Carbon::parse($pay->fecha_pago)->format('d/m/Y') : 'N/A' }}</td>
                                         <td class="px-1 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-right">{{ $pay->id_proceso ? $pay->id_proceso : '-' }}</td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     @else
                         <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                             @if($activeTab === 'paid')
                                 {{ __('No se encontraron pagos para la Canasta ID ') . $file->idCanasta . __('.') }}
                             @endif
                         </div>
                     @endif

                     @if($paidData && $paidData->hasPages())
                         <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                             {{ $paidData->links(data: ['scrollTo' => false]) }}
                         </div>
                     @endif
                 @endif
                 {{-- === FIN TABLA PAGOS === --}}

             </div>
        </div>
        {{-- === FIN SECCIÓN DETALLE REGISTROS CON PESTAÑAS === --}}

    {{-- Modales --}}
    {{-- Se pasa el $expectedFileType determinado antes al componente modal --}}
    <div wire:ignore>
        <livewire:modals.file-upload id="import-modal-1" title="Importar Archivo de Glosas" :fileId="$file->id" action-method="importFileGloss" :tipoArchivo="$expectedFileType" />
        <livewire:modals.file-upload id="import-modal-2" title="Importar Archivo de Pagos" :fileId="$file->id" action-method="importFilePays" :tipoArchivo="$expectedFileType" />

        {{-- Modal para detalle de registro individual --}}
        <livewire:modals.register-detail wire:key="register-detail-{{ $selectedRegisterId ?? 'new' }}" />

        <livewire:modals.edit-file wire:key="edit-file-modal-instance" />
    </div>

</div>