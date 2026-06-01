@php
    use App\Filament\Support\ProgramaCategories;
    use App\Filament\Support\ProgramaImageUpload;
    use App\Filament\Support\ProgramaSolicitudTableColumn;
    use App\Filament\Support\ProgramasTableColumns;
    use Illuminate\Support\Str;

    /** @var \App\Models\Programas $record */
    $record = $record ?? $schemaComponent?->getRecord();

    $images = ProgramaImageUpload::existingStoredPaths($record->gallery_images ?? [], 'programas/gallery');

    $codigo = str_pad($record->id, 4, '0', STR_PAD_LEFT);
    $codigo = substr($codigo, 0, 2) . ' ' . substr($codigo, 2, 2);

    $osLabel = match ($record->os_required) {
        'windows' => 'Windows',
        'mac' => 'Mac',
        'win-mac' => 'Win & Mac',
        default => strtoupper((string) $record->os_required),
    };

    $webOficialUrl = ProgramasTableColumns::webOficialUrl($record->web_oficial);

    $badgeBase = 'inline-flex max-w-full items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset';

    $badge = fn (string $tone): string => match ($tone) {
        'blue' => "{$badgeBase} bg-blue-600/22 text-blue-950 ring-blue-800/35 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30",
        'green' => "{$badgeBase} bg-emerald-600/22 text-emerald-950 ring-emerald-800/35 dark:bg-green-500/15 dark:text-green-300 dark:ring-green-500/30",
        'rose' => "{$badgeBase} bg-rose-600/22 text-rose-950 ring-rose-800/35 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30",
        'pink' => "{$badgeBase} bg-pink-600/22 text-pink-950 ring-pink-800/35 dark:bg-pink-500/15 dark:text-pink-300 dark:ring-pink-500/30",
        'violet' => "{$badgeBase} bg-violet-600/22 text-violet-950 ring-violet-800/35 dark:bg-violet-500/15 dark:text-violet-300 dark:ring-violet-500/30",
        'orange' => "{$badgeBase} bg-orange-600/22 text-orange-950 ring-orange-800/35 dark:bg-orange-500/15 dark:text-orange-300 dark:ring-orange-500/30",
        'fuchsia' => "{$badgeBase} bg-fuchsia-600/22 text-fuchsia-950 ring-fuchsia-800/35 dark:bg-fuchsia-500/15 dark:text-fuchsia-300 dark:ring-fuchsia-500/30",
        'amber' => "{$badgeBase} bg-amber-600/22 text-amber-950 ring-amber-800/35 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30",
        'emerald' => "{$badgeBase} bg-emerald-600/22 text-emerald-950 ring-emerald-800/35 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30",
        'teal' => "{$badgeBase} bg-teal-600/22 text-teal-950 ring-teal-800/35 dark:bg-teal-500/15 dark:text-teal-300 dark:ring-teal-500/30",
        'cyan' => "{$badgeBase} bg-cyan-600/22 text-cyan-950 ring-cyan-800/35 dark:bg-cyan-500/15 dark:text-cyan-300 dark:ring-cyan-500/30",
        'zinc' => "{$badgeBase} bg-zinc-600/22 text-zinc-950 ring-zinc-700/35 dark:bg-zinc-500/15 dark:text-zinc-300 dark:ring-zinc-500/30",
        default => "{$badgeBase} bg-indigo-600/22 text-indigo-950 ring-indigo-800/35 dark:bg-zinc-500/15 dark:text-zinc-300 dark:ring-zinc-500/30",
    };

    $categoryBadgeClass = match ($record->category) {
        'diseño grafico' => $badge('pink'),
        'kontakt' => $badge('violet'),
        'arquitectura' => $badge('orange'),
        'aplicaciones' => $badge('blue'),
        'video' => $badge('fuchsia'),
        'music' => $badge('amber'),
        'office-pdf' => $badge('emerald'),
        default => $badge('zinc'),
    };

    $osBadgeClass = match ($record->os_required) {
        'windows' => $badge('blue'),
        'mac' => $badge('rose'),
        'win-mac' => $badge('zinc'),
        default => $badge('zinc'),
    };

    $metaFields = [
        ['label' => 'Código', 'value' => $codigo, 'badge' => $badge('blue')],
        ['label' => 'Tamaño', 'value' => $record->size, 'badge' => $badge('green')],
        ['label' => 'Sistema operativo', 'value' => $osLabel, 'badge' => $osBadgeClass],
        ['label' => 'Categoría', 'value' => ProgramaCategories::label($record->category), 'badge' => $categoryBadgeClass],
    ];

    if ($record->year_prog) {
        $metaFields[] = ['label' => 'Año', 'value' => $record->year_prog, 'badge' => $badge('fuchsia')];
    }

    if ($record->working) {
        $metaFields[] = [
            'label' => 'Subcategoría',
            'value' => ProgramaCategories::subcategoryLabel($record->category, $record->working),
            'badge' => $badge('violet'),
        ];
    }

    if (filled($record->idioma)) {
        $metaFields[] = ['label' => 'Idioma', 'value' => strtoupper($record->idioma), 'badge' => $badge('orange')];
    }

    if (filled($record->required)) {
        $metaFields[] = ['label' => 'Requerido', 'value' => $record->required, 'badge' => $badge('teal')];
    }

    if (filled($record->company)) {
        $metaFields[] = ['label' => 'Marca', 'value' => strtoupper($record->company), 'badge' => $badge('cyan')];
    }

    if (filled($record->level_inst)) {
        $metaFields[] = ['label' => 'Tipo / archivo', 'value' => $record->level_inst, 'badge' => $badge('violet')];
    }

    $fotoDescr1Url = ProgramaImageUpload::publicUrl(
        ProgramaImageUpload::existingSinglePath($record->foto_descr1, 'programas/descr'),
        'programas/descr',
    );

    $fotoDescr2Url = ProgramaImageUpload::publicUrl(
        ProgramaImageUpload::existingSinglePath($record->foto_descr2, 'programas/descr'),
        'programas/descr',
    );

    $hasFotosDescripcion = filled($fotoDescr1Url) || filled($fotoDescr2Url);

    $solicitarStatus = ProgramaSolicitudTableColumn::status($record);
