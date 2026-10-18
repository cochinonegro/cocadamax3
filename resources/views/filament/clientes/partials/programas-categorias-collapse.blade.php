@php
    use App\Filament\Support\ProgramaCategories;
@endphp

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
                @foreach (ProgramaCategories::options() as $key => $label)
                    <button
                        type="button"
                        wire:click="toggleCategoryFilter(@js($key))"
                        wire:key="invitado-cat-{{ $key }}"
                        @class([
                            'invitado-categorias-grid__item',
                            'is-active' => ($this->categoryFilter ?? null) === $key,
                        ])
                    >
                        <span>{{ mb_strtoupper($label) }}</span>
                    </button>
                @endforeach
            </div>

            @if (filled($this->categoryFilter ?? null))
                <button
                    type="button"
                    class="invitado-categorias-clear"
                    wire:click="clearCategoryFilter"
                >
                    Quitar filtro de categoría
                </button>
            @endif
        </div>
    </div>
</section>

<p class="invitado-ultimos-heading">VER ULTIMOS PROGRAMAS DISPONIBLES:</p>
