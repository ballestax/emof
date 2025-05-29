<div
    x-data="{ open: @entangle('showModal').live }"
    x-show="open"
    x-on:keydown.escape.window="open = false"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4 py-6"
    style="display: none;"
    wire:ignore.self
>
    {{-- Contenedor del Modal --}}
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-lg shadow-lg p-6 overflow-hidden" @click.outside="open = false">

        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            {{ __('Editar Archivo') }}
            @if($fileInstance)
                #<span class="font-mono">{{ $fileInstance->id }}</span>
            @endif
        </h2>

        @if($fileInstance)
        <form wire:submit.prevent="updateFile">
            @csrf

            {{-- idCanasta Field --}}
            <div class="mb-4">
                <label for="edit-idCanasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ID Canasta') }}</label>
                <input type="text" wire:model.defer="idCanasta" id="edit-idCanasta"
                       class="mt-1 block w-full border {{ $errors->has('idCanasta') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                @error('idCanasta') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Esquema Field --}}
            <div class="mb-6">
                <label for="edit-esquema" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Esquema') }}</label>
                <select wire:model.defer="esquema" id="edit-esquema"
                        class="mt-1 block w-full border {{ $errors->has('esquema') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option value="">{{ __('Seleccione...') }}</option>
                    <option value="nuevo">{{ __('Nuevo') }}</option>
                    <option value="anterior">{{ __('Anterior') }}</option>
                </select>
                @error('esquema') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- fechaCargue Field --}}
            <div class="mb-4">
                <label for="edit-fechaCargue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Fecha Cargue') }}</label>
                <input type="date" wire:model.defer="fechaCargue" id="edit-fechaCargue"
                       class="mt-1 block w-full border {{ $errors->has('fechaCargue') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                @error('fechaCargue') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            
            {{-- Aquí puedes mostrar otros campos no editables si lo deseas, tomándolos de $fileInstance --}}
            {{-- Ejemplo:
            <div class="mb-4">
                <strong class="block text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">{{ __('GUID') }}</strong>
                <p class="text-sm text-gray-900 dark:text-gray-100 break-all">{{ $fileInstance->GUID ?? 'N/A' }}</p>
            </div>
            --}}

            {{-- Botones de Acción --}}
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm"
                    @click="open = false" {{-- También podrías llamar a wire:click="closeModal" si prefieres resetear desde el backend al cancelar --}}
                    wire:click="closeModal"
                    aria-label="{{ __('Cancelar') }}">
                    {{ __('Cancelar') }}
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded shadow-sm disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="updateFile">
                    <span wire:loading.remove wire:target="updateFile">
                        <i class="fas fa-save mr-2"></i> {{ __('Guardar Cambios') }}
                    </span>
                    <span wire:loading wire:target="updateFile">
                         <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('Guardando...') }}
                    </span>
                </button>
            </div>
        </form>
        @else
            <p class="text-red-500">{{ __('No se ha podido cargar la información del archivo para editar.') }}</p>
             <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                 <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" @click="open = false" wire:click="closeModal">
                     {{ __('Cerrar') }}
                 </button>
            </div>
        @endif
    </div> {{-- Fin Contenedor del Modal --}}
</div> {{-- Fin Div Raíz --}}