<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar — BarberApp</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="h-full bg-zinc-900 flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    <!-- Stripe barbería -->
    <div class="h-1.5 rounded-t-xl overflow-hidden flex">
        <div class="flex-1 bg-red-600"></div>
        <div class="flex-1 bg-white"></div>
        <div class="flex-1 bg-blue-500"></div>
        <div class="flex-1 bg-white"></div>
        <div class="flex-1 bg-red-600"></div>
    </div>

    <!-- Card login -->
    <div class="bg-white rounded-b-xl shadow-2xl p-8">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-lg shadow-blue-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="6" cy="6" r="3"/><path d="M8.12 8.12 12 12"/><path d="M20 4 8.12 15.88"/>
                    <circle cx="6" cy="18" r="3"/><path d="M14.8 14.8 20 20"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900">BarberApp</h1>
            <p class="text-zinc-500 text-sm mt-1">Panel de Administración</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 text-sm mb-5 flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" x-data="{ showPass: false }">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <!-- Usuario -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-zinc-700 mb-1.5" for="usuario">
                    Usuario
                </label>
                <input id="usuario" type="text" name="usuario" autocomplete="username"
                       value="<?= htmlspecialchars($usuario ?? '') ?>"
                       class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              transition-shadow"
                       placeholder="Ingresa tu usuario" autofocus required>
            </div>

            <!-- Contraseña -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-zinc-700 mb-1.5" for="password">
                    Contraseña
                </label>
                <div class="relative">
                    <input id="password" :type="showPass ? 'text' : 'password'" name="password"
                           autocomplete="current-password"
                           class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 pr-10 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                  transition-shadow"
                           placeholder="••••••••" required>
                    <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition-colors">
                        <svg x-show="!showPass" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg x-show="showPass" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 px-4
                           rounded-lg transition-colors shadow-lg shadow-blue-500/25 active:scale-95">
                Ingresar al sistema
            </button>
        </form>

    </div>

    <!-- Credenciales de prueba -->
    <div class="mt-4 rounded-xl border border-zinc-700/60 bg-zinc-800/60 px-4 py-3"
         x-data="{ mostrar: false }">
        <button type="button" @click="mostrar = !mostrar"
                class="w-full flex items-center justify-between text-xs text-zinc-400 hover:text-zinc-200 transition-colors">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Credenciales de prueba
            </span>
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="mostrar ? 'rotate-180' : ''"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>

        <div x-show="mostrar" x-transition class="mt-3 space-y-1.5" style="display:none;">
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Usuario</span>
                <button type="button"
                        onclick="document.getElementById('usuario').value='admin'"
                        class="font-mono text-blue-400 hover:text-blue-300 transition-colors cursor-pointer">
                    admin
                </button>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Contraseña</span>
                <button type="button"
                        onclick="document.getElementById('password').value='Admin123'"
                        class="font-mono text-blue-400 hover:text-blue-300 transition-colors cursor-pointer">
                    Admin123
                </button>
            </div>
            <p class="text-zinc-600 text-xs pt-1">Clic en cada valor para autocompletar</p>
        </div>
    </div>

    <p class="text-center text-zinc-600 text-xs mt-3">
        BarberApp v<?= APP_VERSION ?> &nbsp;·&nbsp; Sistema de Gestión de Barberías
    </p>
</div>

<!-- Lucide -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>lucide.createIcons();</script>

</body>
</html>
