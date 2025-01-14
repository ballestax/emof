<div class="container mx-auto">
    <!-- Detalles del Archivo -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 flex flex-wrap items-start">
        <!-- Contenedor de detalles de archivo, con tres columnas cuando hay suficiente espacio -->
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <div>
                <strong class="text-gray-700">{{ __('ID Canasta') }}:</strong>
                <p class="text-gray-900">{{ $file->idCanasta }}</p>
            </div>
            <div>
                <strong class="text-gray-700">{{ __('GUID') }}:</strong>
                <p class="text-gray-900">{{ $file->GUID }}</p>
            </div>
            <div>
                <strong class="text-gray-700">{{ __('Tipo de Archivo') }}:</strong>
                <p class="text-gray-900">{{ $file->tipoArchivo }}</p>
            </div>
            <div>
                <strong class="text-gray-700">{{ __('Registros') }}:</strong>
                <p class="text-gray-900">{{ $file->registros }}</p>
            </div>
            <div>
                <strong class="text-gray-700">{{ __('Fecha de Cargue') }}:</strong>
                <p class="text-gray-900">{{ $file->fechaCargue->format('d/m/Y') }}</p>
            </div>
            <div>
                <strong class="text-gray-700">{{ __('Cargado hace') }}:</strong>
                <p class="text-gray-900">{{ $file->fechaCargue->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Contenedor de Acciones (botones alineados a la derecha del panel) -->
        <div class="flex flex-col justify-start items-end space-y-4 w-auto ml-6">
            <a href="{{ route('files.index') }}" class="inline-block w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                {{ __('Volver') }}
            </a>
            <a href="{{ route('files.edit', $file) }}" class="inline-block w-full bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-sm">
                {{ __('Editar') }}
            </a>
            <button wire:click.prevent="delete" class="inline-block w-full bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                {{ __('Eliminar') }}
            </button>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 mt-1">
        <div class="flex justify-start space-x-4">
            <button
                class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">
                {{ __('Cargar Glosas') }}
            </button>

            <!-- Botón para abrir el modal de Cargar Pagos -->
            <button
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">
                {{ __('Cargar Pagos') }}
            </button>
        </div>
    </div>

    <!-- Listado de Registros -->
    <div class="flex flex-col mt-6">
        <div class="py-2 flex justify-between items-center">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Listado de Registros') }}</h2>
            <button wire:click="toggleTableVersion" class="flex items-center px-4 py-2 bg-indigo-600 text-white font-bold rounded text-sm">
                @if($isFullVersion)
                <i class="fas fa-columns mr-2"></i> <!-- Ícono de tabla simplificada -->
                {{ __('Versión Corta') }}
                @else
                <i class="fas fa-table mr-2"></i> <!-- Ícono de tabla completa -->
                {{ __('Versión Completa') }}
                @endif
            </button>
        </div>

        <div class="min-w-full border-b border-gray-200 shadow overflow-x-auto">

            @if($registers->isNotEmpty())
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        @if ($isFullVersion)
                        @if($fileType == 'nuevo')
                        @foreach(['Consecutivo', 'Tipo Documento', 'Número Documento', 'Primer Nombre','Segundo Nombre','Primer Apellido', 'Segundo Apellido', 'Código CUPS', 'Código CUPS2 Anticuerpos', 'Registro Sanitario Prueba', 'Código EPS', 'Nombre EPS', 'Concepto presentacion', 'NIT', 'Nombre', 'Codigo Habilitacion', 'Valor', 'No Factura', 'Fecha Toma', 'Fecha Resultado', 'Resultado', 'ID Examen'] as $header)
                        <th class="px-2 py-1 text-left text-gray-500 border-b border-gray-200 bg-gray-50 text-xs break-words">{{ __($header) }}</th>
                        @endforeach
                        @else
                        @foreach(['Consecutivo', 'Tipo Documento', 'Número Documento', 'Primer Nombre','Segundo Nombre','Primer Apellido', 'Segundo Apellido', 'Código CUPS', 'Registro Sanitario Prueba', 'Código EPS', 'Nombre EPS', 'Compra Masiva', 'Valor Prueba','NIT IPS Tomo Muestra', 'Nombre IPS Tomo Muestra','Codigo Habilitacion IPS', 'Valor Toma Muestra', 'Factura Muestra','NIT Laboratorio','Nombre Laboratorio','Codigo Habilitacion Lab.','Valor Procesamiento', 'Factura Procesamiento','Fecha Toma', 'Resultado Prueba', 'Fecha Resultado', 'Tipo Procedimiento'] as $header)
                        <th class="px-2 py-1 text-left text-gray-500 border-b border-gray-200 bg-gray-50 text-xs break-words">{{ __($header) }}</th>
                        @endforeach
                        @endif
                        @else
                        @if($fileType == 'nuevo')
                        @foreach(['Consecutivo', 'Tipo Documento', 'Número Documento', 'Nombre Completo', 'Factura','Valor', 'Fecha Procesamiento','Fecha Resultado','Resultado Prueba', 'IPS'] as $header)
                        <th class="px-1 py-1 text-left text-gray-500 border-b border-gray-200 bg-gray-50 text-xs break-words">{{ __($header) }}</th>
                        @endforeach
                        @else
                        @foreach(['Consecutivo', 'Tipo Documento', 'Número Documento', 'Nombre Completo', 'Factura Toma', 'Valor Toma', 'Factura Procesamiento', 'Valor Procesamiento', 'Fecha Resultado', 'Resultado'] as $header)
                        <th class="px-1 py-1 text-left text-gray-500 border-b border-gray-200 bg-gray-50 text-xs break-words">{{ __($header) }}</th>
                        @endforeach
                        @endif
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($registers as $register)
                    <tr>
                        @if ($isFullVersion)
                        @if($fileType == 'nuevo')
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->consecutivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->numero_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_nombre }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->segundo_nombre }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->segundo_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_cups }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_cups2_anticuerpos }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->registro_sanitario_prueba }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_eps }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre_eps }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->concepto_presentacion }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nit }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_habilitacion }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm text-right">{{ $register->valor ? '$' . number_format($register->valor, 0) : 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->resultado_prueba }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->id_examen }}</td>
                        @else
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->consecutivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->numero_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_nombre }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->segundo_nombre }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->segundo_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_cups }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->registro_sanitario_prueba }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_eps }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre_eps }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->compra_masiva }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm text-right">{{ $register->valor_prueba ? '$' . number_format($register->valor_prueba, 0) : 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nit_ips_tomo_muestra }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre_ips_tomo_muestra }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_habilitacion_ips_tomo_muestra }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm text-right">{{ $register->valor_toma_muestra_a_cobrar_adres ? '$' . number_format($register->valor_toma_muestra_a_cobrar_adres, 0) : 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura_muestra }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nit_laboratorio_procesamiento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre_laboratorio_procesamiento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_habilitacion_procesamiento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm text-right">{{ $register->valor_procesamiento_a_cobrar_adres ? '$' . number_format($register->valor_procesamiento_a_cobrar_adres, 0) : 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura_procesamiento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->resultado_prueba }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_procedimiento }}</td>
                        @endif
                        @else
                        @if($fileType == 'nuevo')
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->consecutivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->numero_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_nombre }} {{ $register->segundo_nombre }} {{ $register->primer_apellido }} {{ $register->segundo_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->valor ? '$' . number_format($register->valor, 0) : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->resultado_prueba == 0 ? 'NEGATIVO':'POSITIVO'}}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre }}</td>

                        @else
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->consecutivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->numero_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_nombre }} {{ $register->segundo_nombre }} {{ $register->primer_apellido }} {{ $register->segundo_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura_muestra }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm text-right">{{ $register->valor_toma_muestra_a_cobrar_adres ? '$' . number_format($register->valor_toma_muestra_a_cobrar_adres, 0) : 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura_procesamiento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm text-right">{{ $register->valor_procesamiento_a_cobrar_adres ? '$' . number_format($register->valor_procesamiento_a_cobrar_adres, 0) : 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->resultado_prueba == 0 ? 'NEGATIVO':'POSITIVO'}}</td>
                        @endif
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @else
            <p class="text-gray-600">{{ __('No se encontraron registros.') }}</p>
            @endif
        </div>
        <!-- Paginación -->
        <div class="px-6 py-4">
            {{ $registers->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
</div>