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
@endphp

<div class="cliente-producto-detalle grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-3">
        @forelse ($images as $image)
            <img
                src="{{ ProgramaImageUpload::publicUrl($image, 'programas/gallery') }}"
                alt="{{ $record->progname }}"
                @class([
                    'rounded-xl border border-gray-200 object-cover dark:border-gray-700',
                    'w-1/2' => $loop->first,
                    'w-full' => ! $loop->first,
                ])
            />
        @empty
            <div class="flex h-48 items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-900/50 dark:text-gray-400">
                Sin imágenes del producto
            </div>
        @endforelse
    </div>

    <div class="space-y-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Programa</p>
            <h2 class="text-2xl font-bold text-gray-950 dark:text-white">{{ $record->progname }}</h2>
        </div>

        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Código</dt>
                <dd class="font-medium text-gray-950 dark:text-white">{{ $codigo }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Tamaño</dt>
                <dd class="font-medium text-gray-950 dark:text-white">{{ $record->size }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Sistema operativo</dt>
                <dd class="font-medium text-gray-950 dark:text-white">{{ $osLabel }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Categoría</dt>
                <dd class="font-medium text-gray-950 dark:text-white">{{ ProgramaCategories::label($record->category) }}</dd>
            </div>
            @if ($record->year_prog)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Año</dt>
                    <dd class="font-medium text-gray-950 dark:text-white">{{ $record->year_prog }}</dd>
                </div>
            @endif
            @if ($record->working)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Subcategoría</dt>
                    <dd class="font-medium text-gray-950 dark:text-white">{{ ProgramaCategories::subcategoryLabel($record->category, $record->working) }}</dd>
                </div>
            @endif
            @if (filled($record->idioma))
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Idioma</dt>
                    <dd class="font-medium text-gray-950 dark:text-white">{{ strtoupper($record->idioma) }}</dd>
                </div>
            @endif
            @if (filled($record->required))
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Requerido</dt>
                    <dd class="font-medium text-gray-950 dark:text-white">{{ $record->required }}</dd>
                </div>
            @endif
            @if (filled($record->company))
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Marca</dt>
                    <dd class="font-medium text-gray-950 dark:text-white">{{ strtoupper($record->company) }}</dd>
                </div>
            @endif
            @if (filled($record->level_inst))
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Tipo / archivo</dt>
                    <dd class="font-medium text-gray-950 dark:text-white">{{ $record->level_inst }}</dd>
                </div>
            @endif
            @if ($webOficialUrl)
                <div class="col-span-2">
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
            @endif
        </dl>

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
