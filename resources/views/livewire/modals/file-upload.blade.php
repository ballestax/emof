{{-- resources/views/livewire/modals/file-upload.blade.php --}}
<div
    {{-- Alpine.js state --}}
    x-data="{
        open: false,
        uploading: false,
        uploadProgress: 0,
        fileSelected: false,
        fileName: '',
        messageText: '',
        errorText: '',
        showMessages: false,

        // --- init() - Se ejecuta al inicializar Alpine ---
        init() {
            // $watch se ejecuta cuando cambia la propiedad 'open'
            $watch('open', value => {
                if (value) {
                    // Resetear estado al abrir
                    console.log('Modal init triggered for:', @js($id));
                    this.uploading = false;
                    this.uploadProgress = 0;
                    this.fileSelected = false;
                    this.fileName = '';
                    this.messageText = '';
                    this.errorText = '';
                    this.showMessages = false; // Ocultar al inicio

                    // Resetear el input de archivo nativo (si existe la referencia)
                    const fileInput = $refs.fileInput;
                    if (fileInput) {
                         fileInput.value = ''; // Limpia la selección visual
                    } else {
                        console.warn('Alpine init: $refs.fileInput not found on modal open for', @js($id));
                    }

                    // Limpiar mensajes visualmente (si existen las referencias)
                    const messageDiv = $refs.messageDiv;
                    const errorDiv = $refs.errorDiv;
                    if(messageDiv) messageDiv.innerText = ''; else console.warn('Alpine init: $refs.messageDiv not found for', @js($id));
                    if(errorDiv) errorDiv.innerText = ''; else console.warn('Alpine init: $refs.errorDiv not found for', @js($id));

                    // Llamar al método Livewire para resetear validación (si existe $wire)
                    if (typeof $wire !== 'undefined') {
                         // Llama al método público renombrado 'clearMyValidation'
                        $wire.call('clearMyValidation').catch(error => console.error('Livewire clearMyValidation call failed:', error));
                    } else {
                         console.warn('Alpine init: $wire not available yet for clearMyValidation on modal', @js($id));
                    }
                }
            }); // Fin $watch('open')

            // --- Listeners para eventos Livewire sobre el input ---
            // Usar $nextTick para asegurar que $refs esté disponible
            this.$nextTick(() => {
                const fileInput = $refs.fileInput;
                if (fileInput) {
                    fileInput.addEventListener('livewire-upload-start', () => {
                        console.log('Alpine listener: livewire-upload-start');
                        this.uploading = true; this.uploadProgress = 0; this.showMessages = false; this.messageText = ''; this.errorText = '';
                    });
                    fileInput.addEventListener('livewire-upload-finish', () => {
                         console.log('Alpine listener: livewire-upload-finish');
                        this.uploading = false; this.uploadProgress = 100;
                        // Esperar un poco para obtener mensajes actualizados desde Livewire
                        setTimeout(() => {
                            if (typeof $wire !== 'undefined') {
                                this.messageText = $wire.get('sessionMessage');
                                this.errorText = $wire.get('sessionError');
                                if (this.messageText || this.errorText) this.showMessages = true;
                                else this.showMessages = false;
                            } else {
                                console.warn('Alpine finish: $wire not available for getting messages');
                            }
                        }, 300);
                    });
                    fileInput.addEventListener('livewire-upload-error', (e) => {
                        console.error('Alpine listener: livewire-upload-error', e);
                        this.uploading = false; this.uploadProgress = 0;
                         setTimeout(() => {
                             if (typeof $wire !== 'undefined') {
                                 this.errorText = $wire.get('sessionError') || 'Error durante la carga.';
                             } else {
                                 this.errorText = 'Error durante la carga.';
                             }
                            this.messageText = '';
                            this.showMessages = true;
                         }, 300);
                    });
                    fileInput.addEventListener('livewire-upload-progress', (event) => {
                        // Validar que event.detail.progress existe y es un número
                        if (event.detail && typeof event.detail.progress === 'number') {
                            this.uploadProgress = event.detail.progress;
                        } else {
                             console.warn('Alpine listener: livewire-upload-progress event missing or invalid detail.progress');
                        }
                    });
                } else {
                    console.error('Alpine init ($nextTick): $refs.fileInput not found for modal', @js($id));
                }
            }); // Fin $nextTick

        } // --- Fin init() ---

    }" {{-- FIN ATRIBUTO x-data --}}

    {{-- Atributos para controlar visibilidad y cierre del modal --}}
    x-on:open-modal.window="if ($event.detail.id === @js($id)) { console.log('Opening modal:', @js($id)); open = true; }"
    x-on:close-modal.window="
        const modalId = @js($id);
        let receivedId = undefined;
        const detail = $event.detail;
        const isFromBackend = Array.isArray(detail) && detail.length > 0;
        const isFromFrontend = typeof detail === 'object' && detail !== null && detail.hasOwnProperty('id');

        if (isFromBackend) receivedId = detail[0];
        else if (isFromFrontend) receivedId = detail.id;

        if (receivedId === modalId) {
            const delay = isFromBackend ? 1500 : 0;
            console.log(`Closing modal ${modalId} with delay ${delay}ms`);
            if(delay > 0) {
                setTimeout(() => { open = false; }, delay);
            } else {
                open = false; // Cerrar inmediatamente (Cancelar o error)
            }
        }
    "
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4 py-6"
    style="display: none;"
    wire:ignore.self
    wire:key="file-upload-modal-{{ $id }}" {{-- Clave Estable --}}
