@php
    use App\Filament\Support\TiendaProgramas;
@endphp

<x-filament-panels::page>
    <div class="tienda-catalogo mx-auto w-full max-w-none">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($this->programas as $programa)
                @php
                    $cover = TiendaProgramas::coverUrl($programa);
                    $descripcion = TiendaProgramas::plainDescription($programa);
                @endphp

                <article
                    wire:key="tienda-producto-{{ $programa->id }}"
                    class="tienda-producto-card flex flex-col overflow-hidden rounded-xl border border-gray-700/80 bg-gray-900/60"
                >
                    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-950/50">
                        @if ($cover)
                            <img
                                src="{{ $cover }}"
                                alt="{{ $programa->progname }}"
                                class="h-full w-full object-cover"
                            />
                        @else
                            <div class="flex h-full min-h-[10rem] items-center justify-center text-sm text-gray-500">
                                Sin imagen
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <h3 class="mb-2 line-clamp-2 text-base font-semibold leading-snug text-white">
                            {{ $programa->progname }}
                        </h3>

                        @if ($descripcion)
                            <p class="mb-4 line-clamp-4 flex-1 text-sm leading-relaxed text-gray-400">
                                {{ $descripcion }}
                            </p>
                        @else
                            <p class="mb-4 flex-1 text-sm italic text-gray-500">Sin descripción.</p>
                        @endif

                        <a
                            href="{{ $this->verMasUrl($programa) }}"
                            class="tienda-ver-mas-btn mt-auto inline-flex w-fit items-center justify-center rounded-md px-3 py-1.5 text-xs font-semibold uppercase tracking-wide"
                        >
                            Ver más
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-700 p-10 text-center text-sm text-gray-400">
                    No hay productos en esta categoría.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $this->programas->links() }}
        </div>
    </div>
</x-filament-panels::page>
