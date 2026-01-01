<x-filament::card class="bg-gray-900 border border-gray-700 rounded-xl p-4 hover:border-primary-500 hover:shadow-lg transition-all duration-300">
    <h4 class="font-bold text-cyan-400 mb-3 text-lg">🚀 Accesos Rápidos</h4>
    <ul class="space-y-2">
        <li>
            <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition text-sm">
                <x-heroicon-o-home class="w-4 h-4" />
                Dashboard Principal
            </a>
        </li>
        <li>
            <a href="{{ route('filament.admin.resources.users.index') }}" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition text-sm">
                <x-heroicon-o-user-group class="w-4 h-4" />
                Gestión de Usuarios
            </a>
        </li>
        <li>
            <a href="https://cocadamax3.com/ayuda" target="_blank" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition text-sm">
                <x-heroicon-o-question-mark-circle class="w-4 h-4" />
                Ayuda y Documentación
            </a>
        </li>
        <li>
            <a href="mailto:soporte@cocadamax3.com" class="flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition text-sm">
                <x-heroicon-o-envelope class="w-4 h-4" />
                Soporte Técnico
            </a>
        </li>
        <li class="pt-2 border-t border-gray-800 mt-2">
            <a href="https://cocadamax3.com/mac" target="_blank" class="flex items-center gap-2 text-purple-400 hover:text-purple-300 transition text-sm">
                <x-heroicon-o-link class="w-4 h-4" />
                Ver Productos de Mac
            </a>
        </li>
        <li>
            <a href="https://cocadamax3.com/windows" target="_blank" class="flex items-center gap-2 text-blue-400 hover:text-blue-300 transition text-sm">
                <x-heroicon-o-link class="w-4 h-4" />
                Ver Productos de Windows
            </a>
        </li>
    </ul>
</x-filament::card>
