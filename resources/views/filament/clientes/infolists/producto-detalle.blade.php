@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $images = $record->gallery_images ?? [];

    $codigo = str_pad($record->id, 4, '0', STR_PAD_LEFT);
    $codigo = substr($codigo, 0, 2) . ' ' . substr($codigo, 2, 2);

    $osLabel = match ($record->os_required) {
        'windows' => 'Windows',
        'mac' => 'Mac',
        'win-mac' => 'Win & Mac',
        default => strtoupper((string) $record->os_required),
    };
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Galería izquierda --}}
    <div class="space-y-3">
        @forelse ($images as $image)
            <img
                src="{{ Storage::disk('public')->url($image) }}"
                alt="{{ $record->progname }}"
                class="w-full rounded-xl border border-gray-700 object-cover"
            />
        @empty
            <div class="flex h-48 items-center justify-center rounded-xl border border-dashed border-gray-600 bg-gray-900/50 text-sm text-gray-400">
                Sin imágenes del producto
            </div>
        @endforelse
    </div>

    {{-- Info derecha --}}
    <div class="space-y-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-400">Programa</p>
            <h2 class="text-2xl font-bold text-white">{{ $record->progname }}</h2>
        </div>

        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-400">Código</dt>
                <dd class="font-medium text-white">{{ $codigo }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Tamaño</dt>
                <dd class="font-medium text-white">{{ $record->size }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Sistema operativo</dt>
                <dd class="font-medium text-white">{{ $osLabel }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Categoría</dt>
                <dd class="font-medium text-white">{{ ucfirst($record->category) }}</dd>
            </div>
            @if ($record->year_prog)
                <div>
                    <dt class="text-gray-400">Año</dt>
                    <dd class="font-medium text-white">{{ $record->year_prog }}</dd>
                </div>
            @endif
            @if ($record->working)
                <div>
                    <dt class="text-gray-400">Subcategoría</dt>
                    <dd class="font-medium text-white">{{ $record->working }}</dd>
                </div>
            @endif
        </dl>

        <div class="border-t border-gray-700 pt-4">
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-amber-400">Descripción</h3>
            @if (filled($record->description))
                <div class="prose prose-invert max-w-none text-sm text-gray-200">
                    {!! Str::markdown($record->description) !!}
                </div>
            @else
                <p class="text-sm text-gray-400">Sin descripción disponible.</p>
            @endif
        </div>
    </div>
</div>
