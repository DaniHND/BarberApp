<?php
$hoy = date('d/m/Y');
?>

<!-- ── Stat cards ──────────────────────────────────────── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">Citas hoy</p>
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                <i data-lucide="calendar-check" class="w-4 h-4 text-blue-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900">0</p>
        <p class="text-xs text-zinc-400 mt-1">Sin citas programadas</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">En espera</p>
            <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center">
                <i data-lucide="clock" class="w-4 h-4 text-purple-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900">0</p>
        <p class="text-xs text-zinc-400 mt-1">Lista de espera vacía</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">Servicios activos</p>
            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center">
                <i data-lucide="scissors" class="w-4 h-4 text-amber-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900">5</p>
        <p class="text-xs text-zinc-400 mt-1">Configurados en BD</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">Ingresos hoy</p>
            <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                <i data-lucide="banknote" class="w-4 h-4 text-green-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900">L. 0</p>
        <p class="text-xs text-zinc-400 mt-1">Sin ingresos registrados</p>
    </div>

</div>

<!-- ── Sistema listo ─────────────────────────────────────── -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 mb-6">
    <div class="flex items-start gap-4">
        <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-500/25">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-white"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-sm font-bold text-zinc-900">Fase 1 completada — Sistema listo</h2>
                <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">✓ Activo</span>
            </div>
            <p class="text-xs text-zinc-500 mt-1">
                La arquitectura MVC está instalada, la base de datos con 8 tablas fue creada y el login del administrador está funcionando.
            </p>

            <!-- Próximas fases -->
            <div class="mt-4 grid sm:grid-cols-3 gap-3">
                <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
                    <div class="text-xs font-bold text-amber-600 mb-1">FASE 2 — PRÓXIMA</div>
                    <div class="text-xs font-semibold text-zinc-700">Dashboard + Servicios</div>
                    <div class="text-xs text-zinc-500 mt-0.5">Agregar cortes, precios y duración. KPIs reales.</div>
                </div>
                <div class="bg-stone-50 rounded-lg p-3 border border-stone-200">
                    <div class="text-xs font-bold text-zinc-400 mb-1">FASE 3</div>
                    <div class="text-xs font-semibold text-zinc-600">Reserva de clientes</div>
                    <div class="text-xs text-zinc-400 mt-0.5">Página pública sin registro.</div>
                </div>
                <div class="bg-stone-50 rounded-lg p-3 border border-stone-200">
                    <div class="text-xs font-bold text-zinc-400 mb-1">FASES 4–5</div>
                    <div class="text-xs font-semibold text-zinc-600">Agenda + Reportes</div>
                    <div class="text-xs text-zinc-400 mt-0.5">Gestión completa de la barbería.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Columnas inferiores ───────────────────────────────── -->
<div class="grid lg:grid-cols-2 gap-4">

    <!-- Citas del día -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-4 h-4 text-amber-500"></i>
                Citas de hoy
            </h3>
            <span class="text-xs text-zinc-400"><?= $hoy ?></span>
        </div>
        <div class="p-8 text-center">
            <div class="w-12 h-12 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-3">
                <i data-lucide="calendar-off" class="w-5 h-5 text-zinc-400"></i>
            </div>
            <p class="text-sm font-medium text-zinc-600">Sin citas programadas</p>
            <p class="text-xs text-zinc-400 mt-1">Disponible a partir de la Fase 3</p>
        </div>
    </div>

    <!-- Lista de espera -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-purple-500"></i>
                Lista de espera
            </h3>
            <span class="text-xs bg-zinc-100 text-zinc-500 px-2 py-0.5 rounded-full font-medium">0</span>
        </div>
        <div class="p-8 text-center">
            <div class="w-12 h-12 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-3">
                <i data-lucide="user-plus" class="w-5 h-5 text-zinc-400"></i>
            </div>
            <p class="text-sm font-medium text-zinc-600">Lista de espera vacía</p>
            <p class="text-xs text-zinc-400 mt-1">Disponible a partir de la Fase 4</p>
        </div>
    </div>

</div>
