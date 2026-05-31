@php
    use App\Filament\Admin\Resources\Programas\ProgramasResource;
    use App\Filament\Support\ProgramaCategories;
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
        'office-pdf' => 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/30',
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
        <div class="sticky top-0 z-10 -mx-4 space-y-2 bg-gray-950/90 px-4 py-2 backdrop-blur sm:static sm:mx-0 sm:bg-transparent sm:px-0 sm:py-0">
            <div class="flex gap-2">
                <button
                    type="button"
                    wire:click="setOsFilter('windows')"
                    @class([
                        'programas-os-tab programas-os-tab--windows inline-flex flex-1 items-center justify-center rounded-lg border px-3 py-1.5 text-sm font-semibold transition sm:flex-none sm:px-4 sm:py-1 sm:text-xs',
                        'border-blue-500 bg-blue-500/15 text-blue-300' => $this->osFilter === 'windows',
                        'border-gray-700 bg-gray-900/60 text-blue-300/70 hover:border-blue-500/50' => $this->osFilter !== 'windows',
                    ])
                >
                    Windows
                </button>
                <button
                    type="button"
                    wire:click="setOsFilter('mac')"
                    @class([
                        'programas-os-tab programas-os-tab--mac inline-flex flex-1 items-center justify-center rounded-lg border px-3 py-1.5 text-sm font-semibold transition sm:flex-none sm:px-4 sm:py-1 sm:text-xs',
                        'border-rose-900 bg-rose-500/15 text-rose-300' => $this->osFilter === 'mac',
                        'border-gray-700 bg-gray-900/60 text-rose-300/70 hover:border-rose-900/50' => $this->osFilter !== 'mac',
                    ])
                >
                    Mac
                </button>
            </div>

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
                    x-data="{ expanded: false }"
                    class="rounded-xl border border-gray-700/80 bg-gray-900/60 p-4 shadow-sm sm:p-3"
                >
                    <button
                        type="button"
                        x-on:click="expanded = ! expanded"
                        class="flex w-full cursor-pointer items-center justify-between gap-2 text-left"
                    >
                        <h3 class="min-w-0 flex-1 text-base font-semibold uppercase leading-snug tracking-wide text-white transition hover:text-amber-300 sm:text-xs">
                            {{ mb_strtoupper($programa->progname) }}
                        </h3>
                        <span
                            class="shrink-0 text-[10px] leading-none text-gray-500 transition-transform"
                            x-bind:class="expanded && 'rotate-180'"
                        >▼</span>
                    </button>

                    <div
                        x-show="expanded"
                        x-cloak
                        style="display: none;"
                        class="mt-3 space-y-2 border-t border-gray-800 pt-3 sm:mt-2 sm:pt-2"
                    >
                        <div class="flex items-start justify-end gap-2">
                            {!! $labeledBadge('ID', '#'.$programa->id, 'bg-blue-500/15 text-blue-300 ring-blue-500/30 shrink-0') !!}
                        </div>

                        <div class="space-y-2 sm:space-y-1">
                            {{-- Fila 1 --}}
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 sm:gap-y-1">
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
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 sm:gap-y-1">
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
                                    {!! $labeledBadge('Categoría', ProgramaCategories::label($programa->category), $categoryColor($programa->category)) !!}
                                @endif

                                <div class="ml-auto inline-flex flex-wrap items-center justify-end gap-x-3 gap-y-1.5">
                                    <div class="inline-flex items-center gap-2.5">
                                        <span class="text-sm font-medium text-gray-300 sm:text-xs">Instalador</span>
                                        <button
                                            type="button"
                                            role="switch"
                                            wire:click="toggleInstaladorVisibility({{ $programa->id }})"
                                            aria-checked="{{ $programa->show_instalador ? 'true' : 'false' }}"
                                            title="{{ $programa->show_instalador ? 'Instalador visible para clientes' : 'Instalador oculto para clientes' }}"
                                            @class([
                                                'card-pedidos-switch relative inline-flex shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500/50',
                                                'card-pedidos-switch--on' => $programa->show_instalador,
                                                'bg-gray-600' => ! $programa->show_instalador,
                                            ])
                                        >
                                            <span
                                                @class([
                                                    'card-pedidos-switch-knob pointer-events-none inline-block rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out',
                                                    'is-on' => $programa->show_instalador,
                                                ])
                                            ></span>
                                        </button>
                                    </div>

                                    <div class="inline-flex items-center gap-2.5">
                                        <span class="text-sm font-medium text-gray-300 sm:text-xs">Pedidos</span>
                                        <button
                                            type="button"
                                            role="switch"
                                            wire:click="togglePedidosVisibility({{ $programa->id }})"
                                            aria-checked="{{ $programa->isPedidosTimerActive() ? 'true' : 'false' }}"
                                            title="{{ $programa->isPedidosTimerActive() ? 'Visible en Pedidos (30 min)' : 'Oculto en Pedidos' }}"
                                            @class([
                                                'card-pedidos-switch relative inline-flex shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500/50',
                                                'card-pedidos-switch--on' => $programa->isPedidosTimerActive(),
                                                'bg-gray-600' => ! $programa->isPedidosTimerActive(),
                                            ])
                                        >
                                            <span
                                                @class([
                                                    'card-pedidos-switch-knob pointer-events-none inline-block rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out',
                                                    'is-on' => $programa->isPedidosTimerActive(),
                                                ])
                                            ></span>
                                        </button>
                                    </div>
                                </div>

                                {!! $labeledBadge('Sistema', $osLabel($programa->os_required), $osColor($programa->os_required)) !!}
                            </div>
                        </div>

                        @php
                            $descripcionPlano = filled($programa->description)
                                ? Str::of(strip_tags(Str::markdown($programa->description)))->squish()
                                : null;
                        @endphp
                        <div x-data="{ open: false }">
                            <button
                                type="button"
                                x-on:click="open = ! open"
                                class="flex w-full items-center justify-between gap-2 rounded-md border border-gray-700/80 bg-gray-950/50 px-2.5 py-1.5 text-left text-sm font-medium text-gray-400 transition hover:border-gray-600 hover:text-gray-200 sm:px-2 sm:py-1 sm:text-xs"
                            >
                                <span>Descripción</span>
                                <span
                                    class="shrink-0 text-[10px] leading-none text-gray-500 transition-transform"
                                    x-bind:class="open && 'rotate-180'"
                                >▼</span>
                            </button>
                            <div
                                x-show="open"
                                x-cloak
                                style="display: none;"
                                class="mt-1 rounded-md border border-gray-800/60 bg-gray-950/30 px-2.5 py-2 sm:px-2 sm:py-1.5"
                            >
                                @if (filled($descripcionPlano))
                                    <p class="break-words text-sm leading-relaxed text-gray-400 sm:text-[10px] sm:leading-snug">
                                        {{ $descripcionPlano }}
                                    </p>
                                @else
                                    <p class="text-sm italic text-gray-500 sm:text-[10px]">Sin descripción.</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-2 border-t border-gray-800 pt-3 sm:pt-2">
                            <a
                                href="{{ ProgramasResource::getUrl('edit', ['record' => $programa]) }}"
                                class="inline-flex flex-1 items-center justify-center rounded-md bg-amber-500/15 px-1 py-1.5 text-sm font-semibold text-amber-300 ring-1 ring-inset ring-amber-500/30 sm:px-2 sm:py-1 sm:text-[10px]"
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
                                class="inline-flex flex-1 cursor-pointer items-center justify-center rounded-md bg-rose-500/15 px-1 py-1.5 text-sm font-semibold text-rose-300 ring-1 ring-inset ring-rose-500/30 sm:px-2 sm:py-1 sm:text-[10px]"
                            >
                                CopyLink
                            </button>

                            <button
                                type="button"
                                wire:click="deletePrograma({{ $programa->id }})"
                                wire:confirm="¿Eliminar este programa? Esta acción no se puede deshacer."
                                class="inline-flex flex-1 cursor-pointer items-center justify-center rounded-md bg-red-500/15 px-1 py-1.5 text-sm font-semibold text-red-300 ring-1 ring-inset ring-red-500/30 sm:px-2 sm:py-1 sm:text-[10px]"
                            >
                                Borrar
                            </button>
                        </div>
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
