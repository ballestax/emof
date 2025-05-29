<div> {{-- Raíz --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Fondo --}}
            <div x-data @click="$wire.closeModal()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenido Modal --}}
            <div x-data="{ showModal: @entangle('showModal') }" x-show="showModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg"> {{-- Aumentado max-w- a 5xl para más espacio --}}

                {{-- Cabecera --}}
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                     <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                        Detalle del Registro (ID: {{ $registerId ?? 'N/A' }})
                    </h3>
                    <button wire:click="closeModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        {{-- SVG Icono Cerrar --}}
                         <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>

                {{-- Cuerpo del Modal --}}
                <div class="mt-4" x-data="{ expanded: false }">
                    @if($registerData)
                        @php
                            $isV2 = 1;
                            $isV1 = isset($registerData->no_factura_muestra);
                            $nombreCompleto = trim(($registerData->primer_nombre ?? '') . ' ' . ($registerData->segundo_nombre ?? '') . ' ' . ($registerData->primer_apellido ?? '') . ' ' . ($registerData->segundo_apellido ?? ''));
                        @endphp

                        {{-- === SECCIÓN RESUMEN (Siempre visible) === --}}
                        <section class="mb-4">
                            <div class="flex justify-between items-center mb-3 border-b pb-2">
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b pb-2">Resumen</h4>
                                @if($status)
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-s font-medium',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $status === 'Pagado',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $status === 'Glosado',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' => $status === 'Pendiente',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100' => $status === 'Error',
                                    ])>
                                        {{ $status }}
                                    </span>
                                @endif                            
                            </div>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-4">
                                <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Consecutivo</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->consecutivo ?? 'N/A' }}</dd></div>
                                <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Documento</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->tipo_documento ?? 'N/A' }} - {{ $registerData->numero_documento ?? 'N/A' }}</dd></div>
                                <div class="sm:col-span-2"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $nombreCompleto ?: 'N/A' }}</dd></div> {{-- Nombre ocupa 2 cols --}}

                                @if($isV2)
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Factura</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->no_factura ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIT Prestador</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nit ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Resultado</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->resultado_prueba ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($registerData->valor) ? '$' . number_format($registerData->valor, 0, ',', '.') : 'N/A' }}</dd></div>
                                @elseif($isV1)
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Factura Muestra</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->no_factura_muestra ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Factura Proc.</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->no_factura_procesamiento ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Resultado</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->resultado_prueba ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Total</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($registerData->valor_prueba) ? '$' . number_format($registerData->valor_prueba, 0, ',', '.') : 'N/A' }}</dd></div>
                                @endif
                            </dl>
                        </section>
                        {{-- === FIN SECCIÓN RESUMEN === --}}

                        {{-- === CONTENEDOR PARA DETALLES (Colapsable) === --}}
                        <div x-show="expanded" x-collapse
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="space-y-6 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">

                            {{-- SECCIÓN 1 DETALLADA: Identificación y Datos Personales --}}
                            <section>
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Identificación y Datos Personales (Detalle)</h4>                               
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-4">                                    
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo Documento</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->tipo_documento ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-3"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Número Documento</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->numero_documento ?? 'N/A' }}</dd></div> {{-- Documento ocupa 2 --}}
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Primer Nombre</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->primer_nombre ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Segundo Nombre</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->segundo_nombre ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Primer Apellido</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->primer_apellido ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Segundo Apellido</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->segundo_apellido ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código EPS</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->codigo_eps ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-3"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre EPS</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nombre_eps ?? 'N/A' }}</dd></div> {{-- Nombre EPS ocupa 3 --}}
                                </dl>
                            </section>

                            {{-- SECCIÓN 2 DETALLADA: Datos de la Factura / Prestador --}}
                            <section>
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Datos de Factura y Prestador (Detalle)</h4>
                                @if($isV2)
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-4">
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No. Factura</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->no_factura ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($registerData->valor) ? '$' . number_format($registerData->valor, 0, ',', '.') : 'N/A' }}</dd></div>
                                    <div class="sm:col-span-2"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Concepto Presentación</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->concepto_presentacion ?? 'N/A' }}</dd></div> {{-- Concepto ocupa 2 --}}
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIT Prestador</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nit ?? 'N/A' }}</dd></div>
                                    <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código Habilitación</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->codigo_habilitacion ?? 'N/A' }}</dd></div>    
                                    <div class="sm:col-span-2"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre Prestador</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nombre ?? 'N/A' }}</dd></div> {{-- Nombre prestador ocupa 2 --}}
                                    
                                </dl>
                                @elseif($isV1)
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-4">
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Prueba Total</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($registerData->valor_prueba) ? '$' . number_format($registerData->valor_prueba, 0, ',', '.') : 'N/A' }}</dd></div>
                                     {{-- Datos Toma Muestra --}}
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Factura Muestra</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->no_factura_muestra ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Muestra</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($registerData->valor_toma_muestra_a_cobrar_adres) ? '$' . number_format($registerData->valor_toma_muestra_a_cobrar_adres, 0, ',', '.') : 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIT IPS Muestra</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nit_ips_tomo_muestra ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-2"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre IPS Muestra</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nombre_ips_tomo_muestra ?? 'N/A' }}</dd></div> {{-- Nombre IPS ocupa 2 --}}
                                     <div class="sm:col-span-2"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cód. Hab. IPS Muestra</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->codigo_habilitacion_ips_tomo_muestra ?? 'N/A' }}</dd></div> {{-- Cod hab IPS ocupa 2 --}}

                                     {{-- Datos Procesamiento --}}
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Factura Proc.</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->no_factura_procesamiento ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Proc.</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($registerData->valor_procesamiento_a_cobrar_adres) ? '$' . number_format($registerData->valor_procesamiento_a_cobrar_adres, 0, ',', '.') : 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIT Lab. Proc.</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nit_laboratorio_procesamiento ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre Lab. Proc.</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->nombre_laboratorio_procesamiento ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-4"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cód. Hab. Lab. Proc.</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->codigo_habilitacion_procesamiento ?? 'N/A' }}</dd></div> {{-- Cod hab Lab ocupa 4 --}}
                                </dl>
                                @else
                                     <p class="text-sm text-gray-500 dark:text-gray-400">No hay datos de factura específicos para mostrar.</p>
                                @endif
                            </section>

                            {{-- SECCIÓN 3 DETALLADA: Datos de la Muestra y Resultado --}}
                             <section>
                                 <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Datos de Muestra y Resultado (Detalle)</h4>
                                 {{-- Cambiado a 4 columnas --}}
                                 <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-4">
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código CUPS</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->codigo_cups ?? 'N/A' }}</dd></div>
                                     @if($isV2)<div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CUPS 2 Anticuerpos</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->codigo_cups2_anticuerpos ?? 'N/A' }}</dd></div>@endif
                                     <div class="sm:col-span-{{ $isV2 ? '2' : '3' }}"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reg. Sanitario Prueba</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->registro_sanitario_prueba ?? 'N/A' }}</dd></div> {{-- Reg San ocupa 2 o 3 --}}
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Toma</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->fecha_toma ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Resultado</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->fecha_resultado ?? 'N/A' }}</dd></div>
                                     <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Resultado Prueba</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->resultado_prueba ?? 'N/A' }}</dd></div>
                                     @if($isV2)
                                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID Examen</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->id_examen ?? 'N/A' }}</dd></div>
                                     @elseif($isV1)
                                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo Procedimiento</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->tipo_procedimiento ?? 'N/A' }}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Compra Masiva</dt><dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $registerData->compra_masiva ?? 'N/A' }}</dd></div>
                                     @endif
                                 </dl>
                            </section>                            

                        </div>
                         {{-- === FIN CONTENEDOR DETALLES === --}}

                         {{-- === BOTÓN PARA EXPANDIR/CONTRAER === --}}
                         <div class="mt-4 text-center border-t border-gray-200 dark:border-gray-700 pt-3">
                            <button @click="expanded = !expanded" type="button" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 focus:outline-none focus:underline">
                                <span x-show="!expanded">Mostrar Detalles Completos <i class="fas fa-chevron-down fa-fw ml-1"></i></span>
                                <span x-show="expanded">Ocultar Detalles <i class="fas fa-chevron-up fa-fw ml-1"></i></span>
                            </button>
                         </div>
                         {{-- === FIN BOTÓN === --}}

                    @elseif($registerId)
                        {{-- ... (código de carga) ... --}}
                    @else
                        {{-- ... (código de error) ... --}}
                    @endif
                </div> {{-- Cierra el div x-data --}}

                {{-- Pie del Modal --}}
                <div class="mt-6 flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                     <button wire:click="closeModal" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>