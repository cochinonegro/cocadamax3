<x-filament-panels::page>
    <div class="tienda-flow mx-auto max-w-5xl">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->subcategorias as $key => $label)
                <button
                    type="button"
                    wire:click="elegirSubcategoria(@js($key))"
                    wire:key="tienda-sub-{{ $key }}"
                    class="tienda-choice-btn tienda-choice-btn--category"
                >
                    <span>{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
