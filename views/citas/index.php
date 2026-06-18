<?php
$esHoy     = $fecha === date('Y-m-d');
$esPasado  = $fecha < date('Y-m-d');
$horaActual = date('H:i');

$estadoConfig = [
    'reservado'     => ['label' => 'Reservado',     'bg' => 'bg-blue-100',    'text' => 'text-blue-700',   'dot' => 'bg-blue-500'],
    'confirmado'    => ['label' => 'Confirmado',     'bg' => 'bg-green-100',   'text' => 'text-green-700',  'dot' => 'bg-green-500'],
    'atendido'      => ['label' => 'Atendido',       'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700','dot' => 'bg-emerald-500'],
    'no_presentado' => ['label' => 'No presentado',  'bg' => 'bg-red-100',     'text' => 'text-red-700',    'dot' => 'bg-red-400'],
    'en_espera'     => ['label' => 'En espera',      'bg' => 'bg-purple-100',  'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
    'cancelado'     => ['label' => 'Cancelado',      'bg' => 'bg-zinc-100',    'text' => 'text-zinc-500',   'dot' => 'bg-zinc-400'],
];

// Botones de acción por estado
$acciones = [
    'reservado'  => [
        ['estado' => 'confirmado',    'label' => 'Llegó',      'class' => 'bg-green-500 hover:bg-green-600 text-white'],
        ['estado' => 'no_presentado', 'label' => 'No vino',    'class' => 'bg-red-100 hover:bg-red-200 text-red-700'],
    ],
    'confirmado' => [
        ['estado' => 'atendido',      'label' => 'Atendido',   'class' => 'bg-emerald-500 hover:bg-emerald-600 text-white'],
    ],
    'en_espera'  => [
        ['estado' => 'atendido',      'label' => 'Atendido',   'class' => 'bg-emerald-500 hover:bg-emerald-600 text-white'],
    ],
];
?>

<!-- Navegación de fecha -->
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-2">
        <a href="<?= url('citas') ?>?fecha=<?= $fechaPrev ?>"
           class="p-2 rounded-lg border border-stone-200 bg-white hover:bg-stone-50 transition-colors shadow-sm">
            <i data-lucide="chevron-left" class="w-4 h-4 text-zinc-600"></i>
        </a>
        <div class="text-center px-2">
            <div class="text-sm font-bold text-zinc-900"><?= fechaEsp($fecha, 'completa') ?></div>
            <?php if ($esHoy): ?>
            <span class="text-xs text-blue-600 font-semibold">Hoy · <?= $horaActual ?></span>
            <?php elseif ($esPasado): ?>
            <span class="text-xs text-zinc-400">Día pasado</span>
            <?php else: ?>
            <span class="text-xs text-blue-500">Día futuro</span>
            <?php endif; ?>
        </div>
        <a href="<?= url('citas') ?>?fecha=<?= $fechaNext ?>"
           class="p-2 rounded-lg border border-stone-200 bg-white hover:bg-stone-50 transition-colors shadow-sm">
            <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-600"></i>
        </a>
        <?php if (!$esHoy): ?>
        <a href="<?= url('citas') ?>"
           class="ml-1 text-xs px-2.5 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
            Hoy
        </a>
        <?php endif; ?>
    </div>

    <!-- Input de fecha -->
    <input type="date" value="<?= $fecha ?>"
           onchange="location.href='<?= url('citas') ?>?fecha='+this.value"
           class="border border-stone-200 rounded-lg px-3 py-1.5 text-sm text-zinc-700 bg-white
                  focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hidden sm:block">
</div>

<!-- Stats del día -->
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5">
    <?php
    $statCards = [
        ['label' => 'Total',         'value' => $stats['total'],          'color' => 'text-zinc-800',    'bg' => 'bg-zinc-50',    'icon' => 'calendar-days'],
        ['label' => 'Reservados',    'value' => $stats['reservados'],     'color' => 'text-blue-700',    'bg' => 'bg-blue-50',    'icon' => 'clock'],
        ['label' => 'Confirmados',   'value' => $stats['confirmados'],    'color' => 'text-green-700',   'bg' => 'bg-green-50',   'icon' => 'user-check'],
        ['label' => 'Atendidos',     'value' => $stats['atendidos'],      'color' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'icon' => 'check-circle-2'],
        ['label' => 'No presentados','value' => $stats['no_presentados'], 'color' => 'text-red-600',     'bg' => 'bg-red-50',     'icon' => 'user-x'],
    ];
    foreach ($statCards as $sc): ?>
    <div class="<?= $sc['bg'] ?> rounded-xl p-3 border border-stone-200 shadow-sm">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs text-zinc-500"><?= $sc['label'] ?></span>
            <i data-lucide="<?= $sc['icon'] ?>" class="w-3.5 h-3.5 <?= $sc['color'] ?>"></i>
        </div>
        <div class="text-2xl font-bold <?= $sc['color'] ?>"><?= $sc['value'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- FullCalendar — solo tablet/desktop -->
<div class="hidden md:block bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden mb-5">
    <div class="px-5 py-3.5 border-b border-stone-100 flex items-center gap-2">
        <i data-lucide="calendar-range" class="w-4 h-4 text-blue-500"></i>
        <h3 class="text-sm font-semibold text-zinc-800">Vista de agenda</h3>
    </div>
    <div class="p-3">
        <div id="calendar-agenda" style="min-height:240px;"></div>
    </div>
</div>

<!-- Lista de citas -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
            <i data-lucide="list" class="w-4 h-4 text-blue-500"></i>
            Citas del día
        </h3>
        <a href="<?= url('reservar') ?>" target="_blank"
           class="text-xs text-blue-500 hover:text-blue-700 flex items-center gap-1">
            <i data-lucide="external-link" class="w-3 h-3"></i>
            Página de reservas
        </a>
    </div>

    <?php if (empty($citas)): ?>
    <div class="p-10 text-center">
        <div class="w-12 h-12 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-3">
            <i data-lucide="calendar-off" class="w-5 h-5 text-zinc-400"></i>
        </div>
        <p class="text-sm font-medium text-zinc-600">Sin citas para este día</p>
        <p class="text-xs text-zinc-400 mt-1">Las reservas aparecerán aquí automáticamente</p>
    </div>
    <?php else: ?>

    <div class="divide-y divide-stone-100 md:divide-stone-50">
        <?php foreach ($citas as $c):
            $ec = $estadoConfig[$c['estado']] ?? $estadoConfig['reservado'];
            $ac = $acciones[$c['estado']] ?? [];
            $hInicio = substr($c['hora_inicio'], 0, 5);
            $hFin    = substr($c['hora_fin'],    0, 5);
        ?>
        <div class="flex items-start gap-4 px-4 md:px-5 py-4 hover:bg-stone-50/50 transition-colors">

            <!-- Hora -->
            <div class="text-center w-14 flex-shrink-0 pt-0.5">
                <div class="text-sm font-bold text-zinc-800"><?= $hInicio ?></div>
                <div class="text-xs text-zinc-400"><?= $hFin ?></div>
                <div class="text-xs text-zinc-300 mt-0.5"><?= (int)$c['duracion_minutos'] ?>m</div>
            </div>

            <!-- Línea de tiempo -->
            <div class="flex flex-col items-center self-stretch pt-1 flex-shrink-0">
                <div class="w-2.5 h-2.5 rounded-full <?= $ec['dot'] ?> flex-shrink-0"></div>
                <div class="w-px flex-1 bg-stone-200 mt-1"></div>
            </div>

            <!-- Contenido -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 flex-wrap">
                    <div>
                        <div class="font-semibold text-zinc-800 text-sm">
                            <?= htmlspecialchars($c['nombre_cliente']) ?>
                        </div>
                        <div class="text-xs text-zinc-500 mt-0.5 flex items-center gap-2">
                            <span><?= htmlspecialchars($c['servicio_nombre']) ?></span>
                            <span class="text-zinc-300">·</span>
                            <span class="font-medium text-zinc-600"><?= moneda((float)$c['precio']) ?></span>
                            <?php if ($c['telefono_cliente']): ?>
                            <span class="text-zinc-300">·</span>
                            <span><?= htmlspecialchars($c['telefono_cliente']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full <?= $ec['bg'] . ' ' . $ec['text'] ?> flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full <?= $ec['dot'] ?>"></span>
                        <?= $ec['label'] ?>
                    </span>
                </div>

                <!-- Botones de acción — tamaño táctil para móvil -->
                <?php if (!empty($ac)): ?>
                <div class="flex items-center gap-2 mt-3 flex-wrap">
                    <?php foreach ($ac as $btn): ?>
                    <form method="POST" action="<?= url('citas/estado') ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="id"         value="<?= (int)$c['id'] ?>">
                        <input type="hidden" name="fecha"      value="<?= htmlspecialchars($fecha) ?>">
                        <input type="hidden" name="estado"     value="<?= $btn['estado'] ?>">
                        <button type="submit"
                                class="text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm <?= $btn['class'] ?>">
                            <?= $btn['label'] ?>
                        </button>
                    </form>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <!-- Footer -->
    <div class="px-5 py-3 border-t border-stone-100 bg-stone-50 text-xs text-zinc-400">
        <?= $stats['total'] ?> cita<?= $stats['total'] !== 1 ? 's' : '' ?> para el día
        &nbsp;·&nbsp;
        Auto-liberación activa: citas vencidas marcadas como "No presentado"
    </div>

    <?php endif; ?>
</div>

<!-- FullCalendar init -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('calendar-agenda');
    if (!el || typeof FullCalendar === 'undefined') return;

    const calendar = new FullCalendar.Calendar(el, {
        initialView:   'timeGridDay',
        initialDate:   '<?= $fecha ?>',
        locale:        'es',
        headerToolbar: false,
        slotMinTime:   '<?= $cfg['horario_inicio'] ?? '08:00' ?>',
        slotMaxTime:   '<?= $cfg['horario_fin']    ?? '19:00' ?>',
        slotDuration:  '00:30:00',
        height:        'auto',
        allDaySlot:    false,
        nowIndicator:  true,
        events:        <?= json_encode($eventosFC) ?>,
        eventClick: function(info) {
            const p = info.event.extendedProps;
            Swal.fire({
                title: info.event.title,
                html: `<div class="text-left text-sm space-y-1">
                    <p><b>Estado:</b> ${p.estado}</p>
                    <p><b>Precio:</b> L. ${parseFloat(p.precio).toFixed(2)}</p>
                </div>`,
                icon: 'info',
                confirmButtonColor: '#f59e0b',
                confirmButtonText:  'Cerrar',
            });
        },
        eventContent: function(arg) {
            return {
                html: `<div class="overflow-hidden px-1 text-xs leading-tight">
                    <div class="font-semibold truncate">${arg.event.extendedProps.cliente}</div>
                    <div class="opacity-80 truncate">${arg.event.extendedProps.servicio}</div>
                </div>`
            };
        },
    });
    calendar.render();
});
</script>
