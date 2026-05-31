@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $steps = $record->installation_steps ?? [];
    $installerPhotoUrl = filled($record->foto_instalador)
        ? Storage::disk('public')->url($record->foto_instalador)
        : null;
@endphp

@if ($installerPhotoUrl)
    <div class="mb-6 rounded-xl border border-gray-700 bg-gray-900/40 p-4">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-amber-400">
            Imagen del instalador
        </h4>
        @include('filament.clientes.partials.image-lightbox', [
            'src' => $installerPhotoUrl,
            'alt' => 'Imagen del instalador — ' . $record->progname,
        ])
    </div>
@endif

@if (filled($record->info_install))
    <div class="mb-6 rounded-xl border border-gray-700 bg-gray-900/40 p-4">
        <h4 class="mb-2 text-sm font-semibold uppercase tracking-wide text-amber-400">
            Información sobre esta instalación
        </h4>
        <div class="prose prose-invert max-w-none text-sm text-gray-200">
            {!! Str::markdown($record->info_install) !!}
        </div>
    </div>
@endif

@if (count($steps) > 0)
    <div class="space-y-6">
        @foreach ($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $title = $step['title'] ?? null;
                $description = $step['description'] ?? ($step['text'] ?? null);
                $imagePath = $step['image'] ?? null;
                $imageUrl = filled($imagePath) ? Storage::disk('public')->url($imagePath) : null;
            @endphp

            <div class="rounded-xl border border-gray-700 bg-gray-900/40 p-4">
                <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-amber-500">
                    Paso {{ $stepNumber }}
                </p>

                @if (filled($title))
                    <h4 class="mb-3 text-lg font-semibold text-white">
                        {{ $title }}
                    </h4>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        @if ($imageUrl)
                            @include('filament.clientes.partials.image-lightbox', [
                                'src' => $imageUrl,
                                'alt' => 'Paso ' . $stepNumber . ' — ' . ($title ?? $record->progname),
                            ])
                        @else
                            <div class="flex h-40 items-center justify-center rounded-lg border border-dashed border-gray-600 text-sm text-gray-500">
                                Sin imagen
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center">
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
@elseif (! $installerPhotoUrl && blank($record->info_install))
    <p class="text-sm text-gray-400">No hay instrucciones de instalación para este producto.</p>
@endif
