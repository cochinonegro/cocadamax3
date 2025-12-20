<x-filament::card class="bg-gray-900 border border-gray-700">
    {{-- Sección 1: Información del usuario --}}
    <div class="mb-5">
        <h3 class="text-lg font-bold text-red-400 mb-2">👤 Bienvenido</h2>
        <p class="text-gray-300">
            <strong>{{ Auth::user()?->name ?? 'Invitado' }}</strong><br>
            <span class="text-sm text-gray-500">{{ Auth::user()?->email ?? 'Sin correo' }}</span>
        </p>

        <div class="mt-3">
            <a
                href="{{ route('filament.admin.auth.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition"
            >
                <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
                Salir
            </a>

            <form id="logout-form" action="{{ route('filament.admin.auth.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    {{-- Separador visual --}}
    <div class="border-t border-gray-700 my-4"></div>

    {{-- Sección 2: Accesos rápidos --}}
    <div>
        <h4 class="font-bold text-cyan-400 mb-3">🚀 Accesos Rápidos</h4>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition">
                    <x-heroicon-o-home class="w-4 h-4" />
                    Dashboard Principal
                </a>
            </li>
            <li>
                <a href="{{ route('filament.admin.resources.users.index') }}" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition">
                    <x-heroicon-o-user-group class="w-4 h-4" />
                    Gestión de Usuarios
                </a>
            </li>
            <li>
                <a href="https://cocadamax3.com/ayuda" target="_blank" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition">
                    <x-heroicon-o-question-mark-circle class="w-4 h-4" />
                    Ayuda y Documentación
                </a>
            </li>
            <li>
                <a href="mailto:soporte@cocadamax3.com" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition">
                    <x-heroicon-o-envelope class="w-4 h-4" />
                    Soporte Técnico
                </a>
            </li>
        </ul>
    </div>
</x-filament::card>
