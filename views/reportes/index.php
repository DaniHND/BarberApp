<?php
$nombresMes = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

$totalMes    = (float) $resumenMes['total'];
$citasMes    = (int)   $resumenMes['citas'];
$totalHoy    = (float) $resumenHoy['total'];
$citasHoy    = (int)   $resumenHoy['citas'];
$promedio    = $citasMes > 0 ? $totalMes / $citasMes : 0;

// Datos para Chart.js
$diasLabels  = array_keys($porDia);
$diasData    = array_values($porDia);
$maxMonto    = $topSvcs ? max(array_column($topSvcs, 'total_monto')) : 0;
?>

<!-- Navegación de mes -->
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <h2 class="text-lg font-bold text-zinc-900">Reportes</h2>
    <div class="flex items-center gap-2">
        <a href="<?= url('reportes') ?>?mes=<?= $prevMes ?>&anio=<?= $prevAnio ?>"
           class="p-2 rounded-lg border border-stone-200 bg-white hover:bg-stone-50 shadow-sm transition-colors">
            <i data-lucide="chevron-left" class="w-4 h-4 text-zinc-600"></i>
        </a>
        <div class="px-4 py-2 bg-white border border-stone-200 rounded-lg shadow-sm text-sm font-semibold text-zinc-800 min-w-[160px] text-center">
            <?= $nombresMes[$mes] ?> <?= $anio ?>
        </div>
        <?php
        $esActual = ($anio == date('Y') && $mes == (int)date('n'));
        ?>
        <a href="<?= url('reportes') ?>?mes=<?= $nextMes ?>&anio=<?= $nextAnio ?>"
           class="p-2 rounded-lg border border-stone-200 bg-white hover:bg-stone-50 shadow-sm transition-colors
                  <?= $esActual ? 'opacity-30 pointer-events-none' : '' ?>">
            <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-600"></i>
        </a>
        <?php if (!$esActual): ?>
        <a href="<?= url('reportes') ?>"
           class="text-xs px-2.5 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
            Hoy
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- KPI cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-zinc-500">Ingresos hoy</span>
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                <i data-lucide="banknote" class="w-4 h-4 text-emerald-600"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-emerald-700"><?= moneda($totalHoy) ?></div>
        <div class="text-xs text-zinc-400 mt-1"><?= $citasHoy ?> cita<?= $citasHoy !== 1 ? 's' : '' ?> atendida<?= $citasHoy !== 1 ? 's' : '' ?></div>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-zinc-500">Ingresos del mes</span>
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <i data-lucide="trending-up" class="w-4 h-4 text-blue-600"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-blue-700"><?= moneda($totalMes) ?></div>
        <div class="text-xs text-zinc-400 mt-1"><?= $nombresMes[$mes] ?> <?= $anio ?></div>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-zinc-500">Citas atendidas</span>
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <i data-lucide="calendar-check-2" class="w-4 h-4 text-blue-600"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-blue-700"><?= $citasMes ?></div>
        <div class="text-xs text-zinc-400 mt-1">en <?= $nombresMes[$mes] ?></div>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-zinc-500">Promedio por cita</span>
            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                <i data-lucide="calculator" class="w-4 h-4 text-purple-600"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-purple-700"><?= moneda($promedio) ?></div>
        <div class="text-xs text-zinc-400 mt-1">ticket promedio</div>
    </div>

</div>

