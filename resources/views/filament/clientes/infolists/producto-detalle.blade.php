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

    $fotoDescr1Url = ProgramaImageUpload::publicUrl(
        ProgramaImageUpload::existingSinglePath($record->foto_descr1, 'programas/descr'),
        'programas/descr',
    );

    $fotoDescr2Url = ProgramaImageUpload::publicUrl(
        ProgramaImageUpload::existingSinglePath($record->foto_descr2, 'programas/descr'),
        'programas/descr',
    );

    $hasFotosDescripcion = filled($fotoDescr1Url) || filled($fotoDescr2Url);
@endphp

<div id="cliente-producto-detalle">
    <style>
        #cliente-producto-detalle .cp-layout {
            display: grid !important;
            grid-template-columns: 9rem minmax(0, 1fr) !important;
            gap: 1.5rem 2rem;
            align-items: start;
        }

        #cliente-producto-detalle .cp-gallery {
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
        }

        #cliente-producto-detalle .cp-frame {
            display: flex;
            width: 9rem;
            height: 9rem;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0.75rem;
            border: 1px solid rgb(63 63 70);
            background: rgb(24 24 27 / 0.55);
            padding: 0.625rem;
            box-sizing: border-box;
        }

        #cliente-producto-detalle .cp-frame img {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            object-position: center;
        }

        #cliente-producto-detalle .cp-content {
            min-width: 0;
        }

        #cliente-producto-detalle .cp-meta {
            display: grid !important;
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 0.75rem 1rem;
        }

        #cliente-producto-detalle .cp-meta-label {
            font-size: 0.75rem;
            line-height: 1rem;
            color: rgb(161 161 170);
        }

        #cliente-producto-detalle .cp-meta-value {
            margin-top: 0.375rem;
        }

        #cliente-producto-detalle .cp-web {
            margin-top: 1rem;
            font-size: 0.875rem;
        }

        #cliente-producto-detalle .cp-web-label {
            font-size: 0.75rem;
            color: rgb(161 161 170);
        }

        #cliente-producto-detalle .cp-descripcion {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgb(63 63 70);
        }

        #cliente-producto-detalle .cp-solicitar {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgb(63 63 70);
        }

        #cliente-producto-detalle .cp-full {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgb(63 63 70);
        }

        #cliente-producto-detalle .cp-fotos-descr {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 1rem;
            margin-top: 1.25rem;
            width: 100%;
        }

        #cliente-producto-detalle .cp-foto-descr-item {
            display: flex;
            min-height: 12rem;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0.75rem;
            border: 1px solid rgb(63 63 70);
            background: rgb(24 24 27 / 0.55);
            padding: 0.5rem;
            box-sizing: border-box;
        }

        #cliente-producto-detalle .cp-foto-descr-item img {
            display: block;
            width: 100%;
            height: auto;
            max-height: 28rem;
            object-fit: contain;
            object-position: center;
        }

        @media (max-width: 767px) {
            #cliente-producto-detalle .cp-layout {
                grid-template-columns: 1fr !important;
            }

            #cliente-producto-detalle .cp-meta {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            #cliente-producto-detalle .cp-fotos-descr {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

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
                        class="mt-1 inline-block font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                    >
                        {{ $record->web_oficial }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="cp-solicitar">
        @livewire('solicitar-programa-button', ['programaId' => $record->id, 'variant' => 'detail'], key('solicitar-detail-'.$record->id))
    </div>

    <div class="cp-full">
        <div class="cp-descripcion" style="margin-top:0;padding-top:0;border-top:none;">
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">Descripción</h3>
            @if (filled($record->description))
                <div class="prose prose-sm max-w-none text-gray-700 dark:prose-invert dark:text-gray-200">
                    {!! Str::markdown($record->description) !!}
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Sin descripción disponible.</p>
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
