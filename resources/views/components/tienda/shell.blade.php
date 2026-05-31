@props([
    'step' => 1,
    'badge' => null,
])

@php
    use App\Filament\Support\TiendaPresentation;

    $steps = TiendaPresentation::steps($step);
@endphp

<div {{ $attributes->merge(['class' => 'tienda-shell']) }}>
    <div class="tienda-shell__inner">
        <nav class="tienda-steps" aria-label="Progreso de la tienda">
            @foreach ($steps as $index => $item)
                @if ($index > 0)
                    <span @class(['tienda-steps__line', 'is-done' => $item['done'] || $item['active']]) aria-hidden="true"></span>
                @endif

                <div @class([
                    'tienda-steps__item',
                    'is-active' => $item['active'],
                    'is-done' => $item['done'],
                ])>
                    <span class="tienda-steps__dot">{{ $index + 1 }}</span>
                    <span class="tienda-steps__label">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </nav>

        @if ($badge)
            <p class="tienda-shell__badge">{{ $badge }}</p>
        @endif

        {{ $slot }}
    </div>
</div>