<!-- Gráfica + Top servicios -->
<div class="grid lg:grid-cols-3 gap-5 mb-5">

    <!-- Ingresos por día — gráfica -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center gap-2">
            <i data-lucide="bar-chart-2" class="w-4 h-4 text-blue-500"></i>
            <h3 class="text-sm font-semibold text-zinc-800">Ingresos diarios — <?= $nombresMes[$mes] ?> <?= $anio ?></h3>
        </div>
        <div class="p-4">
            <?php if ($totalMes > 0): ?>
            <canvas id="grafica-ingresos" height="180"></canvas>
            <?php else: ?>
            <div class="h-44 flex flex-col items-center justify-center text-zinc-400">
                <i data-lucide="bar-chart-2" class="w-8 h-8 mb-2 opacity-30"></i>
                <span class="text-sm">Sin ingresos registrados en <?= $nombresMes[$mes] ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top servicios -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-100 flex items-center gap-2">
            <i data-lucide="trophy" class="w-4 h-4 text-blue-500"></i>
            <h3 class="text-sm font-semibold text-zinc-800">Top servicios del mes</h3>
        </div>
        <div class="p-4 space-y-3">
            <?php if (empty($topSvcs)): ?>
            <div class="text-center py-6 text-zinc-400 text-sm">Sin datos este mes</div>
            <?php else:
                $coloresBars = ['bg-blue-600','bg-red-500','bg-emerald-400','bg-purple-400','bg-rose-400'];
                foreach ($topSvcs as $idx => $svc):
                    $pct = $maxMonto > 0 ? round(($svc['total_monto'] / $maxMonto) * 100) : 0;
                    $color = $coloresBars[$idx % count($coloresBars)];
            ?>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-semibold text-zinc-700 truncate max-w-[140px]">
                        <?= htmlspecialchars($svc['nombre']) ?>
                    </span>
                    <span class="text-xs text-zinc-500 ml-2 flex-shrink-0">
                        <?= (int)$svc['total_citas'] ?> cita<?= $svc['total_citas'] != 1 ? 's' : '' ?>
                        · <?= moneda((float)$svc['total_monto']) ?>
                    </span>
                </div>
                <div class="h-1.5 bg-stone-100 rounded-full overflow-hidden">
                    <div class="h-full <?= $color ?> rounded-full transition-all"
                         style="width:<?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

</div>

<!-- Últimas citas atendidas -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-stone-100 flex items-center gap-2">
        <i data-lucide="list-checks" class="w-4 h-4 text-blue-500"></i>
        <h3 class="text-sm font-semibold text-zinc-800">Últimas citas cobradas</h3>
        <span class="ml-auto text-xs text-zinc-400">Últimas 15</span>
    </div>

    <?php if (empty($ultimas)): ?>
    <div class="p-10 text-center text-sm text-zinc-400">
        Aún no hay citas cobradas registradas.
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500">Cliente</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500 hidden sm:table-cell">Servicio</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500 hidden md:table-cell">Hora</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Fecha</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold text-zinc-500">Cobrado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                <?php foreach ($ultimas as $u): ?>
                <tr class="hover:bg-stone-50/60 transition-colors">
                    <td class="px-5 py-3 font-medium text-zinc-800">
                        <?= htmlspecialchars($u['nombre_cliente'] ?? '—') ?>
                    </td>
                    <td class="px-3 py-3 hidden sm:table-cell">
                        <?php if ($u['servicio_nombre']): ?>
                        <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700
                                     border border-blue-100 rounded-full px-2 py-0.5">
                            <i data-lucide="scissors" class="w-3 h-3"></i>
                            <?= htmlspecialchars($u['servicio_nombre']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-zinc-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3 text-zinc-500 text-xs hidden md:table-cell">
                        <?= $u['hora_inicio'] ? substr($u['hora_inicio'], 0, 5) : '—' ?>
                    </td>
                    <td class="px-3 py-3 text-zinc-500 text-xs">
                        <?= fechaEsp($u['fecha']) ?>
                    </td>
                    <td class="px-3 py-3 text-right font-bold text-zinc-800">
                        <?= moneda((float)$u['monto']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js init -->
<?php if ($totalMes > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('grafica-ingresos');
    if (!ctx || typeof Chart === 'undefined') return;

    const labels = <?= json_encode(array_map(fn($d) => (string)$d, $diasLabels)) ?>;
    const data   = <?= json_encode($diasData) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ingresos (<?= htmlspecialchars($nombresMes[$mes]) ?>)',
                data: data,
                backgroundColor: 'rgba(245,158,11,0.75)',
                borderColor:     '#d97706',
                borderWidth:     1,
                borderRadius:    4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'L. ' + parseFloat(ctx.parsed.y).toFixed(2)
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'L. ' + parseFloat(v).toFixed(0)
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>
