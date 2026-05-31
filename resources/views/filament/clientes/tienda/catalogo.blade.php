@php
    use App\Filament\Support\TiendaProgramas;
@endphp

<x-filament-panels::page>
    <x-tienda.shell :step="3" :badge="$this->catalogBadge()">
        <div class="tienda-catalogo">
            <div class="tienda-catalogo__grid">
                @forelse ($this->programas as $programa)
                    @php
                        $cover = TiendaProgramas::coverUrl($programa);
                        $descripcion = TiendaProgramas::plainDescription($programa);
                    @endphp

                    <a
                        href="{{ $this->verMasUrl($programa) }}"
                        wire:key="tienda-producto-{{ $programa->id }}"
                        class="tienda-producto-card tienda-producto-card--link"
                    >
                        <div class="tienda-producto-card__media">
                            @if ($cover)
                                <img
                                    src="{{ $cover }}"
                                    alt="{{ $programa->progname }}"
                                    loading="lazy"
                                />
                            @else
                                <div class="tienda-producto-card__placeholder">
                                    <x-heroicon-o-photo class="h-10 w-10 opacity-40" />
                                </div>
                            @endif
                        </div>

                        <div class="tienda-producto-card__body">
                            <h3 class="tienda-producto-card__title">{{ $programa->progname }}</h3>

                            @if ($descripcion)
                                <p class="tienda-producto-card__desc">{{ $descripcion }}</p>
                            @else
                                <p class="tienda-producto-card__desc is-empty">Sin descripción.</p>
                            @endif

                            <span class="tienda-ver-mas-btn">Ver más</span>
                        </div>
                    </a>
                @empty
                    <div class="tienda-catalogo__empty">
                        <x-heroicon-o-shopping-bag class="h-12 w-12 opacity-40" />
                        <p>No hay productos en esta categoría por ahora.</p>
                    </div>
                @endforelse
            </div>

            @if ($this->programas->hasPages())
                <div class="tienda-catalogo__pagination">
                    {{ $this->programas->links() }}
                </div>
            @endif
        </div>
    </x-tienda.shell>
</x-filament-panels::page>
