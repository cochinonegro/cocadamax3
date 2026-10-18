@php
    use App\Filament\Support\TiendaPresentation;
    use App\Filament\Support\TiendaProgramas;
@endphp

<x-filament-panels::page>
    <x-tienda.shell
        :step="2"
        :badge="'Sistema: ' . TiendaProgramas::osLabel((string) $this->os)"
    >
        <section class="invitado-categorias-box" aria-label="Categorías de programas">
            <div
                class="invitado-categorias-accordion"
                x-data="{ open: false }"
            >
                <button
                    type="button"
                    class="invitado-categorias-accordion__trigger"
                    x-on:click="open = ! open"
                    x-bind:aria-expanded="open.toString()"
                >
                    <span class="invitado-categorias-accordion__label">Ver Categorías de programas</span>
                    <span
                        class="invitado-categorias-accordion__chevron"
                        x-bind:class="open && 'is-open'"
                        aria-hidden="true"
                    >▼</span>
                </button>

                <div
                    class="invitado-categorias-accordion__panel"
                    x-show="open"
                    x-cloak
                    style="display: none;"
                >
                    <div class="invitado-categorias-grid">
                        @foreach ($this->categorias as $key => $label)
                            @php($meta = TiendaPresentation::categoryMeta($key))

                            <button
                                type="button"
                                wire:click="elegirCategoria(@js($key))"
                                wire:key="tienda-cat-{{ $key }}"
                                class="invitado-categorias-grid__item tienda-pill-btn--{{ $meta['tone'] }}"
                            >
                                <span>{{ mb_strtoupper($label) }}</span>
                                <span aria-hidden="true">›</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </x-tienda.shell>
</x-filament-panels::page>
