@php
    $initialMode = null;

    if ($errors->any()) {
        if ($errors->has('login') || $errors->has('password') || filled(old('login'))) {
            $initialMode = 'login';
        } elseif ($errors->has('password_confirmation') || filled(old('password'))) {
            $initialMode = 'register';
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Programas — Bienvenida</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('welcomePortal', (initialMode) => ({
                mode: initialMode,
                ventaStep: 'pin',
                pin: '',
                pinError: '',
                formError: '',
                formErrors: {},
                sending: false,
                saving: false,
                successMessage: '',
                compradoPhone: '',
                compradoPrograma: '',
                compradoName: '',
                compradoError: '',
                compradoModal: false,
                lookingUp: false,
                venta: { name: '', phone: '', email: '', required_prog: '' },
                urls: {
                    verify: @js(route('welcome.venta-rapida.verificar')),
                    store: @js(route('welcome.venta-rapida.store')),
                    lookup: @js(route('welcome.venta-rapida.lookup')),
                    comprado: @js(route('welcome.venta-rapida.comprado')),
                },
                startVentaRapida() {
                    this.mode = 'venta';
                    this.ventaStep = 'pin';
                    this.pin = '';
                    this.pinError = '';
                    this.formError = '';
                    this.formErrors = {};
                    this.successMessage = '';
                    this.compradoPhone = '';
                    this.compradoPrograma = '';
                    this.compradoName = '';
                    this.compradoError = '';
                    this.compradoModal = false;
                    this.lookingUp = false;
                    this.sending = false;
                },
                async verifyPin() {
                    this.pinError = '';
                    this.sending = true;

                    try {
                        await this.postJson(this.urls.verify, { pin: String(this.pin || '').trim() });
                        this.ventaStep = 'choice';
                    } catch (error) {
                        this.pinError = error.message || 'El PIN no es correcto.';
                    } finally {
                        this.sending = false;
                    }
                },
                async lookupComprado() {
                    this.compradoError = '';
                    this.lookingUp = true;

                    try {
                        const data = await this.postJson(this.urls.lookup, { phone: this.compradoPhone });
                        this.compradoName = data.name || '';
                        this.compradoPrograma = '';
                        this.compradoModal = true;
                    } catch (error) {
                        this.compradoModal = false;

                        if (error.status === 404) {
                            this.venta.phone = this.compradoPhone;
                            this.formError = error.message || 'No estás en nuestra base de datos, por favor completa el formulario';
                            this.formErrors = {};
                            this.ventaStep = 'form';
                            return;
                        }

                        this.compradoError = error.message || 'No se pudo comprobar el teléfono.';
                    } finally {
                        this.lookingUp = false;
                    }
                },
                async submitComprado() {
                    this.compradoError = '';
                    this.saving = true;

                    try {
                        const data = await this.postJson(this.urls.comprado, { required_prog: this.compradoPrograma });
                        this.successMessage = data.message || 'Listo. El aviso ya se envió.';
                        this.compradoModal = false;
                        this.ventaStep = 'done';
                    } catch (error) {
                        this.compradoError = error.message || 'No se pudo enviar el aviso.';
                    } finally {
                        this.saving = false;
                    }
                },
                async submitVenta() {
                    this.formError = '';
                    this.formErrors = {};
                    this.saving = true;

                    try {
                        const data = await this.postJson(this.urls.store, this.venta);
                        this.successMessage = data.message || 'Datos guardados.';
                        this.ventaStep = 'done';
                    } catch (error) {
                        this.formError = error.message || 'No se pudieron guardar los datos.';
                        this.formErrors = error.errors || {};
                    } finally {
                        this.saving = false;
                    }
                },
                backHome() {
                    this.mode = null;
                    this.ventaStep = 'pin';
                    this.pin = '';
                    this.pinError = '';
                    this.formError = '';
                    this.formErrors = {};
                    this.successMessage = '';
                    this.compradoPhone = '';
                    this.compradoPrograma = '';
                    this.compradoName = '';
                    this.compradoError = '';
                    this.compradoModal = false;
                    this.lookingUp = false;
                    this.venta = { name: '', phone: '', email: '', required_prog: '' };
                },
                async postJson(url, body) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(body),
                    });

                    let data = {};

                    try {
                        data = await response.json();
                    } catch (e) {
                        data = {};
                    }

                    if (! response.ok) {
                        if (response.status === 429) {
                            throw Object.assign(new Error('Demasiados intentos. Espera un momento y vuelve a intentarlo.'), { errors: {} });
                        }

                        const firstField = data.errors ? Object.values(data.errors).flat()[0] : null;
                        throw Object.assign(new Error(firstField || data.message || 'No se pudo completar la acción.'), {
                            errors: data.errors || {},
                            status: response.status,
                        });
                    }

                    return data;
                },
            }));
        });
    </script>
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
        x-data="welcomePortal(@js($initialMode))"
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
                x-on:click="startVentaRapida()"
                class="w-full rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-6 py-5 text-left transition hover:border-emerald-400 hover:bg-emerald-500/20"
            >
                <span class="block text-lg font-semibold tracking-wide text-emerald-300">VENTA RAPIDA</span>
                <span class="mt-1 block text-sm text-zinc-400">Registrar cliente con un PIN</span>
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

        {{-- Venta rápida --}}
        <section
            x-show="mode === 'venta'"
            x-cloak
            class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6 shadow-xl"
        >
            <button
                type="button"
                x-on:click="backHome()"
                class="mb-4 inline-flex items-center gap-1 text-sm text-zinc-400 transition hover:text-emerald-300"
            >
                ← Volver
            </button>

            <h2 class="mb-1 text-lg font-semibold text-white">VENTA RAPIDA</h2>

            <div x-show="ventaStep === 'pin'" class="space-y-4">
                <p class="text-sm text-zinc-400">Introduce el PIN de 4 cifras.</p>
                <p x-show="pinError" x-text="pinError" class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-300"></p>

                <form x-on:submit.prevent="verifyPin()" class="space-y-4">
                    <div>
                        <label for="venta_pin" class="mb-1 block text-sm font-medium text-zinc-300">PIN <span class="text-rose-400">*</span></label>
                        <input
                            id="venta_pin"
                            x-model="pin"
                            type="text"
                            inputmode="numeric"
                            maxlength="4"
                            autocomplete="one-time-code"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-center text-lg tracking-[0.4em] text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                            placeholder="••••"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:bg-zinc-800 disabled:text-zinc-500"
                        x-bind:disabled="sending || String(pin).length !== 4"
                    >
                        Continuar
                    </button>
                </form>
            </div>

            <div x-show="ventaStep === 'choice'" class="space-y-4">
                <p class="text-sm text-zinc-400">Elige cómo continuar.</p>

                <button
                    type="button"
                    x-on:click="ventaStep = 'form'"
                    class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400"
                >
                    Completar datos
                </button>

                <button
                    type="button"
                    x-on:click="ventaStep = 'comprado'; compradoError = ''"
                    class="w-full rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-2.5 text-sm font-semibold tracking-wide text-emerald-300 transition hover:bg-emerald-500/20"
                >
                    YA HE COMPRADO
                </button>
            </div>

            <div x-show="ventaStep === 'comprado'" class="space-y-4">
                <p class="text-sm text-zinc-400">Escribe el teléfono con el que ya estás registrado.</p>
                <p x-show="compradoError && !compradoModal" x-text="compradoError" class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-300"></p>

                <form x-on:submit.prevent="lookupComprado()" class="space-y-4">
                    <div>
                        <label for="comprado_phone" class="mb-1 block text-sm font-medium text-zinc-300">Teléfono <span class="text-rose-400">*</span></label>
                        <input
                            id="comprado_phone"
                            x-model="compradoPhone"
                            type="tel"
                            required
                            maxlength="30"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                            placeholder="Ej: 612345678"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:bg-zinc-800 disabled:text-zinc-500"
                        x-bind:disabled="lookingUp"
                    >
                        <span x-show="!lookingUp">Continuar</span>
                        <span x-show="lookingUp">Buscando…</span>
                    </button>
                </form>

                <button
                    type="button"
                    x-on:click="ventaStep = 'choice'"
                    class="w-full text-sm text-zinc-400 underline-offset-2 transition hover:text-emerald-300 hover:underline"
                >
                    Volver a las opciones
                </button>
            </div>

            <div x-show="ventaStep === 'form'" class="space-y-4">
                <p class="mb-5 text-sm text-zinc-400">Completa los datos del cliente.</p>

                <button
                    type="button"
                    x-on:click="ventaStep = 'comprado'; compradoError = ''"
                    class="w-full rounded-lg border border-emerald-500/40 px-4 py-2.5 text-sm font-semibold tracking-wide text-emerald-300 transition hover:bg-emerald-500/10"
                >
                    YA HE COMPRADO
                </button>

                <p x-show="formError && !Object.keys(formErrors).length" x-text="formError" class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-300"></p>

                <form x-on:submit.prevent="submitVenta()" class="space-y-4">
                    <div>
                        <label for="venta_name" class="mb-1 block text-sm font-medium text-zinc-300">Nombre <span class="text-rose-400">*</span></label>
                        <input
                            id="venta_name"
                            x-model="venta.name"
                            type="text"
                            required
                            maxlength="255"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                            placeholder="Nombre del cliente"
                        >
                        <p x-show="formErrors.name" x-text="(formErrors.name || [])[0]" class="mt-1 text-xs text-rose-300"></p>
                    </div>

                    <div>
                        <label for="venta_phone" class="mb-1 block text-sm font-medium text-zinc-300">Teléfono <span class="text-rose-400">*</span></label>
                        <input
                            id="venta_phone"
                            x-model="venta.phone"
                            type="tel"
                            required
                            maxlength="30"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                            placeholder="Ej: 612345678"
                        >
                        <p x-show="formErrors.phone" x-text="(formErrors.phone || [])[0]" class="mt-1 text-xs text-rose-300"></p>
                    </div>

                    <div>
                        <label for="venta_email" class="mb-1 block text-sm font-medium text-zinc-300">Correo <span class="text-rose-400">*</span></label>
                        <input
                            id="venta_email"
                            x-model="venta.email"
                            type="email"
                            required
                            maxlength="255"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                            placeholder="correo@ejemplo.com"
                        >
                        <p x-show="formErrors.email" x-text="(formErrors.email || [])[0]" class="mt-1 text-xs text-rose-300"></p>
                    </div>

                    <div>
                        <label for="venta_programa" class="mb-1 block text-sm font-medium text-zinc-300">Programa solicitado <span class="text-rose-400">*</span></label>
                        <input
                            id="venta_programa"
                            x-model="venta.required_prog"
                            type="text"
                            required
                            maxlength="255"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                            placeholder="Ej: Photoshop"
                        >
                        <p x-show="formErrors.required_prog" x-text="(formErrors.required_prog || [])[0]" class="mt-1 text-xs text-rose-300"></p>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:bg-zinc-800 disabled:text-zinc-500"
                        x-bind:disabled="saving"
                    >
                        <span x-show="!saving">Guardar cliente</span>
                        <span x-show="saving">Guardando…</span>
                    </button>
                </form>
            </div>

            <div
                x-show="compradoModal"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4"
            >
                <div class="w-full max-w-md rounded-2xl border border-zinc-700 bg-zinc-900 p-6 shadow-2xl" x-on:click.outside="compradoModal = false">
                    <h3 class="text-lg font-semibold text-white">Correcto, ya estás. Qué programa quieres?</h3>
                    <p class="mt-2 text-sm text-zinc-400" x-show="compradoName" x-text="compradoName"></p>
                    <p x-show="compradoError" x-text="compradoError" class="mt-3 rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-300"></p>

                    <form x-on:submit.prevent="submitComprado()" class="mt-5 space-y-4">
                        <div>
                            <label for="comprado_programa" class="mb-1 block text-sm font-medium text-zinc-300">Programa <span class="text-rose-400">*</span></label>
                            <input
                                id="comprado_programa"
                                x-model="compradoPrograma"
                                type="text"
                                required
                                maxlength="255"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30"
                                placeholder="Ej: Photoshop"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:bg-zinc-800 disabled:text-zinc-500"
                            x-bind:disabled="saving"
                        >
                            <span x-show="!saving">Enviar</span>
                            <span x-show="saving">Enviando…</span>
                        </button>
                    </form>
                </div>
            </div>

            <div x-show="ventaStep === 'done'" class="space-y-4">
                <p class="rounded-lg border border-green-500/30 bg-green-500/10 px-3 py-2 text-sm text-green-300" x-text="successMessage"></p>
                <button
                    type="button"
                    x-on:click="backHome()"
                    class="w-full rounded-lg border border-zinc-700 px-4 py-2.5 text-sm font-semibold text-zinc-200 transition hover:border-zinc-500"
                >
                    Volver al inicio
                </button>
            </div>
        </section>

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
            <p class="mb-5 text-sm text-zinc-400">Elige una clave personal para tu cuenta (mínimo 4 caracteres).</p>

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

                <div>
                    <label for="register_password" class="mb-1 block text-sm font-medium text-zinc-300">Clave personal <span class="text-rose-400">*</span></label>
                    <input
                        id="register_password"
                        name="password"
                        type="password"
                        required
                        minlength="4"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="Tu clave personal"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-zinc-300">Confirmar clave <span class="text-rose-400">*</span></label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        minlength="4"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
                        placeholder="Repite tu clave"
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
                Introduce tu <strong class="text-zinc-300">correo</strong> o tu <strong class="text-zinc-300">teléfono</strong> como usuario.
                Puedes usar tu <strong class="text-zinc-300">clave personal</strong> o la <strong class="text-zinc-300">clave general de acceso</strong>.
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
