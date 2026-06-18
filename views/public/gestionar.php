<?php
$hoy      = date('Y-m-d');
$maxFecha = date('Y-m-d', strtotime('+30 days'));
$nueva       = !empty($nueva);
$reprogramada = !empty($_GET['reprogramada']);
$errorRep     = !empty($_GET['errorRep']);

// Estados que permiten gestión
$gestionable = $cita && in_array($cita['estado'], ['reservado', 'confirmado'], true);

// Label de estado
$estadoLabels = [
    'reservado'      => ['label' => 'Reservado',      'class' => 'bg-blue-100 text-blue-700'],
    'confirmado'     => ['label' => 'Confirmado',      'class' => 'bg-green-100 text-green-700'],
    'atendido'       => ['label' => 'Atendido',        'class' => 'bg-emerald-100 text-emerald-700'],
    'cancelado'      => ['label' => 'Cancelado',       'class' => 'bg-red-100 text-red-700'],
    'no_presentado'  => ['label' => 'No presentado',   'class' => 'bg-zinc-100 text-zinc-500'],
    'en_espera'      => ['label' => 'En espera',       'class' => 'bg-purple-100 text-purple-700'],
];
$estadoInfo = isset($cita['estado']) ? ($estadoLabels[$cita['estado']] ?? ['label' => $cita['estado'], 'class' => 'bg-zinc-100 text-zinc-500']) : null;
?>

