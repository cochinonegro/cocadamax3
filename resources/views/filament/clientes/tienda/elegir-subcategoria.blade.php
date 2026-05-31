@php
    use App\Filament\Support\ProgramaCategories;
    use App\Filament\Support\TiendaPresentation;
    use App\Filament\Support\TiendaProgramas;
@endphp

<x-filament-panels::page>
    <x-tienda.shell
        :step="2"
        :badge="ProgramaCategories::label($this->category) . ' · ' . TiendaProgramas::osLabel((string) $this->os)"
    >
        <p class="tienda-shell__lead">Elige una subcategoría para ver los productos disponibles.</p>

        <div class="tienda-pill-grid tienda-pill-grid--sub">
            @foreach ($this->subcategorias as $key => $label)
                @php($meta = TiendaPresentation::subcategoryMeta((string) $this->category, $key))

                <button
                    type="button"
                    wire:click="elegirSubcategoria(@js($key))"
                    wire:key="tienda-sub-{{ $key }}"
                    class="tienda-pill-btn tienda-pill-btn--{{ $meta['tone'] }}"
                >
                    <span class="tienda-pill-btn__icon">
                        <x-dynamic-component :component="$meta['icon']" class="h-6 w-6" />
                    </span>
                    <span class="tienda-pill-btn__label">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </x-tienda.shell>
</x-filament-panels::page>
