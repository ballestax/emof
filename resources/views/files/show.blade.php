<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle Archivo
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-2 bg-white border-b border-gray-200">
                <livewire:file.detail :file="$file" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>