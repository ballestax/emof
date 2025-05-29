@props(['value'])

@if(is_null($value))
    {{-- Caso 1: El valor es NULL (no aplica o no validado) --}}
    <span class="text-gray-400">-</span>
@else
    {{-- Caso 2: El valor NO es NULL (es 0 o 1 u otro) --}}
    <i @class([
            'fas',
            'fa-times-circle text-red-500' => $value === 0,  // Error si es 0
            'fa-check-circle text-green-500' => $value === 1, // OK si es 1
            'fa-question-circle text-yellow-500' => !in_array($value, [0, 1], true) // Opcional: Icono diferente si es un valor inesperado
        ])
       {{-- Ajustamos el título para que coincida con la nueva lógica --}}
       title="{{ $value === 1 ? 'OK (Validado)' : ($value === 0 ? 'Error (No Válido)' : 'Valor inesperado (' . $value . ')') }}">
    </i>
@endif