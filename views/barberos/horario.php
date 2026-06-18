<?php
/** @var array $barbero  @var array $horarios  @var array $bloqueos */

$diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
$hoy = date('Y-m-d');
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
    <a href="<?= url('barberos') ?>" class="hover:text-zinc-600 transition-colors">Barberos</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-zinc-600 font-medium"><?= htmlspecialchars($barbero['nombre']) ?></span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    <!-- ── COLUMNA 1: Horario semanal ────────────────── -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5">
        <h3 class="font-bold text-zinc-800 mb-1">Horario semanal</h3>
        <p class="text-xs text-zinc-400 mb-4">Marca los días en que trabaja y el rango de horas</p>

        <form method="POST" action="<?= url('barberos/horario/guardar') ?>"
              x-data="horarioForm()">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="barbero_id" value="<?= (int)$barbero['id'] ?>">

            <div class="space-y-2" id="dias-grid">
                <?php foreach ($diasNombres as $num => $nombre): ?>
                <?php $h = $horarios[$num] ?? null; ?>
                <div class="rounded-lg border border-stone-200 p-3"
                     x-data="{ trabaja: <?= $h ? 'true' : 'false' ?> }">

                    <div class="flex items-center gap-3">
                        <!-- Checkbox / toggle día -->
                        <label class="flex items-center gap-2 cursor-pointer flex-1">
                            <input type="checkbox"
                                   name="dias[<?= $num ?>][activo]"
                                   value="1"
                                   x-model="trabaja"
                                   <?= $h ? 'checked' : '' ?>
                                   class="w-4 h-4 rounded border-zinc-300 text-blue-500 focus:ring-blue-400">
                            <span class="text-sm font-semibold text-zinc-700" x-text="'<?= $nombre ?>'"></span>
                        </label>

                        <!-- Horas (solo si trabaja ese día) -->
                        <div class="flex items-center gap-1.5" x-show="trabaja">
                            <input type="time" name="dias[<?= $num ?>][hora_inicio]"
                                   value="<?= htmlspecialchars($h['hora_inicio'] ?? '08:00') ?>"
                                   class="border border-zinc-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                   title="Hora de entrada">
                            <span class="text-zinc-300 text-xs">–</span>
                            <input type="time" name="dias[<?= $num ?>][hora_fin]"
                                   value="<?= htmlspecialchars($h['hora_fin'] ?? '19:00') ?>"
                                   class="border border-zinc-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                   title="Hora de salida">
                        </div>
                        <span x-show="!trabaja" class="text-xs text-zinc-400 italic">No trabaja</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="submit"
                    class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5
                           rounded-lg transition-colors text-sm">
                <i data-lucide="save" class="w-4 h-4 inline mr-1 -mt-0.5"></i>
                Guardar horario
            </button>
        </form>
    </div>

    <!-- ── COLUMNA 2: Días bloqueados ────────────────── -->
    <div class="space-y-4">

        <!-- Formulario para agregar bloqueo -->
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5">
            <h3 class="font-bold text-zinc-800 mb-1">Días bloqueados</h3>
            <p class="text-xs text-zinc-400 mb-4">
                Días en que el barbero no estará disponible (vacaciones, compromisos, etc.)
            </p>

            <form method="POST" action="<?= url('barberos/bloqueo/agregar') ?>" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="barbero_id" value="<?= (int)$barbero['id'] ?>">

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Fecha</label>
                    <input type="date" name="fecha"
                           min="<?= $hoy ?>"
                           required
                           class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">
                        Motivo <span class="text-zinc-400 font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="motivo"
                           placeholder="Ej: Vacaciones, cita médica…"
                           class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5
                               rounded-lg transition-colors text-sm">
                    <i data-lucide="calendar-x" class="w-4 h-4 inline mr-1 -mt-0.5"></i>
                    Bloquear este día
                </button>
            </form>
        </div>

        <!-- Lista de bloqueos futuros -->
        <?php if (!empty($bloqueos)): ?>
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-stone-100">
                <p class="text-xs font-semibold text-zinc-600">Próximos días bloqueados</p>
            </div>
            <ul class="divide-y divide-stone-50">
                <?php foreach ($bloqueos as $bl): ?>
                <li class="flex items-center justify-between px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-zinc-800">
                            <?= fechaEsp($bl['fecha'], 'completa') ?>
                        </div>
                        <?php if ($bl['motivo']): ?>
                        <div class="text-xs text-zinc-400 mt-0.5"><?= htmlspecialchars($bl['motivo']) ?></div>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?= url('barberos/bloqueo/eliminar') ?>" x-data
                          @submit.prevent="Swal.fire({
                              title: '¿Desbloquear este día?',
                              icon: 'question',
                              showCancelButton: true,
                              confirmButtonColor: '#2563eb',
                              confirmButtonText: 'Sí, desbloquear',
                              cancelButtonText: 'Cancelar',
                          }).then(r => r.isConfirmed && $el.submit())">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="id" value="<?= (int)$bl['id'] ?>">
                        <input type="hidden" name="barbero_id" value="<?= (int)$barbero['id'] ?>">
                        <button type="submit"
                                class="p-1.5 rounded-lg text-zinc-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                                title="Desbloquear">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php else: ?>
        <div class="bg-stone-50 border border-dashed border-stone-200 rounded-xl p-6 text-center">
            <i data-lucide="check-circle" class="w-6 h-6 text-green-400 mx-auto mb-2"></i>
            <p class="text-xs text-zinc-400">Sin días bloqueados próximamente</p>
        </div>
        <?php endif; ?>

        <!-- Enlace de vuelta -->
        <a href="<?= url('barberos') ?>"
           class="flex items-center gap-2 text-xs text-zinc-400 hover:text-zinc-600 transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            Volver a la lista de barberos
        </a>
    </div>

</div>

<script>
function horarioForm() {
    return {};
}
</script>
