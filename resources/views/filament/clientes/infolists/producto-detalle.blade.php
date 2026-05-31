@php
    use App\Filament\Support\ProgramaCategories;
    use App\Filament\Support\ProgramaImageUpload;
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

    $categoryBadgeClass = match ($record->category) {
        'diseño grafico' => 'cliente-producto-detalle__badge--pink',
        'kontakt' => 'cliente-producto-detalle__badge--violet',
        'arquitectura' => 'cliente-producto-detalle__badge--orange',
        'aplicaciones' => 'cliente-producto-detalle__badge--blue',
        'video' => 'cliente-producto-detalle__badge--fuchsia',
        'music' => 'cliente-producto-detalle__badge--amber',
        'office-pdf' => 'cliente-producto-detalle__badge--emerald',
        default => 'cliente-producto-detalle__badge--gray',
    };

    $osBadgeClass = match ($record->os_required) {
        'windows' => 'cliente-producto-detalle__badge--blue',
        'mac' => 'cliente-producto-detalle__badge--rose',
        'win-mac' => 'cliente-producto-detalle__badge--gray',
        default => 'cliente-producto-detalle__badge--gray',
    };

    $metaFields = [
        ['label' => 'Código', 'value' => $codigo, 'badge' => 'cliente-producto-detalle__badge--blue'],
        ['label' => 'Tamaño', 'value' => $record->size, 'badge' => 'cliente-producto-detalle__badge--green'],
        ['label' => 'Sistema operativo', 'value' => $osLabel, 'badge' => $osBadgeClass],
        ['label' => 'Categoría', 'value' => ProgramaCategories::label($record->category), 'badge' => $categoryBadgeClass],
    ];

    if ($record->year_prog) {
        $metaFields[] = ['label' => 'Año', 'value' => $record->year_prog, 'badge' => 'cliente-producto-detalle__badge--fuchsia'];
    }

    if ($record->working) {
        $metaFields[] = [
            'label' => 'Subcategoría',
            'value' => ProgramaCategories::subcategoryLabel($record->category, $record->working),
            'badge' => 'cliente-producto-detalle__badge--violet',
        ];
    }

    if (filled($record->idioma)) {
        $metaFields[] = ['label' => 'Idioma', 'value' => strtoupper($record->idioma), 'badge' => 'cliente-producto-detalle__badge--orange'];
    }

    if (filled($record->required)) {
        $metaFields[] = ['label' => 'Requerido', 'value' => $record->required, 'badge' => 'cliente-producto-detalle__badge--teal'];
    }

    if (filled($record->company)) {
        $metaFields[] = ['label' => 'Marca', 'value' => strtoupper($record->company), 'badge' => 'cliente-producto-detalle__badge--cyan'];
    }

    if (filled($record->level_inst)) {
        $metaFields[] = ['label' => 'Tipo / archivo', 'value' => $record->level_inst, 'badge' => 'cliente-producto-detalle__badge--violet'];
    }
@endphp

<div class="cliente-producto-detalle">
    <div class="cliente-producto-detalle__gallery">
        @forelse ($images as $image)
            <img
                src="{{ ProgramaImageUpload::publicUrl($image, 'programas/gallery') }}"
                alt="{{ $record->progname }}"
                @class([
                    'cliente-producto-detalle__image',
                    'cliente-producto-detalle__image--primary' => $loop->first,
                    'cliente-producto-detalle__image--secondary' => ! $loop->first,
                ])
            />
        @empty
            <div class="cliente-producto-detalle__placeholder">
                Sin imágenes del producto
            </div>
        @endforelse
    </div>

    <div class="cliente-producto-detalle__content space-y-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Programa</p>
            <h2 class="text-2xl font-bold text-gray-950 dark:text-white">{{ $record->progname }}</h2>
        </div>

        <dl class="cliente-producto-detalle__meta">
            @foreach ($metaFields as $field)
                <div class="cliente-producto-detalle__meta-item">
                    <dt>{{ $field['label'] }}</dt>
                    <dd>
                        <span @class(['cliente-producto-detalle__badge', $field['badge']])>
                            {{ $field['value'] }}
                        </span>
                    </dd>
                </div>
            @endforeach
        </dl>

        @if ($webOficialUrl)
            <dl class="cliente-producto-detalle__web">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Web oficial</dt>
                    <dd>
                        <a
                            href="{{ $webOficialUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                        >
                            {{ $record->web_oficial }}
                        </a>
                    </dd>
                </div>
            </dl>
        @endif

        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">Descripción</h3>
            @if (filled($record->description))
                <div class="prose prose-sm max-w-none text-gray-700 dark:prose-invert dark:text-gray-200">
                    {!! Str::markdown($record->description) !!}
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Sin descripción disponible.</p>
            @endif
        </div>
    </div>
</div>
