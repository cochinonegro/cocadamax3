<x-filament-panels::page>
    <x-tienda.shell :step="1">
        <p class="tienda-shell__lead">Elige tu plataforma para ver programas compatibles.</p>

        <div class="tienda-os-grid">
            <button
                type="button"
                wire:click="elegirOs('windows')"
                class="tienda-os-btn tienda-os-btn--windows"
            >
                <span class="tienda-os-btn__icon">
                    <x-heroicon-o-computer-desktop class="h-9 w-9" />
                </span>
                <span class="tienda-os-btn__label">Windows</span>
                <span class="tienda-os-btn__hint">PC · Laptop</span>
            </button>

            <button
                type="button"
                wire:click="elegirOs('mac')"
                class="tienda-os-btn tienda-os-btn--mac"
            >
                <span class="tienda-os-btn__icon">
                    <x-heroicon-o-device-tablet class="h-9 w-9" />
                </span>
                <span class="tienda-os-btn__label">Mac</span>
                <span class="tienda-os-btn__hint">MacBook · iMac</span>
            </button>
        </div>
    </x-tienda.shell>
</x-filament-panels::page>
