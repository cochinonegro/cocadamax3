<x-filament-panels::page>
    <div class="tienda-flow mx-auto max-w-3xl">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <button
                type="button"
                wire:click="elegirOs('windows')"
                class="tienda-choice-btn tienda-choice-btn--windows"
            >
                <x-heroicon-o-computer-desktop class="h-10 w-10" />
                <span>Windows</span>
            </button>

            <button
                type="button"
                wire:click="elegirOs('mac')"
                class="tienda-choice-btn tienda-choice-btn--mac"
            >
                <x-heroicon-o-device-tablet class="h-10 w-10" />
                <span>Mac</span>
            </button>
        </div>
    </div>
</x-filament-panels::page>
