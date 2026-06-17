<?php
$hoy      = date('Y-m-d');
$maxFecha = date('Y-m-d', strtotime('+30 days'));
$datos    = $datos ?? [];

// Pre-fill from POST (error case)
$preServicioId  = (int) ($datos['servicio_id']  ?? 0);
$preFecha       = htmlspecialchars($datos['fecha']       ?? '', ENT_QUOTES);
$preHoraInicio  = htmlspecialchars($datos['hora_inicio'] ?? '', ENT_QUOTES);
$preHoraFin     = htmlspecialchars($datos['hora_fin']    ?? '', ENT_QUOTES);
$preNombre      = htmlspecialchars($datos['nombre']      ?? '', ENT_QUOTES);
$preTelefono    = htmlspecialchars($datos['telefono']    ?? '', ENT_QUOTES);

$pasoInicial = !empty($errores) ? 3 : 1;

// Info del servicio pre-seleccionado (para Alpine.js)
$svcPre = null;
foreach ($servicios as $s) {
    if ((int)$s['id'] === $preServicioId) { $svcPre = $s; break; }
}
?>

<div class="max-w-lg mx-auto px-4 py-6 pb-10">

    <!-- Título de página -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-zinc-900">Reserva tu cita</h1>
        <p class="text-sm text-zinc-500 mt-1">Sin registro — solo tu nombre y ya</p>
    </div>

    <!-- Indicadores de paso -->
    <div class="flex items-center justify-center gap-0 mb-8" x-data x-cloak>
        <?php foreach ([1 => 'Servicio', 2 => 'Fecha y hora', 3 => 'Confirmar'] as $n => $label): ?>
        <div class="flex flex-col items-center">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                        <?= $pasoInicial >= $n ? 'bg-amber-500 text-white' : 'bg-stone-200 text-zinc-400' ?>">
                <?= $n ?>
            </div>
            <span class="text-xs mt-1 <?= $pasoInicial >= $n ? 'text-amber-600 font-medium' : 'text-zinc-400' ?> hidden sm:block">
                <?= $label ?>
            </span>
        </div>
        <?php if ($n < 3): ?>
        <div class="w-12 h-0.5 mx-1 mb-4 <?= $pasoInicial > $n ? 'bg-amber-500' : 'bg-stone-200' ?>"></div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Errores -->
    <?php if (!empty($errores)): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 flex gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
        <div class="space-y-0.5">
            <?php foreach ($errores as $e): ?>
            <p class="text-sm text-red-700"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ══ Alpine.js component ══ -->
    <div x-data="reserva()" x-init="init()">

        <!-- ── PASO 1: Servicio ──────────────────────────── -->
        <div x-show="paso === 1" x-cloak>
            <h2 class="text-base font-bold text-zinc-800 mb-1">¿Qué servicio necesitas?</h2>
            <p class="text-sm text-zinc-500 mb-4">Elige un servicio para continuar</p>

            <?php if (empty($servicios)): ?>
            <div class="bg-white rounded-2xl border border-stone-200 p-8 text-center">
                <i data-lucide="scissors" class="w-8 h-8 text-zinc-300 mx-auto mb-2"></i>
                <p class="text-sm text-zinc-500">No hay servicios disponibles.</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 gap-3">
                <?php foreach ($servicios as $s): ?>
                <button type="button"
                        @click="seleccionarServicio(<?= (int)$s['id'] ?>, '<?= addslashes(htmlspecialchars($s['nombre'])) ?>', <?= (float)$s['precio'] ?>, <?= (int)$s['duracion_minutos'] ?>)"
                        class="slot-btn w-full p-5 rounded-2xl border-2 text-left transition-all active:scale-[.98]"
                        :class="servicioId === <?= (int)$s['id'] ?>
                            ? 'border-amber-500 bg-amber-50 shadow-md'
                            : 'border-stone-200 bg-white hover:border-amber-300 hover:shadow-sm'">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-zinc-800"><?= htmlspecialchars($s['nombre']) ?></div>
                            <?php if ($s['descripcion']): ?>
                            <div class="text-sm text-zinc-500 mt-0.5"><?= htmlspecialchars($s['descripcion']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-lg font-black text-amber-600"><?= moneda((float)$s['precio']) ?></div>
                            <div class="text-xs text-zinc-400"><?= duracionFmt((int)$s['duracion_minutos']) ?></div>
                        </div>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── PASO 2: Fecha + Hora ──────────────────────── -->
        <div x-show="paso === 2" x-cloak>

            <!-- Chip de servicio seleccionado -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-full px-3 py-1.5">
                    <i data-lucide="scissors" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span class="text-sm font-medium text-amber-800" x-text="servicioNombre"></span>
                    <span class="text-xs text-amber-600" x-text="'· ' + precioFmt"></span>
                </div>
                <button type="button" @click="paso = 1"
                        class="text-xs text-zinc-400 hover:text-zinc-600 transition-colors underline">
                    Cambiar
                </button>
            </div>

            <!-- Selector de fecha -->
            <div class="bg-white rounded-xl border border-stone-200 p-4 mb-4 shadow-sm">
                <label class="block text-xs font-semibold text-zinc-600 mb-2">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1"></i>Selecciona una fecha
                </label>
                <input type="date"
                       x-model="fecha"
                       @change="cargarSlots()"
                       min="<?= $hoy ?>"
                       max="<?= $maxFecha ?>"
                       class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                <p class="text-xs text-zinc-400 mt-1.5">Lunes a sábado · Máximo 30 días</p>
            </div>

            <!-- Slots de hora -->
            <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm mb-4">
                <div class="text-xs font-semibold text-zinc-600 mb-3">
                    <i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1"></i>Horas disponibles
                </div>

                <!-- Estado vacío: sin fecha -->
                <div x-show="!fecha" class="text-center py-6">
                    <i data-lucide="calendar-search" class="w-7 h-7 text-zinc-200 mx-auto mb-2"></i>
                    <p class="text-xs text-zinc-400">Elige una fecha para ver los horarios</p>
                </div>

                <!-- Cargando -->
                <div x-show="fecha && cargando" class="text-center py-6">
                    <div class="w-6 h-6 border-2 border-amber-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    <p class="text-xs text-zinc-400">Verificando disponibilidad…</p>
                </div>

                <!-- Sin slots disponibles (cerrado ese día) -->
                <div x-show="fecha && !cargando && slots.length === 0 && fecha !== ''"
                     class="text-center py-6">
                    <i data-lucide="calendar-off" class="w-7 h-7 text-zinc-200 mx-auto mb-2"></i>
                    <p class="text-xs text-zinc-400">No hay horarios disponibles ese día.</p>
                    <p class="text-xs text-zinc-300 mt-0.5">Prueba con otra fecha</p>
                </div>

                <!-- Grid de slots -->
                <div x-show="slots.length > 0 && !cargando"
                     class="grid grid-cols-3 gap-2.5">
                    <template x-for="slot in slots" :key="slot.hora_inicio">
                        <button type="button"
                                @click="seleccionarSlot(slot)"
                                :disabled="!slot.disponible"
                                class="slot-btn py-4 px-2 rounded-2xl border-2 text-center flex flex-col items-center gap-0.5 transition-all active:scale-[.97]"
                                :class="horaInicio === slot.hora_inicio
                                    ? 'border-amber-500 bg-amber-500 text-white shadow-md'
                                    : slot.disponible
                                        ? 'border-stone-200 bg-white hover:border-amber-400 hover:shadow-sm text-zinc-700'
                                        : 'border-stone-100 bg-stone-50 text-zinc-300 cursor-not-allowed'">
                            <span class="text-base font-black leading-none" x-text="slot.hora_inicio"></span>
                            <span class="text-[10px] font-normal opacity-70 leading-none" x-text="slot.hora_fin"
                                  :class="!slot.disponible ? 'line-through' : ''"></span>
                        </button>
                    </template>
                </div>

                <!-- Leyenda -->
                <div x-show="slots.length > 0 && !cargando" class="flex items-center gap-4 mt-3 pt-3 border-t border-stone-100">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded border-2 border-stone-200 bg-white"></div>
                        <span class="text-xs text-zinc-400">Disponible</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded border-2 border-amber-500 bg-amber-500"></div>
                        <span class="text-xs text-zinc-400">Seleccionado</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded border-2 border-stone-100 bg-stone-50"></div>
                        <span class="text-xs text-zinc-400">Ocupado</span>
                    </div>
                </div>
            </div>

            <!-- Botón continuar -->
            <button type="button"
                    @click="avanzarPaso3()"
                    :disabled="!horaInicio"
                    class="slot-btn w-full py-4 rounded-2xl font-bold text-base transition-all active:scale-[.98]"
                    :class="horaInicio
                        ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-md'
                        : 'bg-stone-200 text-zinc-400 cursor-not-allowed'">
                <span x-text="horaInicio ? 'Continuar · ' + slotLabel : 'Selecciona una hora'"></span>
            </button>
        </div>

        <!-- ── PASO 3: Datos + Confirmar ─────────────────── -->
        <div x-show="paso === 3" x-cloak>

            <!-- Resumen de la selección -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                <div class="text-xs font-semibold text-amber-700 mb-2 uppercase tracking-wide">Tu selección</div>
                <div class="space-y-1.5 text-sm text-amber-900">
                    <div class="flex items-center gap-2">
                        <i data-lucide="scissors" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
                        <span x-text="servicioNombre" class="font-medium"></span>
                        <span x-text="'— ' + precioFmt" class="text-amber-600 ml-auto"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
                        <span x-text="fechaFmt(fecha)"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
                        <span x-text="slotLabel"></span>
                        <span x-text="'(' + servicioDuracion + ' min)'" class="text-amber-600 ml-1"></span>
                    </div>
                </div>
                <button type="button" @click="paso = 2"
                        class="text-xs text-amber-600 hover:text-amber-800 underline mt-2 block">
                    Cambiar fecha/hora
                </button>
            </div>

            <!-- Formulario de datos -->
            <form method="POST" action="<?= url('reservar/guardar') ?>" class="space-y-4">
                <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="servicio_id"  :value="servicioId">
                <input type="hidden" name="fecha"        :value="fecha">
                <input type="hidden" name="hora_inicio"  :value="horaInicio">
                <input type="hidden" name="hora_fin"     :value="horaFin">

                <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm space-y-4">
                    <div class="text-xs font-semibold text-zinc-600 uppercase tracking-wide">Tus datos</div>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                            Nombre o apodo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" x-model="nombre"
                               value="<?= $preNombre ?>"
                               placeholder="¿Cómo te llamamos?"
                               class="w-full border border-zinc-200 rounded-xl px-4 py-3.5 text-base
                                      focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                               autofocus required>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                            Teléfono <span class="text-zinc-400 font-normal">(opcional)</span>
                        </label>
                        <input type="tel" name="telefono" x-model="telefono"
                               value="<?= $preTelefono ?>"
                               placeholder="Para recordatorios"
                               class="w-full border border-zinc-200 rounded-xl px-4 py-3.5 text-base
                                      focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Confirmar -->
                <button type="submit"
                        class="slot-btn w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4
                               rounded-2xl shadow-md text-base flex items-center justify-center gap-2 active:scale-[.98] transition-all">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    Confirmar reserva
                </button>

                <p class="text-xs text-zinc-400 text-center">
                    Al confirmar aceptas la política de no-show de la barbería.
                    Guarda el enlace de tu cita para poder cancelar o reprogramar.
                </p>
            </form>
        </div>

    </div><!-- /Alpine -->
</div>

<script>
function reserva() {
    return {
        paso:            <?= $pasoInicial ?>,
        servicioId:      <?= $preServicioId ?>,
        servicioNombre:  '<?= addslashes($svcPre['nombre'] ?? '') ?>',
        servicioPrecio:  <?= (float) ($svcPre['precio'] ?? 0) ?>,
        servicioDuracion: <?= (int) ($svcPre['duracion_minutos'] ?? 0) ?>,
        fecha:           '<?= $preFecha ?>',
        slots:           [],
        horaInicio:      '<?= $preHoraInicio ?>',
        horaFin:         '<?= $preHoraFin ?>',
        nombre:          '<?= $preNombre ?>',
        telefono:        '<?= $preTelefono ?>',
        cargando:        false,

        init() {
            if (this.fecha && this.servicioId) this.cargarSlots();
        },

        seleccionarServicio(id, nombre, precio, duracion) {
            this.servicioId      = id;
            this.servicioNombre  = nombre;
            this.servicioPrecio  = precio;
            this.servicioDuracion = duracion;
            this.horaInicio = '';
            this.horaFin    = '';
            this.paso = 2;
            if (this.fecha) this.cargarSlots();
        },

        async cargarSlots() {
            if (!this.fecha || !this.servicioId) return;
            this.cargando   = true;
            this.slots      = [];
            this.horaInicio = '';
            this.horaFin    = '';
            try {
                const r = await fetch(`${baseUrl}/reservar/horarios?fecha=${this.fecha}&servicio_id=${this.servicioId}`);
                this.slots = await r.json();
            } catch(e) { this.slots = []; }
            this.cargando = false;
        },

        seleccionarSlot(slot) {
            if (!slot.disponible) return;
            this.horaInicio = slot.hora_inicio;
            this.horaFin    = slot.hora_fin;
        },

        avanzarPaso3() {
            if (!this.horaInicio) return;
            this.paso = 3;
        },

        fechaFmt(f) {
            if (!f) return '';
            const [y, m, d] = f.split('-');
            const meses = ['','ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
            const dias  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
            const dt    = new Date(y, m - 1, d);
            return `${dias[dt.getDay()]} ${d} de ${meses[m]} de ${y}`;
        },

        get slotLabel() {
            return this.horaInicio ? `${this.horaInicio} – ${this.horaFin}` : '';
        },

        get precioFmt() {
            return 'L. ' + parseFloat(this.servicioPrecio).toFixed(2);
        }
    };
}
</script>
