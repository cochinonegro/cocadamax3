@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $steps = $record->installation_steps ?? [];
@endphp

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

@if (count($steps) === 0 && blank($record->info_install))
    <p class="text-sm text-gray-400">No hay instrucciones de instalación para este producto.</p>
@elseif (count($steps) > 0)
    <div class="space-y-6">
        @foreach ($steps as $index => $step)
            <div class="grid grid-cols-1 gap-4 rounded-xl border border-gray-700 bg-gray-900/40 p-4 md:grid-cols-2">
                <div>
                    @if (! empty($step['image']))
                        <img
                            src="{{ Storage::disk('public')->url($step['image']) }}"
                            alt="Instalación paso {{ $index + 1 }}"
                            class="w-full rounded-lg border border-gray-600 object-cover"
                        />
                    @else
                        <div class="flex h-40 items-center justify-center rounded-lg border border-dashed border-gray-600 text-sm text-gray-500">
                            Sin imagen
                        </div>
                    @endif
                </div>
                <div class="flex items-center">
                    @if (! empty($step['text']))
                        <div class="prose prose-invert max-w-none text-sm text-gray-200">
                            {!! Str::markdown($step['text']) !!}
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Sin instrucciones.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
