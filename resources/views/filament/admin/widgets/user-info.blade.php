<x-filament::card class="rounded-xl border border-gray-700 bg-gray-900 p-4 transition-all duration-300 hover:border-primary-500 hover:shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="text-2xl text-gray-400">👤</div>
            <div>
                <h3 class="text-lg font-semibold text-white">Bienvenido</h3>
                <p class="text-base font-medium text-gray-300">{{ Auth::user()?->name ?? 'Administrador' }}</p>
            </div>
        </div>
        <a href="{{ route('filament.admin.auth.logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="inline-flex items-center gap-2 rounded-lg bg-gray-800 px-3 py-1.5 text-sm text-white transition hover:bg-gray-700">
            <x-heroicon-o-arrow-left-on-rectangle class="h-4 w-4" />
            Cerrar sesión
        </a>
        <form id="logout-form" action="{{ route('filament.admin.auth.logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>
</x-filament::card>
