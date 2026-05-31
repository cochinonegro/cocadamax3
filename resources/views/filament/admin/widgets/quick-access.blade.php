<x-filament::card class="rounded-xl border border-gray-700 bg-gray-900 p-4 transition-all duration-300 hover:border-primary-500 hover:shadow-lg">
    <h4 class="mb-3 text-lg font-bold text-cyan-400">Accesos rápidos</h4>
    <ul class="space-y-2">
        <li>
            <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center gap-2 text-sm text-cyan-400 transition hover:text-cyan-300">
                <x-heroicon-o-home class="h-4 w-4" />
                Escritorio
            </a>
        </li>
        <li>
            <a href="{{ route('filament.admin.resources.users.index') }}" class="flex items-center gap-2 text-sm text-cyan-400 transition hover:text-cyan-300">
                <x-heroicon-o-user-group class="h-4 w-4" />
                Usuarios
            </a>
        </li>
        <li>
            <a href="{{ route('filament.admin.resources.clientes.index') }}" class="flex items-center gap-2 text-sm text-cyan-400 transition hover:text-cyan-300">
                <x-heroicon-o-users class="h-4 w-4" />
                Clientes
            </a>
        </li>
        <li>
            <a href="{{ route('filament.admin.resources.programas.index') }}" class="flex items-center gap-2 text-sm text-cyan-400 transition hover:text-cyan-300">
                <x-heroicon-o-cpu-chip class="h-4 w-4" />
                Programas
            </a>
        </li>
        <li>
            <a href="mailto:soporte@cocadamax3.com" class="flex items-center gap-2 text-sm text-cyan-400 transition hover:text-cyan-300">
                <x-heroicon-o-envelope class="h-4 w-4" />
                Soporte técnico
            </a>
        </li>
        <li class="mt-2 border-t border-gray-800 pt-2">
            <a href="https://programas.space/clientes" target="_blank" class="flex items-center gap-2 text-sm text-purple-400 transition hover:text-purple-300">
                <x-heroicon-o-link class="h-4 w-4" />
                Catálogo clientes
            </a>
        </li>
    </ul>
</x-filament::card>
