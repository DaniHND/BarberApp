<?php
// Datos del dashboard
$citasHoy       = (int)   ($stats['citas_hoy']       ?? 0);
$enEspera       = (int)   ($stats['en_espera']        ?? 0);
$serviciosAct   = (int)   ($stats['servicios_activos']?? 0);
$ingresosHoy    = (float) ($stats['ingresos_hoy']     ?? 0);
$hoy            = date('d/m/Y');

// Datos para Chart.js
$chartLabels = [];
$chartData   = [];
foreach (($citasSemana ?? []) as $fecha => $total) {
    $chartLabels[] = date('d/m', strtotime($fecha));
    $chartData[]   = $total;
}
$maxCitas = max(array_values($citasSemana ?? [0]) ?: [0]);
?>

<!-- ── Stat cards ─────────────────────────────────────── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">Citas hoy</p>
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                <i data-lucide="calendar-check" class="w-4 h-4 text-blue-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900"><?= $citasHoy ?></p>
        <p class="text-xs text-zinc-400 mt-1"><?= $citasHoy === 0 ? 'Sin citas programadas' : 'citas para hoy' ?></p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">En espera</p>
            <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center">
                <i data-lucide="clock" class="w-4 h-4 text-purple-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900"><?= $enEspera ?></p>
        <p class="text-xs text-zinc-400 mt-1"><?= $enEspera === 0 ? 'Lista de espera vacía' : 'esperando atención' ?></p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">Servicios activos</p>
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                <i data-lucide="scissors" class="w-4 h-4 text-blue-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900"><?= $serviciosAct ?></p>
        <p class="text-xs text-zinc-400 mt-1">
            <?php if ($serviciosAct === 0): ?>
                <a href="<?= url('servicios/crear') ?>" class="text-blue-500 hover:underline">Agregar servicios →</a>
            <?php else: ?>
                disponibles para reserva
            <?php endif; ?>
        </p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-stone-200 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs text-zinc-500 font-medium">Ingresos hoy</p>
            <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                <i data-lucide="banknote" class="w-4 h-4 text-green-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-zinc-900"><?= moneda($ingresosHoy) ?></p>
        <p class="text-xs text-zinc-400 mt-1"><?= $hoy ?></p>
    </div>

</div>

<!-- ── Gráfica + Top servicios ────────────────────────── -->
<div class="grid lg:grid-cols-3 gap-4 mb-4">

    <!-- Gráfica citas últimos 7 días -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                <i data-lucide="trending-up" class="w-4 h-4 text-blue-500"></i>
                Citas — últimos 7 días
            </h3>
            <?php if (array_sum($chartData) > 0): ?>
            <span class="text-xs text-zinc-400">Total: <?= array_sum($chartData) ?></span>
            <?php endif; ?>
        </div>
        <div class="p-4" style="height:200px; position:relative;">
            <?php if ($maxCitas === 0): ?>
            <div class="h-full flex flex-col items-center justify-center text-center">
                <i data-lucide="bar-chart-2" class="w-8 h-8 text-zinc-200 mb-2"></i>
                <p class="text-xs text-zinc-400">Sin citas en los últimos 7 días</p>
                <p class="text-xs text-zinc-300 mt-0.5">La gráfica se activará con la Fase 3</p>
            </div>
            <?php else: ?>
            <canvas id="chartCitas"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top servicios -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                <i data-lucide="star" class="w-4 h-4 text-blue-500"></i>
                Top servicios
            </h3>
            <a href="<?= url('servicios') ?>" class="text-xs text-blue-500 hover:text-blue-700">Ver todos</a>
        </div>
        <div class="p-4">
            <?php if (empty($topServicios)): ?>
            <div class="text-center py-6">
                <i data-lucide="scissors" class="w-7 h-7 text-zinc-200 mx-auto mb-2"></i>
                <p class="text-xs text-zinc-400">Sin servicios aún</p>
                <a href="<?= url('servicios/crear') ?>" class="text-xs text-blue-500 hover:underline mt-1 block">
                    Agregar servicio →
                </a>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php
                $maxTotal = max(array_column($topServicios, 'total')) ?: 1;
                foreach ($topServicios as $i => $svc):
                    $pct = $maxTotal > 0 ? round(($svc['total'] / $maxTotal) * 100) : 0;
                    $colores = ['bg-blue-600','bg-red-500','bg-green-500','bg-purple-500','bg-zinc-400'];
                    $color = $colores[$i] ?? 'bg-zinc-400';
                ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-zinc-700 truncate max-w-[140px]">
                            <?= htmlspecialchars($svc['nombre']) ?>
                        </span>
                        <span class="text-xs text-zinc-400 ml-2 flex-shrink-0">
                            <?= $svc['total'] ?> cita<?= $svc['total'] !== 1 ? 's' : '' ?>
                        </span>
                    </div>
                    <div class="h-1.5 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full <?= $color ?> rounded-full transition-all"
                             style="width: <?= max($pct, $svc['total'] > 0 ? 8 : 0) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ── Accesos rápidos / citas del día ───────────────── -->
<div class="grid lg:grid-cols-2 gap-4">

    <!-- Citas del día -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-4 h-4 text-blue-500"></i>
                Agenda de hoy
            </h3>
            <span class="text-xs text-zinc-400"><?= $hoy ?></span>
        </div>
        <div class="p-8 text-center">
            <div class="w-12 h-12 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-3">
                <i data-lucide="calendar-off" class="w-5 h-5 text-zinc-400"></i>
            </div>
            <p class="text-sm font-medium text-zinc-600">Sin citas programadas</p>
            <p class="text-xs text-zinc-400 mt-1">Disponible a partir de la Fase 3 — Reservas</p>
        </div>
    </div>

    <!-- Lista de espera -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-purple-500"></i>
                Lista de espera
            </h3>
            <span class="text-xs bg-zinc-100 text-zinc-500 px-2 py-0.5 rounded-full font-medium">
                <?= $enEspera ?>
            </span>
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

<?php if ($maxCitas > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('chartCitas');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartData) ?>,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: {
                label: ctx => ` ${ctx.parsed.y} cita${ctx.parsed.y !== 1 ? 's' : ''}`
            }}},
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f5f5f4' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
<?php endif; ?>
