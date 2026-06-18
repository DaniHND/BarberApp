<?php $hoy = date('H:i'); ?>

<div class="grid lg:grid-cols-3 gap-5">

    <!-- ── Formulario: Agregar a la espera ─────────────── -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-stone-100 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4 text-blue-500"></i>
                <h3 class="text-sm font-semibold text-zinc-800">Agregar a la espera</h3>
            </div>
            <form method="POST" action="<?= url('espera/agregar') ?>" class="p-5 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" placeholder="Nombre del cliente"
                           class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required autofocus>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                        Teléfono <span class="text-zinc-400 font-normal">(opcional)</span>
                    </label>
                    <input type="tel" name="telefono" placeholder="Número de contacto"
                           class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                        Servicio <span class="text-zinc-400 font-normal">(opcional)</span>
                    </label>
                    <select name="servicio_id"
                            class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Sin especificar</option>
                        <?php foreach ($servicios as $s): ?>
                        <option value="<?= (int)$s['id'] ?>">
                            <?= htmlspecialchars($s['nombre']) ?> — <?= moneda((float)$s['precio']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600
                               text-white text-sm font-semibold py-2.5 rounded-lg transition-colors shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Agregar a la espera
                </button>
            </form>

            <!-- Info -->
            <div class="px-5 pb-4">
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700">
                    <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                    Clientes que llegaron sin cita previa. Se atienden en orden de llegada.
                </div>
            </div>
        </div>
    </div>

    <!-- ── Lista de espera ──────────────────────────────── -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-stone-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-zinc-800 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-blue-500"></i>
                    En espera ahora
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2 py-0.5 rounded-full">
                        <?= count($lista) ?> esperando
                    </span>
                    <span class="text-xs text-zinc-400"><?= $hoy ?></span>
                </div>
            </div>

            <?php if (empty($lista)): ?>
            <div class="p-10 text-center">
                <div class="w-12 h-12 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-3">
                    <i data-lucide="users" class="w-5 h-5 text-zinc-400"></i>
                </div>
                <p class="text-sm font-medium text-zinc-600">La sala de espera está vacía</p>
                <p class="text-xs text-zinc-400 mt-1">Agrega clientes que llegan sin cita usando el formulario</p>
            </div>
            <?php else: ?>

            <div class="divide-y divide-stone-50">
                <?php foreach ($lista as $i => $p):
                    $llegada = date('H:i', strtotime($p['fecha_llegada']));
                    $espera  = round((time() - strtotime($p['fecha_llegada'])) / 60);
                ?>
                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-stone-50/50 transition-colors">

                    <!-- Número de turno -->
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center
                                text-white font-bold text-sm flex-shrink-0">
                        <?= $i + 1 ?>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-zinc-800 text-sm">
                            <?= htmlspecialchars($p['nombre']) ?>
                        </div>
                        <div class="text-xs text-zinc-400 mt-0.5 flex items-center gap-2 flex-wrap">
                            <span class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                Llegó a las <?= $llegada ?>
                            </span>
                            <?php if ($espera > 0): ?>
                            <span class="<?= $espera > 20 ? 'text-red-400' : 'text-zinc-400' ?>">
                                · <?= $espera ?> min esperando
                            </span>
                            <?php endif; ?>
                            <?php if ($p['telefono']): ?>
                            <span>· <?= htmlspecialchars($p['telefono']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($p['servicio_nombre']): ?>
                        <div class="text-xs text-blue-600 mt-0.5 font-medium">
                            <?= htmlspecialchars($p['servicio_nombre']) ?>
                        </div>
                        <?php else: ?>
                        <div class="text-xs text-zinc-300 mt-0.5">Sin servicio especificado</div>
                        <?php endif; ?>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <!-- Atendido -->
                        <form method="POST" action="<?= url('espera/atender') ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <input type="hidden" name="id"         value="<?= (int)$p['id'] ?>">
                            <button type="submit"
                                    class="flex items-center gap-1.5 text-sm font-semibold px-4 py-2.5
                                           bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl transition-colors shadow-sm">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Atendido
                            </button>
                        </form>

                        <!-- Quitar -->
                        <form method="POST" action="<?= url('espera/cancelar') ?>"
                              x-data
                              @submit.prevent="
                                Swal.fire({
                                    title:'¿Quitar de la espera?',
                                    icon:'question',
                                    showCancelButton:true,
                                    confirmButtonColor:'#dc2626',
                                    cancelButtonColor:'#6b7280',
                                    confirmButtonText:'Sí, quitar',
                                    cancelButtonText:'No',
                                }).then(r => r.isConfirmed && $el.submit())">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <input type="hidden" name="id"         value="<?= (int)$p['id'] ?>">
                            <button type="submit"
                                    class="p-2.5 rounded-xl text-zinc-400 hover:text-red-500 hover:bg-red-50 transition-colors border border-stone-200"
                                    title="Quitar de la espera">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <div class="px-5 py-3 border-t border-stone-100 bg-stone-50 text-xs text-zinc-400">
                Orden de llegada · La página se actualiza al cambiar de estado
            </div>

            <?php endif; ?>
        </div>
    </div>

</div>
