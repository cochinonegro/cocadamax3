@php
    $isCard = $variant === 'card';
    $isDetail = $variant === 'detail';
@endphp

<div
    @class([
        'inline-flex',
        'w-full justify-center' => $isCard,
    ])
    @if ($isCard)
        onclick="event.preventDefault(); event.stopPropagation();"
    @endif
>
    @if ($status === 'en_pedidos')
        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset bg-green-500/15 text-green-300 ring-green-500/30">
            En Pedidos
        </span>
    @elseif ($status === 'pendiente')
        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset bg-amber-500/15 text-amber-300 ring-amber-500/30">
            Pendiente
        </span>
    @else
        <button
            type="button"
            wire:click="solicitar"
            wire:loading.attr="disabled"
            wire:target="solicitar"
            @class([
                'inline-flex items-center justify-center gap-1 rounded-lg font-semibold transition focus:outline-none focus:ring-2 disabled:opacity-60',
                'tienda-solicitar-btn px-3 py-2 text-sm w-full uppercase tracking-wide' => $isCard,
                'px-4 py-2 text-sm border border-amber-500/50 bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 focus:ring-amber-500/40' => $isDetail,
            ])
        >
            <span wire:loading.remove wire:target="solicitar">{{ $isCard ? 'SOLICITAR YA' : 'Solicitar' }}</span>
            <span wire:loading wire:target="solicitar">Enviando…</span>
        </button>
    @endif
</div>
