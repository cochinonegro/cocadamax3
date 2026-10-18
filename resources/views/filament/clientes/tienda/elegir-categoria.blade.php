@php
    use App\Filament\Support\TiendaPresentation;
    use App\Filament\Support\TiendaProgramas;
@endphp

<x-filament-panels::page>
    <x-tienda.shell
        :step="2"
        :badge="'Sistema: ' . TiendaProgramas::osLabel((string) $this->os)"
    >
        <section class="tienda-busqueda" aria-labelledby="tienda-busqueda-titulo">
            <header class="tienda-busqueda__header">
                <h2 id="tienda-busqueda-titulo" class="tienda-busqueda__title">
                    BÚSQUEDA DE PROGRAMA
                </h2>
            </header>

            <div
                class="tienda-accordion tienda-accordion--amber"
                x-data="{ open: false }"
            >
                <button
                    type="button"
                    class="tienda-accordion__trigger"
                    x-on:click="open = ! open"
                    x-bind:aria-expanded="open.toString()"
                >
                    <span class="tienda-accordion__icon">
                        <x-heroicon-o-folder-open class="h-5 w-5" />
                    </span>
                    <span class="tienda-accordion__label">Ver categorías de Programas</span>
                    <span
                        class="tienda-accordion__chevron"
                        x-bind:class="open && 'is-open'"
                        aria-hidden="true"
                    >▼</span>
                </button>

                <div
                    class="tienda-accordion__panel"
                    x-show="open"
                    x-cloak
                    style="display: none;"
                >
                    <div class="tienda-pill-grid tienda-accordion__subs">
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
                </div>
            </div>
        </section>
    </x-tienda.shell>
</x-filament-panels::page>
