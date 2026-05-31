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

    $badgeBase = 'inline-flex max-w-full items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset';

    $categoryBadgeClass = match ($record->category) {
        'diseño grafico' => "{$badgeBase} bg-pink-500/15 text-pink-300 ring-pink-500/30",
        'kontakt' => "{$badgeBase} bg-violet-500/15 text-violet-300 ring-violet-500/30",
        'arquitectura' => "{$badgeBase} bg-orange-500/15 text-orange-300 ring-orange-500/30",
        'aplicaciones' => "{$badgeBase} bg-blue-500/15 text-blue-300 ring-blue-500/30",
        'video' => "{$badgeBase} bg-fuchsia-500/15 text-fuchsia-300 ring-fuchsia-500/30",
        'music' => "{$badgeBase} bg-amber-500/15 text-amber-300 ring-amber-500/30",
        'office-pdf' => "{$badgeBase} bg-emerald-500/15 text-emerald-300 ring-emerald-500/30",
        default => "{$badgeBase} bg-zinc-500/15 text-zinc-300 ring-zinc-500/30",
    };

    $osBadgeClass = match ($record->os_required) {
        'windows' => "{$badgeBase} bg-blue-500/15 text-blue-300 ring-blue-500/30",
        'mac' => "{$badgeBase} bg-rose-500/15 text-rose-300 ring-rose-500/30",
        'win-mac' => "{$badgeBase} bg-zinc-500/15 text-zinc-300 ring-zinc-500/30",
        default => "{$badgeBase} bg-zinc-500/15 text-zinc-300 ring-zinc-500/30",
    };

    $metaFields = [
        ['label' => 'Código', 'value' => $codigo, 'badge' => "{$badgeBase} bg-blue-500/15 text-blue-300 ring-blue-500/30"],
        ['label' => 'Tamaño', 'value' => $record->size, 'badge' => "{$badgeBase} bg-green-500/15 text-green-300 ring-green-500/30"],
        ['label' => 'Sistema operativo', 'value' => $osLabel, 'badge' => $osBadgeClass],
        ['label' => 'Categoría', 'value' => ProgramaCategories::label($record->category), 'badge' => $categoryBadgeClass],
    ];

    if ($record->year_prog) {
        $metaFields[] = ['label' => 'Año', 'value' => $record->year_prog, 'badge' => "{$badgeBase} bg-fuchsia-500/15 text-fuchsia-300 ring-fuchsia-500/30"];
    }

    if ($record->working) {
        $metaFields[] = [
            'label' => 'Subcategoría',
            'value' => ProgramaCategories::subcategoryLabel($record->category, $record->working),
            'badge' => "{$badgeBase} bg-violet-500/15 text-violet-300 ring-violet-500/30",
        ];
    }

    if (filled($record->idioma)) {
        $metaFields[] = ['label' => 'Idioma', 'value' => strtoupper($record->idioma), 'badge' => "{$badgeBase} bg-orange-500/15 text-orange-300 ring-orange-500/30"];
    }

    if (filled($record->required)) {
        $metaFields[] = ['label' => 'Requerido', 'value' => $record->required, 'badge' => "{$badgeBase} bg-teal-500/15 text-teal-300 ring-teal-500/30"];
    }

    if (filled($record->company)) {
        $metaFields[] = ['label' => 'Marca', 'value' => strtoupper($record->company), 'badge' => "{$badgeBase} bg-cyan-500/15 text-cyan-300 ring-cyan-500/30"];
    }

    if (filled($record->level_inst)) {
        $metaFields[] = ['label' => 'Tipo / archivo', 'value' => $record->level_inst, 'badge' => "{$badgeBase} bg-violet-500/15 text-violet-300 ring-violet-500/30"];
    }
@endphp

<div class="grid grid-cols-1 items-start gap-5 md:grid-cols-[minmax(6rem,8rem)_minmax(0,1fr)] md:gap-7">
    <div class="flex flex-col gap-2">
        @forelse ($images as $image)
            <img
                src="{{ ProgramaImageUpload::publicUrl($image, 'programas/gallery') }}"
                alt="{{ $record->progname }}"
                @class([
                    'block w-full rounded-lg border border-zinc-700 object-cover',
                    'max-w-28 aspect-square' => $loop->first,
                    'max-w-20 aspect-[4/3]' => ! $loop->first,
                ])
            />
        @empty
            <div class="flex min-h-28 max-w-28 items-center justify-center rounded-lg border border-dashed border-zinc-600 bg-zinc-900/50 p-3 text-center text-xs text-zinc-400">
                Sin imágenes del producto
            </div>
        @endforelse
    </div>

    <div class="min-w-0 space-y-4">
        <dl class="grid grid-cols-2 gap-3 gap-x-4 md:grid-cols-4">
            @foreach ($metaFields as $field)
                <div>
                    <dt class="text-xs text-zinc-400">{{ $field['label'] }}</dt>
                    <dd class="mt-1.5">
                        <span class="{{ $field['badge'] }}">
                            {{ $field['value'] }}
                        </span>
                    </dd>
                </div>
            @endforeach
        </dl>

        @if ($webOficialUrl)
            <div class="text-sm">
                <p class="text-xs text-zinc-400">Web oficial</p>
                <a
                    href="{{ $webOficialUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-1 inline-block font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                >
                    {{ $record->web_oficial }}
                </a>
            </div>
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
