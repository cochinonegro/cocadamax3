@php
    use App\Filament\Admin\Resources\Programas\ProgramasResource;
    use Illuminate\Support\Str;

    $osLabel = fn (?string $state) => match ($state) {
        'windows' => 'WINDOWS',
        'mac' => 'MAC',
        'win-mac' => 'WIN & MAC',
        default => strtoupper((string) ($state ?? '-')),
    };

    $osColor = fn (?string $state) => match ($state) {
        'windows' => 'bg-blue-500/15 text-blue-300 ring-blue-500/30',
        'mac' => 'bg-rose-500/15 text-rose-300 ring-rose-500/30',
        'win-mac' => 'bg-gray-500/15 text-gray-300 ring-gray-500/30',
        default => 'bg-gray-500/15 text-gray-300 ring-gray-500/30',
    };

    $categoryColor = fn (?string $state) => match ($state) {
        'diseño grafico' => 'bg-pink-500/15 text-pink-300 ring-pink-500/30',
        'kontakt' => 'bg-violet-500/15 text-violet-300 ring-violet-500/30',
        'arquitectura' => 'bg-orange-500/15 text-orange-300 ring-orange-500/30',
        'aplicaciones' => 'bg-blue-500/15 text-blue-300 ring-blue-500/30',
        'video' => 'bg-fuchsia-500/15 text-fuchsia-300 ring-fuchsia-500/30',
        'music' => 'bg-amber-500/15 text-amber-300 ring-amber-500/30',
        default => 'bg-gray-500/15 text-gray-300 ring-gray-500/30',
    };

    $labeledBadge = fn (string $title, string $value, string $classes = 'bg-gray-500/15 text-gray-300 ring-gray-500/30') => <<<HTML
        <span class="inline-flex items-center gap-1.5 sm:gap-1">
            <span class="text-sm font-medium text-white sm:text-[10px]">{$title}:</span>
            <span class="inline-flex items-center rounded-md px-2 py-1 text-sm font-medium ring-1 ring-inset sm:px-1.5 sm:py-0.5 sm:text-[10px] {$classes}">{$value}</span>
        </span>
    HTML;
@endphp

