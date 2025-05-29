<div> {{-- Div raíz obligatorio --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                {{-- Fondo --}}
                <div x-data @click="$wire.closeModal()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>

                {{-- Contenedor Centrado --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenido Modal --}}
                <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                    {{-- Cabecera --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                            Cargar Archivo Principal
                        </h3>
                        <button wire:click="closeModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                             <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Cerrar modal</span>
                        </button>
                    </div>

                    {{-- Cuerpo - Formulario --}}
                    <div class="mt-4">
                         @if (session()->has('error'))
                            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-400" role="alert">
                                <span class="font-medium">¡Error!</span> {{ session('error') }}
                            </div>
                         @endif

                         <form wire:submit.prevent="save">
                            <div class="space-y-4">
                                 {{-- Indicador Carga Archivo --}}
                                 <div wire:loading wire:target="file" class="text-sm text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando/Procesando archivo...
                                 </div>

                                 {{-- Campo Archivo --}}
                                <div>
                                     <label for="main_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo*</label>
                                     <input type="file" id="main_file" wire:model="file" required accept=".xlsx,.xls,.csv,.txt"
                                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 @error('file') border-red-500 dark:border-red-400 @enderror">
                                     @error('file') <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Fila ID Canasta y Esquema Detectado --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="main_idCanasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID Canasta*</label>
                                        <input type="number" id="main_idCanasta" wire:model.lazy="idCanasta" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 @error('idCanasta') border-red-500 dark:border-red-400 @enderror">
                                        @error('idCanasta') <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="main_esquema_detected" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Esquema Detectado</label>
                                        <input type="text" id="main_esquema_detected" value="{{ $esquema ? ($esquema == 'nuevo' ? 'Nuevo (Tipo 2)' : 'Anterior (Tipo 1)') : '---' }}" readonly disabled
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 cursor-not-allowed">
                                        @error('esquema') <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Fila Registros (readonly) y Fecha Cargue --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div>
                                        <label for="main_registros" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            No. Registros*
                                            <span wire:loading wire:target="file" class="ml-2 text-xs text-blue-500 dark:text-blue-400">(Contando...)</span>
                                        </label>
                                        <input type="number" id="main_registros" wire:model.lazy="registros" required min="0" readonly
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 cursor-not-allowed @error('registros') border-red-500 dark:border-red-400 @enderror">
                                        @error('registros') <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                     <div>
                                        <label for="main_fechaCargue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Cargue*</label>
                                        <input type="date" id="main_fechaCargue" wire:model.lazy="fechaCargue" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 @error('fechaCargue') border-red-500 dark:border-red-400 @enderror">
                                        @error('fechaCargue') <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                {{-- Estado oculto --}}
                                <input type="hidden" wire:model="estado" value="1">
                            </div>

                            {{-- Pie / Botones --}}
                             <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" wire:click="closeModal"
                                        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                                    Cancelar
                                </button>
                                {{-- Deshabilitar submit si el esquema no fue detectado O si se está cargando --}}
                                <button type="submit"
                                        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                        wire:loading.attr="disabled" wire:target="save, file" @if(empty($esquema)) disabled title="Seleccione un archivo con esquema válido" @endif>
                                     <span wire:loading.remove wire:target="save, file">Guardar Archivo</span>
                                     <span wire:loading wire:target="save, file">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Guardando...
                                     </span>
                                </button>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>