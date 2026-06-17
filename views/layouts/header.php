<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Panel') ?> — BarberApp</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        barber: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="h-full bg-stone-100" x-data="{ sidebarOpen: false }">

<!-- ── Overlay móvil ────────────────────────────────────── -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/50 md:hidden"
     style="display:none;">
</div>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-zinc-900 flex flex-col
              transform transition-transform duration-300 ease-in-out
              -translate-x-full md:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : ''">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-zinc-800 flex-shrink-0">
        <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="scissors" class="w-5 h-5 text-white"></i>
        </div>
        <div>
            <span class="text-white font-bold text-sm leading-tight block">BarberApp</span>
            <span class="text-zinc-500 text-xs">Panel de Administración</span>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        <!-- Dashboard -->
        <a href="<?= url('dashboard') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  <?= ($activeNav ?? '') === 'dashboard' ? 'bg-amber-500 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' ?>">
            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
            Dashboard
        </a>

        <!-- Sección: Atención -->
        <div class="pt-4 pb-1 px-3">
            <span class="text-zinc-600 text-xs font-semibold uppercase tracking-wider">Atención</span>
        </div>

        <a href="<?= url('citas') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  <?= ($activeNav ?? '') === 'citas' ? 'bg-amber-500 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' ?>">
            <i data-lucide="calendar-days" class="w-4 h-4 flex-shrink-0"></i>
            <span class="flex-1">Agenda de Citas</span>
            <span class="text-xs bg-zinc-800 text-zinc-500 px-1.5 py-0.5 rounded font-normal">F4</span>
        </a>

        <a href="<?= url('espera') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  <?= ($activeNav ?? '') === 'espera' ? 'bg-amber-500 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' ?>">
            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
            <span class="flex-1">Lista de Espera</span>
            <span class="text-xs bg-zinc-800 text-zinc-500 px-1.5 py-0.5 rounded font-normal">F4</span>
        </a>

        <!-- Enlace página pública -->
        <a href="<?= url('reservar') ?>" target="_blank"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  text-zinc-400 hover:bg-zinc-800 hover:text-white">
            <i data-lucide="external-link" class="w-4 h-4 flex-shrink-0"></i>
            <span class="flex-1">Página de Reservas</span>
        </a>

        <!-- Sección: Gestión -->
        <div class="pt-4 pb-1 px-3">
            <span class="text-zinc-600 text-xs font-semibold uppercase tracking-wider">Gestión</span>
        </div>

        <a href="<?= url('servicios') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  <?= ($activeNav ?? '') === 'servicios' ? 'bg-amber-500 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' ?>">
            <i data-lucide="scissors" class="w-4 h-4 flex-shrink-0"></i>
            <span class="flex-1">Servicios</span>
            <span class="text-xs bg-zinc-800 text-zinc-500 px-1.5 py-0.5 rounded font-normal">F2</span>
        </a>

        <a href="<?= url('clientes') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  <?= ($activeNav ?? '') === 'clientes' ? 'bg-amber-500 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' ?>">
            <i data-lucide="heart-handshake" class="w-4 h-4 flex-shrink-0"></i>
            <span class="flex-1">Clientes Frecuentes</span>
        </a>

        <!-- Sección: Análisis -->
        <div class="pt-4 pb-1 px-3">
            <span class="text-zinc-600 text-xs font-semibold uppercase tracking-wider">Análisis</span>
        </div>

        <a href="<?= url('reportes') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  <?= ($activeNav ?? '') === 'reportes' ? 'bg-amber-500 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' ?>">
            <i data-lucide="bar-chart-3" class="w-4 h-4 flex-shrink-0"></i>
            <span class="flex-1">Reportes</span>
        </a>

    </nav>

    <!-- Usuario + Logout -->
    <div class="border-t border-zinc-800 px-3 py-3 flex-shrink-0">
        <div class="flex items-center gap-3 px-3 py-2 mb-1">
            <div class="w-8 h-8 bg-amber-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i data-lucide="user" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-white text-sm font-medium truncate">
                    <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?>
                </div>
                <div class="text-zinc-500 text-xs">Administrador</div>
            </div>
        </div>
        <a href="<?= url('logout') ?>"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                  text-zinc-400 hover:bg-red-950 hover:text-red-400 transition-colors w-full">
            <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i>
            Cerrar sesión
        </a>
    </div>

</aside>

<!-- ── Contenedor principal ──────────────────────────────── -->
<div class="md:ml-64 flex flex-col min-h-screen">

    <!-- Top navbar -->
    <header class="sticky top-0 z-10 bg-white border-b border-stone-200 px-4 py-3 flex items-center gap-4 shadow-sm">
        <!-- Hamburger (solo móvil) -->
        <button @click="sidebarOpen = !sidebarOpen"
                class="md:hidden p-1.5 rounded-lg text-zinc-500 hover:bg-stone-100 -ml-1">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <!-- Título de página -->
        <h1 class="text-sm font-semibold text-zinc-800 flex-1">
            <?= htmlspecialchars($pageTitle ?? 'Panel') ?>
        </h1>

        <!-- Fecha -->
        <span class="hidden sm:block text-xs text-zinc-400">
            <?= fechaEsp('', 'completa') ?>
        </span>
    </header>

    <!-- Contenido de la página -->
    <main class="flex-1 p-4 md:p-6">
