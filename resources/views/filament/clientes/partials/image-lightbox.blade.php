@props([
    'src',
    'alt' => '',
])

<div x-data="{ open: false }" {{ $attributes->class(['relative']) }}>
    <button
        type="button"
        @click="open = true"
        class="block w-full cursor-zoom-in text-left"
        aria-label="Ampliar imagen"
    >
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="w-full rounded-lg border border-gray-600 object-cover transition hover:opacity-90"
        />
    </button>

    <div
        x-show="open"
        x-transition.opacity
        x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4"
        role="dialog"
        aria-modal="true"
    >
        <button
            type="button"
            @click="open = false"
            class="absolute inset-0 cursor-zoom-out"
            aria-label="Cerrar imagen ampliada"
        ></button>

        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="relative z-10 max-h-[95vh] max-w-[95vw] rounded-lg object-contain shadow-2xl"
        />

        <button
            type="button"
            @click="open = false"
            class="absolute end-4 top-4 z-20 rounded-full bg-black/60 px-3 py-1 text-sm text-white hover:bg-black/80"
        >
            Cerrar
        </button>
    </div>
</div>
