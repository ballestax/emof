<div>
    <h4 class="mb-4 text-2xl font-bold">Archivos </h4>
    <div>
        <div class="container mx-auto">
            <form method="POST" wire:submit.prevent="storeFile" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="file">Archivo</label>
                        <input type="file" wire:model="file" class="w-full py-2 rounded">
                        @error('file')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="idCanasta">ID Canasta</label>
                        <input type="text" wire:model.lazy="idCanasta" class="w-full py-2 rounded">
                        @error('idCanasta')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="esquema">Esquema</label>
                        <select wire:model.lazy="esquema" class="w-full py-2 rounded">
                            <option value="">Seleccione un esquema</option>
                            <option value="nuevo">Esquema nuevo</option>
                            <option value="anterior">Esquema anterior</option>
                        </select>
                        @error('esquema')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="estado">Estado</label>
                        <select wire:model.lazy="estado" class="w-full py-2 rounded">
                            <option value="">Seleccione un estado</option>
                            <option value="Cargado">Cargado</option>
                            <option value="Procesado">Procesado</option>
                            <option value="Procesado - En tramite">Procesado - En tramite</option>
                        </select>
                        @error('estado')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="fechaCargue">Fecha de Cargue</label>
                        <input type="date" wire:model.lazy="fechaCargue" class="w-full py-2 rounded">
                        @error('fechaCargue')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="registros">Número de Registros</label>
                        <input type="text" wire:model.lazy="registros" class="w-full py-2 rounded">
                        @error('registros')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @if($fileId)
                    <input type="hidden" wire:model="fileId" value="{{ $fileId }}">
                @endif

                <div class="flex justify-end mt-4">
                    <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded">
                        Submit
                    </button>
                    <button type="button" wire:click="update" class="px-4 py-2 ml-2 text-white bg-indigo-600 rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
        <div class="flex flex-col mt-8">
            <div class="py-2">
                <div class="min-w-full border-b border-gray-200 shadow">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                                    GUID
                                </th>
                                <th class="px-6 py-3 text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                                    Tipo Archivo
                                </th>
                                <th class="px-6 py-3 text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                                    Registros
                                </th>
                                <th class="px-6 py-3 text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                                    Fecha Cargue
                                </th>
                                <th class="px-6 py-3 text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                                    Estado
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            @foreach($files as $file)
                            <tr>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $file->idCanasta }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        {{ $file->GUID }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        {{ $file->tipoArchivo }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        {{ $file->registros }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        {{ $file->fechaCargue }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        {{ $file->estado }}
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $files->links() }}
            </div>
        </div>
    </div>
</div>