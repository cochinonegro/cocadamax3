<x-filament::page>
    <div class="space-y-6">
        @livewire(\App\Filament\Admin\Widgets\UserInfoWidget::class)
        @livewire(\App\Filament\Admin\Widgets\QuickAccessWidget::class)
        @livewire(\App\Filament\Admin\Widgets\MacLinksWidget::class)
        @livewire(\App\Filament\Admin\Widgets\WindowsLinksWidget::class)
    </div>
</x-filament::page>
