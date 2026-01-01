<x-filament::card class="bg-gray-900 border border-gray-700 rounded-xl p-4 hover:border-primary-500 hover:shadow-lg transition-all duration-300">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="text-2xl text-gray-400">👤</div>
            <div>
                <h3 class="text-lg font-semibold text-white">Bienvenido</h3>
                <p class="text-gray-300 text-base font-medium">{{ Auth::user()?->name ?? 'Usuario' }}</p>
            </div>
        </div>
        <a href="{{ route('filament.admin.auth.logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-lg transition">
            <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
            Salir
        </a>
        <form id="logout-form" action="{{ route('filament.admin.auth.logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>
</x-filament::card>
