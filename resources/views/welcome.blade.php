@php
    $initialMode = null;

    if ($errors->any()) {
        if ($errors->has('login') || $errors->has('password') || filled(old('login'))) {
            $initialMode = 'login';
        } else {
            $initialMode = 'register';
        }
    }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Programas — Bienvenida</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
    <div
        class="mx-auto flex min-h-screen max-w-lg flex-col justify-center px-4 py-10 sm:px-6"
        x-data="{ mode: @js($initialMode) }"
    >
        <header class="mb-10 text-center">
            <p class="mb-2 text-sm font-medium uppercase tracking-[0.2em] text-amber-400">CocadaMax</p>
            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Catálogo de Programas</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm text-zinc-400" x-show="mode === null">
                Elige cómo deseas continuar para acceder al catálogo.
            </p>
        </header>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Opciones iniciales --}}
        <div x-show="mode === null" x-cloak class="space-y-4">
            <button
                type="button"
                x-on:click="mode = 'register'"
                class="w-full rounded-2xl border border-amber-500/40 bg-amber-500/10 px-6 py-5 text-left transition hover:border-amber-500 hover:bg-amber-500/20"
            >
                <span class="block text-lg font-semibold text-amber-300">Ingresar por primera vez</span>
                <span class="mt-1 block text-sm text-zinc-400">Crear usuario</span>
            </button>

            <button
                type="button"
                x-on:click="mode = 'login'"
                class="w-full rounded-2xl border border-zinc-700 bg-zinc-900/70 px-6 py-5 text-left transition hover:border-zinc-500 hover:bg-zinc-900"
            >
                <span class="block text-lg font-semibold text-white">Iniciar sesión</span>
                <span class="mt-1 block text-sm text-zinc-400">Ya tengo cuenta</span>
            </button>
        </div>

        {{-- Crear usuario --}}
        <section
            x-show="mode === 'register'"
            x-cloak
            class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6 shadow-xl"
        >
            <button
                type="button"
                x-on:click="mode = null"
                class="mb-4 inline-flex items-center gap-1 text-sm text-zinc-400 transition hover:text-amber-300"
            >
                ← Volver
            </button>

            <h2 class="mb-1 text-lg font-semibold text-white">Ingresar por primera vez</h2>
            <p class="mb-5 text-sm text-zinc-400">Regístrate para acceder al catálogo. Tu clave será tu teléfono.</p>

            <form action="{{ route('welcome.register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-zinc-300">Nombre <span class="text-rose-400">*</span></label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="Tu nombre"
                    >
                </div>

                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-zinc-300">Teléfono <span class="text-rose-400">*</span></label>
                    <input
                        id="phone"
                        name="phone"
                        type="text"
                        value="{{ old('phone') }}"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="Ej: 612345678"
                    >
                </div>

                <div>
                    <label for="register_email" class="mb-1 block text-sm font-medium text-zinc-300">Correo <span class="text-rose-400">*</span></label>
                    <input
                        id="register_email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="correo@ejemplo.com"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-amber-400"
                >
                    Crear usuario
                </button>
            </form>
        </section>

        {{-- Iniciar sesión --}}
        <section
            x-show="mode === 'login'"
            x-cloak
            class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6 shadow-xl"
        >
            <button
                type="button"
                x-on:click="mode = null"
                class="mb-4 inline-flex items-center gap-1 text-sm text-zinc-400 transition hover:text-amber-300"
            >
                ← Volver
            </button>

            <h2 class="mb-1 text-lg font-semibold text-white">Iniciar sesión</h2>
            <p class="mb-5 text-sm text-zinc-400">
                Introduce tu <strong class="text-zinc-300">correo</strong> o tu <strong class="text-zinc-300">teléfono</strong> como usuario,
                y tu clave personal para acceder.
            </p>

            <form action="{{ route('welcome.login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="login" class="mb-1 block text-sm font-medium text-zinc-300">
                        Usuario <span class="text-rose-400">*</span>
                    </label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        value="{{ old('login') }}"
                        required
                        autocomplete="username"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="Correo o teléfono"
                    >
                    <p class="mt-1 text-xs text-zinc-500">Puedes usar tu correo o tu número de teléfono.</p>
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-zinc-300">Clave <span class="text-rose-400">*</span></label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="Tu clave personal"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-zinc-400">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="rounded border-zinc-600 bg-zinc-950 text-amber-500 focus:ring-amber-500/30"
                        @checked(old('remember'))
                    >
                    Recordarme
                </label>

                <button
                    type="submit"
                    class="w-full rounded-lg border border-amber-500/40 bg-amber-500/10 px-4 py-2.5 text-sm font-semibold text-amber-300 transition hover:bg-amber-500/20"
                >
                    Iniciar sesión
                </button>
            </form>
        </section>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