<x-filament-panels::page>
    <div class="space-y-4">
        <div class="sticky top-0 z-10 -mx-4 bg-gray-950/90 px-4 py-2 backdrop-blur sm:static sm:mx-0 sm:bg-transparent sm:px-0 sm:py-0">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar programa..."
                />
            </x-filament::input.wrapper>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($this->programas as $programa)
                <article
                    wire:key="card-programa-{{ $programa->id }}"
                    class="rounded-xl border border-gray-700/80 bg-gray-900/60 p-4 shadow-sm sm:p-3"
                >
                    <div class="mb-2 flex items-start justify-between gap-2">
                        <h3 class="min-w-0 flex-1 text-base font-semibold uppercase leading-snug tracking-wide sm:text-xs">
                            <a
                                href="{{ ProgramasResource::getUrl('edit', ['record' => $programa]) }}"
                                class="text-white transition hover:text-amber-300 hover:underline"
                            >
                                {{ mb_strtoupper($programa->progname) }}
                            </a>
                        </h3>
                        {!! $labeledBadge('ID', '#'.$programa->id, 'bg-blue-500/15 text-blue-300 ring-blue-500/30 shrink-0') !!}
                    </div>

                    @if (filled($programa->description))
                        <p class="mb-2 line-clamp-2 w-full break-words text-sm leading-relaxed text-gray-400 sm:text-[10px] sm:leading-snug">
                            {{ Str::of(strip_tags(Str::markdown($programa->description)))->squish() }}
                        </p>
                    @endif

                    <div class="mb-2 space-y-2 sm:space-y-1">
                        {{-- Fila 1 --}}
                        <div class="flex min-h-[1.5rem] flex-wrap items-center gap-x-2 gap-y-1.5 sm:min-h-[1.125rem] sm:gap-y-1">
                            @if ($programa->show && $programa->show_until?->isFuture())
                                {!! $labeledBadge('Estado', 'ACTIVO', 'bg-green-500/15 text-green-300 ring-green-500/30') !!}
                            @else
                                {!! $labeledBadge('Estado', 'INACTIVO', 'bg-gray-500/15 text-gray-400 ring-gray-500/30') !!}
                            @endif

                            @if (filled($programa->idioma))
                                {!! $labeledBadge('Idioma', strtoupper($programa->idioma), 'bg-orange-500/15 text-orange-300 ring-orange-500/30') !!}
                            @endif

                            @if (filled($programa->required))
                                {!! $labeledBadge('Sist.Requerido', $programa->required, 'bg-teal-500/15 text-teal-300 ring-teal-500/30') !!}
                            @endif

                            @if (filled($programa->size))
                                {!! $labeledBadge('Size', $programa->size, 'bg-green-500/15 text-green-300 ring-green-500/30') !!}
                            @endif

                            @if (filled($programa->company))
                                {!! $labeledBadge('Marca', strtoupper($programa->company), 'bg-cyan-500/15 text-cyan-300 ring-cyan-500/30') !!}
                            @endif
                        </div>

                        {{-- Fila 2 --}}
                        <div class="flex min-h-[1.5rem] flex-wrap items-center gap-x-2 gap-y-1.5 sm:min-h-[1.125rem] sm:gap-y-1">
                            @if (filled($programa->year_prog))
                                {!! $labeledBadge('Año', $programa->year_prog, 'bg-fuchsia-500/15 text-fuchsia-300 ring-fuchsia-500/30') !!}
                            @endif

                            @if (filled($programa->level_inst))
                                {!! $labeledBadge('Tipo/Archivo', $programa->level_inst, 'bg-violet-500/15 text-violet-300 ring-violet-500/30') !!}
                            @endif

                            @if ($programa->date_add)
                                {!! $labeledBadge('Fecha', $programa->date_add->format('d/m/Y'), 'bg-sky-500/15 text-sky-300 ring-sky-500/30') !!}
                            @endif

                            @if (filled($programa->category))
                                {!! $labeledBadge('Categoría', ucfirst($programa->category), $categoryColor($programa->category)) !!}
                            @endif

                            {!! $labeledBadge('Sistema', $osLabel($programa->os_required), $osColor($programa->os_required)) !!}
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 border-t border-gray-800 pt-3 sm:pt-2">
                        <a
                            href="{{ ProgramasResource::getUrl('edit', ['record' => $programa]) }}"
                            class="inline-flex items-center justify-center rounded-md bg-amber-500/15 px-2 py-1.5 text-sm font-semibold text-amber-300 ring-1 ring-inset ring-amber-500/30 sm:px-2 sm:py-1 sm:text-[10px]"
                        >
                            Editar
                        </a>

                        <button
                            type="button"
                            @if (filled($programa->url))
                                x-on:click="
                                    navigator.clipboard.writeText(@js($programa->url));
                                    $wire.notifyLinkCopied();
                                "
                            @else
                                x-on:click="$wire.notifyNoLink()"
                            @endif
                            class="inline-flex cursor-pointer items-center justify-center rounded-md bg-rose-500/15 px-2 py-1.5 text-sm font-semibold text-rose-300 ring-1 ring-inset ring-rose-500/30 sm:px-2 sm:py-1 sm:text-[10px]"
                        >
                            CopyLink
                        </button>

                        <button
                            type="button"
                            wire:click="deletePrograma({{ $programa->id }})"
                            wire:confirm="¿Eliminar este programa? Esta acción no se puede deshacer."
                            class="inline-flex cursor-pointer items-center justify-center rounded-md bg-red-500/15 px-2 py-1.5 text-sm font-semibold text-red-300 ring-1 ring-inset ring-red-500/30 sm:px-2 sm:py-1 sm:text-[10px]"
                        >
                            Borrar
                        </button>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-700 p-8 text-center text-sm text-gray-400">
                    No hay programas para mostrar.
                </div>
            @endforelse
        </div>

        <div>
            {{ $this->programas->links() }}
        </div>
    </div>
</x-filament-panels::page>