@endphp

<div id="cliente-producto-detalle">
    <div class="cp-layout">
        <div class="cp-gallery">
            @forelse ($images as $image)
                <div class="cp-frame">
                    <img
                        src="{{ ProgramaImageUpload::publicUrl($image, 'programas/gallery') }}"
                        alt="{{ $record->progname }}"
                    />
                </div>
            @empty
                <div class="cp-frame" style="font-size:0.75rem;color:rgb(161 161 170);text-align:center;">
                    Sin imágenes
                </div>
            @endforelse
        </div>

        <div class="cp-content">
            <div class="cp-meta">
                @foreach ($metaFields as $field)
                    <div>
                        <div class="cp-meta-label">{{ $field['label'] }}</div>
                        <div class="cp-meta-value">
                            <span class="{{ $field['badge'] }}">
                                {{ $field['value'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($webOficialUrl)
                <div class="cp-web">
                    <div class="cp-web-label">Web oficial</div>
                    <a
                        href="{{ $webOficialUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-1 inline-block font-medium text-indigo-800 hover:text-indigo-600 dark:text-amber-400 dark:hover:text-amber-300"
                    >
                        {{ $record->web_oficial }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="cp-solicitar">
        @if ($solicitarStatus === 'en_pedidos')
            <span class="cp-solicitar-estado cp-solicitar-estado--pedidos">En Pedidos</span>
        @elseif ($solicitarStatus === 'pendiente')
            <span class="cp-solicitar-estado cp-solicitar-estado--pendiente">Pendiente</span>
        @else
            <button
                type="button"
                class="cp-solicitar-btn"
                wire:click="solicitarPrograma"
                wire:loading.attr="disabled"
                wire:target="solicitarPrograma"
            >
                <span wire:loading.remove wire:target="solicitarPrograma">SOLICITAR YA</span>
                <span wire:loading wire:target="solicitarPrograma">Enviando…</span>
            </button>
        @endif
    </div>

    <div class="cp-full">
        <div class="cp-descripcion" style="margin-top:0;padding-top:0;border-top:none;">
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-indigo-800 dark:text-amber-400">Descripción</h3>
            @if (filled($record->description))
                <div class="prose prose-sm max-w-none text-indigo-950 dark:prose-invert dark:text-gray-200">
                    {!! Str::markdown($record->description) !!}
                </div>
            @else
                <p class="text-sm text-indigo-900/80 dark:text-gray-400">Sin descripción disponible.</p>
            @endif
        </div>

        @if ($hasFotosDescripcion)
            <div class="cp-fotos-descr">
                @if ($fotoDescr1Url)
                    <div class="cp-foto-descr-item">
                        <img src="{{ $fotoDescr1Url }}" alt="{{ $record->progname }} — imagen 1" />
                    </div>
                @endif

                @if ($fotoDescr2Url)
                    <div class="cp-foto-descr-item">
                        <img src="{{ $fotoDescr2Url }}" alt="{{ $record->progname }} — imagen 2" />
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
