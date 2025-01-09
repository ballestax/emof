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
                        @foreach(['Consecutivo', 'Tipo Documento', 'Número Documento', 'Nombre Completo', 'Código CUPS', 'Código CUPS2 Anticuerpos', 'Registro Sanitario Prueba', 'Código EPS', 'Nombre EPS', 'Compra Masiva', 'Valor Prueba', 'Valor Toma Muestra', 'Valor Procesamiento', 'Fecha Toma', 'Resultado Prueba', 'Fecha Resultado', 'Tipo de Archivo', 'Archivo Asociado'] as $header)
                        <th class="px-2 py-1 text-left text-gray-500 border-b border-gray-200 bg-gray-50 text-sm">{{ __($header) }}</th>
                        @endforeach
                        @else
                        @foreach(['Consecutivo', 'Tipo Documento', 'Número Documento', 'Nombre Completo', 'Factura Toma','Valor Toma', 'Factura Procesamiento','Valor Procesamiento','Fecha Toma', 'Resultado Prueba'] as $header)
                        <th class="px-2 py-1 text-left text-gray-500 border-b border-gray-200 bg-gray-50 text-sm">{{ __($header) }}</th>
                        @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white">

                    @foreach($registers as $register)
                    <tr>
                        @if ($isFullVersion)
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->consecutivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->numero_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_nombre }} {{ $register->segundo_nombre }} {{ $register->primer_apellido }} {{ $register->segundo_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_cups }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_cups2_anticuerpos ?? 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->registro_sanitario_prueba ?? 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->codigo_eps }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->nombre_eps }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->compra_masiva ? 'Sí' : 'No' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->valor_prueba ? '$' . number_format($register->valor_prueba, 2) : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->valor_toma_muestra_a_cobrar_adres ? '$' . number_format($register->valor_toma_muestra_a_cobrar_adres, 2) : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->valor_procesamiento_a_cobrar_adres ? '$' . number_format($register->valor_procesamiento_a_cobrar_adres, 2) : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->resultado_prueba }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_resultado ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_resultado)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_archivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->file ? $register->file->nombre : 'N/A' }}</td>
                        @else
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->consecutivo }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->tipo_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->numero_documento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->primer_nombre }} {{ $register->segundo_nombre }} {{ $register->primer_apellido }} {{ $register->segundo_apellido }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura_muestra }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->valor_toma_muestra_a_cobrar_adres ? '$' . number_format($register->valor_toma_muestra_a_cobrar_adres, 2) : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->no_factura_procesamiento }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->valor_procesamiento_a_cobrar_adres ? '$' . number_format($register->valor_procesamiento_a_cobrar_adres, 2) : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->fecha_toma ? \Carbon\Carbon::createFromFormat('d/m/Y', $register->fecha_toma)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-sm">{{ $register->resultado_prueba }}</td>
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