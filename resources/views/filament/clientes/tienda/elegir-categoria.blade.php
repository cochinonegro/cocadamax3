@php
    use App\Filament\Support\TiendaPresentation;
    use App\Filament\Support\TiendaProgramas;
@endphp

<x-filament-panels::page>
    <x-tienda.shell
        :step="2"
        :badge="'Sistema: ' . TiendaProgramas::osLabel((string) $this->os)"
    >
        <p class="tienda-shell__lead">Selecciona una categoría para explorar la tienda.</p>

        <div class="tienda-pill-grid">
            @foreach ($this->categorias as $key => $label)
                @php($meta = TiendaPresentation::categoryMeta($key))

                <button
                    type="button"
                    wire:click="elegirCategoria(@js($key))"
                    wire:key="tienda-cat-{{ $key }}"
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
