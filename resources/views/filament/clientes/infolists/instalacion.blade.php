@php
    use App\Filament\Support\ProgramasTableColumns;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    /** @var \App\Models\Programas $record */
    $record = $record ?? $schemaComponent?->getRecord();

    $steps = $record->installation_steps ?? [];
    $installerPhotoUrl = filled($record->foto_instalador)
        ? Storage::disk('public')->url($record->foto_instalador)
        : null;
    $videoInstaladorUrl = ProgramasTableColumns::downloadUrl($record->video_instalador);
@endphp

<div class="cliente-instalacion w-full">
    <div class="mb-8">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
            Información general
        </h4>

        @if (filled($record->info_install))
            <div class="prose prose-sm mb-4 max-w-none text-gray-700 dark:prose-invert dark:text-gray-200">
                {!! Str::markdown($record->info_install) !!}
            </div>
        @endif

        <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-200">
            Lo primero que se debe hacer es apagar el antivirus o detener la seguridad de Windows, buscar el programa Seguridad de Windows y buscar el texto en azul
            <span class="font-semibold text-blue-600 dark:text-blue-400">Administrar la configuración</span>.
        </p>
    </div>

    <div class="mb-8">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
            Foto del instalador
        </h4>
        @if ($installerPhotoUrl)
            @include('filament.clientes.partials.image-lightbox', [
                'src' => $installerPhotoUrl,
                'alt' => 'Foto del instalador — ' . $record->progname,
            ])
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Sin foto del instalador.</p>
        @endif
    </div>

    @if (count($steps) > 0)
        <div class="space-y-6">
            <h4 class="text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                Pasos de instalación
            </h4>

            @foreach ($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $title = $step['title'] ?? null;
                    $description = $step['description'] ?? ($step['text'] ?? null);
                    $imagePath = $step['image'] ?? null;
                    $imageUrl = filled($imagePath) ? Storage::disk('public')->url($imagePath) : null;
                @endphp

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 md:p-6 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-500">
                        Paso {{ $stepNumber }}
                    </p>

                    @if (filled($title))
                        <h5 class="mb-4 text-lg font-semibold text-gray-950 dark:text-white">
                            {{ $title }}
                        </h5>
                    @endif

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            @if ($imageUrl)
                                @include('filament.clientes.partials.image-lightbox', [
                                    'src' => $imageUrl,
                                    'alt' => 'Paso ' . $stepNumber . ' — ' . ($title ?? $record->progname),
                                ])
                            @else
                                <div class="flex h-48 items-center justify-center rounded-lg border border-dashed border-gray-300 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-500">
                                    Sin imagen
                                </div>
                            @endif
                        </div>

                        <div class="flex items-start">
                            @if (filled($description))
                                <div class="prose prose-sm max-w-none text-gray-700 dark:prose-invert dark:text-gray-200">
                                    {!! Str::markdown($description) !!}
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sin descripción.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($videoInstaladorUrl)
        <div class="mb-8">
            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                Video de instalación
            </h4>
            <a
                href="{{ $videoInstaladorUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-500 dark:bg-amber-500 dark:hover:bg-amber-400"
            >
                Ver video de instalación
            </a>
        </div>
    @endif
</div>