>
    {{-- Contenedor del Modal --}}
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 overflow-hidden"> {{-- Eliminado @click.outside --}}

        <h2 class="text-lg font-bold mb-4">{{ __($title) }}</h2>

        {{-- Formulario --}}
        <form wire:submit.prevent="{{ $actionMethod }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="{{ $id }}-file" class="block text-sm font-medium text-gray-700">{{ __('Selecciona un archivo') }}</label>
                <input
                    type="file"
                    id="{{ $id }}-file"
                    x-ref="fileInput"
                    @change="fileSelected = $event.target.files.length > 0; fileName = fileSelected ? $event.target.files[0].name : ''; uploading = false; uploadProgress = 0; showMessages = false;"
                    wire:model="file1"
                    accept=".csv,.xlsx,.txt"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                    :disabled="uploading"
                    wire:key="file-input-{{ $id }}"
                    >

                <template x-if="fileSelected && fileName">
                    <p class="text-xs text-gray-600 mt-1 truncate" x-text="'Archivo: ' + fileName"></p>
                </template>

                @error('file1') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror

                <div x-show="uploading" x-transition class="mt-2">
                     <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 overflow-hidden">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-150"
                             :style="`width: ${uploadProgress}%`">
                        </div>
                     </div>
                     <p class="text-xs text-center text-gray-600 mt-1"><span x-text="uploadProgress"></span>%</p>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-end space-x-4">
                 <button
                    type="button"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                    @click="$dispatch('close-modal', { id: @js($id) })"
                    aria-label="{{ __('Cancelar') }}"
                    :disabled="uploading"
                    >
                    {{ __('Cancelar') }}
                </button>

                {{-- BOTÓN IMPORTAR CON LÓGICA CORREGIDA --}}
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!fileSelected || uploading" {{-- Deshabilitar si no hay archivo o se está cargando (Alpine) --}}
                    wire:loading.attr="disabled" {{-- Deshabilitar durante procesamiento Livewire --}}
                    wire:target="{{ $actionMethod }}, file1" {{-- Target para wire:loading/* --}}
                >
                    {{-- Texto "Importar" - Se oculta si 'uploading' es true o si wire:loading está activo --}}
                    <span x-show="!uploading" wire:loading.remove wire:target="{{ $actionMethod }}">
                         {{ __('Importar') }}
                    </span>

                    {{-- Texto "Cargando..." - Se muestra si 'uploading' es true Y wire:loading NO está activo --}}
                    <span x-show="uploading" wire:loading.remove wire:target="{{ $actionMethod }}">
                        {{ __('Cargando...') }}
                    </span>

                    {{-- Texto "Procesando..." + Spinner - Se muestra SÓLO si wire:loading está activo --}}
                    <span wire:loading wire:target="{{ $actionMethod }}">
                         <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('Procesando...') }}
                    </span>
                </button>
                 {{-- FIN BOTÓN IMPORTAR --}}

            </div>
        </form>

        {{-- Mensajes de Sesión/Flash --}}
        <div class="mt-4 h-5 text-sm" {{-- Altura fija mínima --}}
             x-show="showMessages"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             >
            <div x-ref="messageDiv" class="text-green-600 font-medium" x-text="messageText" x-show="messageText"></div>
            <div x-ref="errorDiv" class="text-red-600 font-medium" x-text="errorText" x-show="errorText"></div>
        </div>

    </div> {{-- Fin Contenedor del Modal --}}
</div> {{-- Fin Div Raíz --}}