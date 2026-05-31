@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $steps = $record->installation_steps ?? [];
    $installerPhotoUrl = filled($record->foto_instalador)
        ? Storage::disk('public')->url($record->foto_instalador)
        : null;

    $hasContent = filled($record->info_install)
        || $installerPhotoUrl
        || count($steps) > 0;
@endphp

<div class="cliente-instalacion w-full">
    @if (! $hasContent)
        <p class="text-sm text-gray-400">No hay instrucciones de instalación para este producto.</p>
    @else
        @if (filled($record->info_install))
            <div class="mb-8">
                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-amber-400">
                    Información general
                </h4>
                <div class="prose prose-invert max-w-none text-sm text-gray-200">
                    {!! Str::markdown($record->info_install) !!}
                </div>
            </div>
        @endif

        <div class="mb-8">
            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-amber-400">
                Foto del instalador
            </h4>
            @if ($installerPhotoUrl)
                @include('filament.clientes.partials.image-lightbox', [
                    'src' => $installerPhotoUrl,
                    'alt' => 'Foto del instalador — ' . $record->progname,
                ])
            @else
                <p class="text-sm text-gray-400">Sin foto del instalador.</p>
            @endif
        </div>

        @if (count($steps) > 0)
            <div class="space-y-6">
                <h4 class="text-sm font-semibold uppercase tracking-wide text-amber-400">
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

                    <div class="rounded-xl border border-gray-700 bg-gray-900/40 p-4 md:p-6">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-amber-500">
                            Paso {{ $stepNumber }}
                        </p>

                        @if (filled($title))
                            <h5 class="mb-4 text-lg font-semibold text-white">
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
                                    <div class="flex h-48 items-center justify-center rounded-lg border border-dashed border-gray-600 text-sm text-gray-500">
                                        Sin imagen
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-start">
                                @if (filled($description))
                                    <div class="prose prose-invert max-w-none text-sm text-gray-200">
                                        {!! Str::markdown($description) !!}
                                    </div>
                                @else
                                    <p class="text-sm text-gray-400">Sin descripción.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