<div class="max-w-lg mx-auto px-4 py-6">

    <!-- Token no encontrado -->
    <?php if (!$cita): ?>
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8 text-center">
        <div class="w-14 h-14 bg-red-50 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="search-x" class="w-6 h-6 text-red-400"></i>
        </div>
        <h2 class="text-lg font-bold text-zinc-800 mb-1">Cita no encontrada</h2>
        <p class="text-sm text-zinc-500 mb-5">El enlace es inválido o la cita ya no existe.</p>
        <a href="<?= url('reservar') ?>"
           class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white
                  font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors">
            <i data-lucide="calendar-plus" class="w-4 h-4"></i>
            Hacer nueva reserva
        </a>
    </div>
    <?php return; endif; ?>

    <!-- Alerta nueva cita -->
    <?php if ($nueva): ?>
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 flex items-start gap-3">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-green-800">¡Cita reservada exitosamente!</p>
            <p class="text-xs text-green-600 mt-0.5">Guarda este enlace para poder gestionar tu cita.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($reprogramada): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 flex items-start gap-3">
        <i data-lucide="calendar-check" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
        <p class="text-sm text-blue-800 font-medium">Tu cita fue reprogramada correctamente.</p>
    </div>
    <?php endif; ?>

    <?php if ($errorRep): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
        <p class="text-sm text-red-700">No se pudo reprogramar. La hora seleccionada ya no está disponible.</p>
    </div>
    <?php endif; ?>

    <!-- Tarjeta de la cita -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-4">
        <div class="bg-zinc-900 px-5 py-4 flex items-start justify-between gap-3">
            <div>
                <p class="text-xs text-zinc-400 font-medium mb-0.5">Tu cita</p>
                <h2 class="text-lg font-bold text-white"><?= htmlspecialchars($cita['servicio_nombre']) ?></h2>
            </div>
            <?php if ($estadoInfo): ?>
            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full <?= $estadoInfo['class'] ?>">
                <?= $estadoInfo['label'] ?>
            </span>
            <?php endif; ?>
        </div>

        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 text-sm">
                <div class="w-8 h-8 bg-stone-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="calendar" class="w-4 h-4 text-zinc-500"></i>
                </div>
                <div>
                    <div class="text-xs text-zinc-400 font-medium">Fecha</div>
                    <div class="font-semibold text-zinc-800"><?= fechaEsp($cita['fecha'], 'completa') ?></div>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <div class="w-8 h-8 bg-stone-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 text-zinc-500"></i>
                </div>
                <div>
                    <div class="text-xs text-zinc-400 font-medium">Hora</div>
                    <div class="font-semibold text-zinc-800">
                        <?= date('g:i A', strtotime($cita['hora_inicio'])) ?>
                        –
                        <?= date('g:i A', strtotime($cita['hora_fin'])) ?>
                        <span class="text-zinc-400 font-normal">(<?= duracionFmt((int)$cita['duracion_minutos']) ?>)</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <div class="w-8 h-8 bg-stone-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-4 h-4 text-zinc-500"></i>
                </div>
                <div>
                    <div class="text-xs text-zinc-400 font-medium">Nombre</div>
                    <div class="font-semibold text-zinc-800"><?= htmlspecialchars($cita['nombre_cliente']) ?></div>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm border-t border-stone-100 pt-3 mt-1">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="banknote" class="w-4 h-4 text-blue-500"></i>
                </div>
                <div>
                    <div class="text-xs text-zinc-400 font-medium">Precio</div>
                    <div class="font-bold text-blue-600"><?= moneda((float)$cita['precio']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($gestionable): ?>
    <!-- ── Acciones ─────────────────────────────────── -->
    <div x-data="gestionar()" x-init="init()">

        <!-- Tabs -->
        <div class="flex gap-2 mb-4">
            <button type="button" @click="panel = 'reprogramar'"
                    class="slot-btn flex-1 py-2.5 rounded-xl text-sm font-semibold border-2 transition-all"
                    :class="panel === 'reprogramar' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-stone-200 bg-white text-zinc-600 hover:border-blue-200'">
                <i data-lucide="calendar-cog" class="w-4 h-4 inline mr-1 -mt-0.5"></i>
                Reprogramar
            </button>
            <button type="button" @click="panel = 'cancelar'"
                    class="slot-btn flex-1 py-2.5 rounded-xl text-sm font-semibold border-2 transition-all"
                    :class="panel === 'cancelar' ? 'border-red-400 bg-red-50 text-red-700' : 'border-stone-200 bg-white text-zinc-600 hover:border-red-200'">
                <i data-lucide="x-circle" class="w-4 h-4 inline mr-1 -mt-0.5"></i>
                Cancelar
            </button>
        </div>

        <!-- Panel Reprogramar -->
        <div x-show="panel === 'reprogramar'" x-cloak>
            <form method="POST" action="<?= url('reservar/reprogramar') ?>" @submit.prevent="confirmarRep()">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="token"      value="<?= htmlspecialchars($cita['token']) ?>">
                <input type="hidden" name="hora_inicio" :value="horaInicio">
                <input type="hidden" name="hora_fin"    :value="horaFin">

                <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm space-y-4">

                    <!-- Fecha -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Nueva fecha</label>
                        <input type="date" x-model="fecha" @change="cargarSlots()"
                               min="<?= $hoy ?>" max="<?= $maxFecha ?>"
                               class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Slots -->
                    <div x-show="fecha">
                        <div class="text-xs font-semibold text-zinc-600 mb-2">Hora disponible</div>

                        <div x-show="cargando" class="text-center py-4">
                            <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                        </div>

                        <div x-show="!cargando && slots.length === 0 && fecha"
                             class="text-center py-4 text-xs text-zinc-400">
                            Sin horarios disponibles ese día.
                        </div>

                        <div x-show="!cargando && slots.length > 0"
                             class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            <template x-for="slot in slots" :key="slot.hora_inicio">
                                <button type="button" @click="seleccionarSlot(slot)"
                                        :disabled="!slot.disponible"
                                        class="slot-btn py-2.5 rounded-xl border-2 text-center text-sm font-medium"
                                        :class="horaInicio === slot.hora_inicio
                                            ? 'border-blue-500 bg-blue-500 text-white'
                                            : slot.disponible
                                                ? 'border-stone-200 bg-white hover:border-blue-400 text-zinc-700'
                                                : 'border-stone-100 bg-stone-50 text-zinc-300 cursor-not-allowed line-through'">
                                    <span x-text="slot.hora_inicio_fmt"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <button type="submit"
                            :disabled="!horaInicio"
                            class="slot-btn w-full py-3 rounded-xl font-semibold text-sm transition-all"
                            :class="horaInicio
                                ? 'bg-blue-500 hover:bg-blue-600 text-white shadow-sm'
                                : 'bg-stone-200 text-zinc-400 cursor-not-allowed'">
                        <span x-text="horaInicioFmt ? 'Reprogramar para las ' + horaInicioFmt : 'Selecciona una hora'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Panel Cancelar -->
        <div x-show="panel === 'cancelar'" x-cloak>
            <div class="bg-white rounded-xl border border-red-200 p-5 shadow-sm text-center">
                <div class="w-12 h-12 bg-red-50 rounded-full mx-auto flex items-center justify-center mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400"></i>
                </div>
                <h3 class="text-sm font-bold text-zinc-800 mb-1">¿Cancelar esta cita?</h3>
                <p class="text-xs text-zinc-500 mb-4">
                    Esta acción no se puede deshacer.
                    El horario quedará disponible para otros clientes.
                </p>
                <form method="POST" action="<?= url('reservar/cancelar') ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="token"      value="<?= htmlspecialchars($cita['token']) ?>">
                    <div class="flex gap-2">
                        <button type="button" @click="panel = null"
                                class="flex-1 py-2.5 rounded-xl border border-stone-200 text-sm font-semibold text-zinc-600 hover:bg-stone-50 transition-colors">
                            Volver
                        </button>
                        <button type="submit"
                                class="slot-btn flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition-colors">
                            Sí, cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /gestionar Alpine -->

    <?php elseif ($cita['estado'] === 'cancelado'): ?>
    <!-- Cita cancelada -->
    <div class="bg-white rounded-xl border border-stone-200 p-6 text-center shadow-sm">
        <div class="w-12 h-12 bg-red-50 rounded-full mx-auto flex items-center justify-center mb-3">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-400"></i>
        </div>
        <p class="text-sm font-semibold text-zinc-700 mb-1">Esta cita fue cancelada</p>
        <p class="text-xs text-zinc-400 mb-4">Puedes hacer una nueva reserva cuando quieras.</p>
        <a href="<?= url('reservar') ?>"
           class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white
                  font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors">
            <i data-lucide="calendar-plus" class="w-4 h-4"></i>
            Nueva reserva
        </a>
    </div>

    <?php else: ?>
    <!-- Cita ya atendida / sin acción -->
    <div class="bg-zinc-50 rounded-xl border border-stone-200 p-5 text-center shadow-sm">
        <p class="text-sm text-zinc-500">Esta cita ya no puede ser modificada.</p>
        <a href="<?= url('reservar') ?>" class="text-xs text-blue-500 hover:underline mt-1 block">
            Hacer otra reserva →
        </a>
    </div>
    <?php endif; ?>

    <!-- Enlace permanente -->
    <div class="mt-6 bg-stone-50 rounded-xl border border-stone-200 p-4">
        <div class="text-xs font-semibold text-zinc-500 mb-2">
            <i data-lucide="link" class="w-3.5 h-3.5 inline mr-1"></i>
            Enlace de tu cita (guárdalo)
        </div>
        <div class="flex items-center gap-2">
            <input type="text" readonly
                   value="<?= url('reservar/gestionar') ?>?token=<?= htmlspecialchars($cita['token']) ?>"
                   class="flex-1 bg-white border border-stone-200 rounded-lg px-3 py-2 text-xs text-zinc-600 font-mono truncate focus:outline-none">
            <button type="button"
                    onclick="navigator.clipboard.writeText(this.previousElementSibling.value).then(()=>{this.textContent='¡Copiado!';setTimeout(()=>{this.textContent='Copiar'},1800)})"
                    class="flex-shrink-0 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold
                           px-3 py-2 rounded-lg transition-colors">
                Copiar
            </button>
        </div>
        <p class="text-xs text-zinc-400 mt-1.5">Usa este enlace para reprogramar o cancelar tu cita.</p>
    </div>

</div>

<script>
function gestionar() {
    return {
        panel:         null,
        fecha:         '',
        slots:         [],
        horaInicio:    '',
        horaFin:       '',
        horaInicioFmt: '',
        horaFinFmt:    '',
        cargando:      false,
        servicioId: <?= (int) ($cita['servicio_id'] ?? 0) ?>,

        init() {},

        async cargarSlots() {
            if (!this.fecha || !this.servicioId) return;
            this.cargando      = true;
            this.slots         = [];
            this.horaInicio    = '';
            this.horaFin       = '';
            this.horaInicioFmt = '';
            this.horaFinFmt    = '';
            try {
                const r = await fetch(`${baseUrl}/reservar/horarios?fecha=${this.fecha}&servicio_id=${this.servicioId}&excluir=<?= (int)($cita['id'] ?? 0) ?>`);
                this.slots = await r.json();
            } catch(e) { this.slots = []; }
            this.cargando = false;
        },

        seleccionarSlot(slot) {
            if (!slot.disponible) return;
            this.horaInicio    = slot.hora_inicio;
            this.horaFin       = slot.hora_fin;
            this.horaInicioFmt = slot.hora_inicio_fmt;
            this.horaFinFmt    = slot.hora_fin_fmt;
        },

        confirmarRep() {
            if (!this.horaInicio) return;
            this.$el.submit();
        }
    };
}
</script>
