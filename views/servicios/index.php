<?php /** @var array $servicios */ ?>

<!-- Header de página -->
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-base font-bold text-zinc-900">Servicios de la barbería</h2>
        <p class="text-xs text-zinc-500 mt-0.5">Gestiona los cortes y servicios que ofreces</p>
    </div>
    <a href="<?= url('servicios/crear') ?>"
       class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold
              px-4 py-2 rounded-lg transition-colors shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nuevo servicio
    </a>
</div>

<!-- Tabla / lista -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">

    <?php if (empty($servicios)): ?>
    <!-- Estado vacío -->
    <div class="p-12 text-center">
        <div class="w-14 h-14 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="scissors" class="w-6 h-6 text-zinc-400"></i>
        </div>
        <p class="text-sm font-semibold text-zinc-600">Sin servicios registrados</p>
        <p class="text-xs text-zinc-400 mt-1 mb-4">Agrega los cortes y servicios que ofrece tu barbería</p>
        <a href="<?= url('servicios/crear') ?>"
           class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm
                  font-semibold px-4 py-2 rounded-lg transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Agregar primer servicio
        </a>
    </div>

    <?php else: ?>
    <!-- Tabla desktop -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide">Servicio</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide hidden sm:table-cell">Precio</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide hidden md:table-cell">Duración</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide">Estado</th>
                    <th class="px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                <?php foreach ($servicios as $s): ?>
                <tr class="hover:bg-stone-50 transition-colors">
                    <!-- Nombre + descripción -->
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-zinc-800"><?= htmlspecialchars($s['nombre']) ?></div>
                        <?php if ($s['descripcion']): ?>
                        <div class="text-xs text-zinc-400 mt-0.5 line-clamp-1">
                            <?= htmlspecialchars($s['descripcion']) ?>
                        </div>
                        <?php endif; ?>
                        <!-- Precio + duración solo en móvil -->
                        <div class="sm:hidden mt-1 flex items-center gap-3 text-xs text-zinc-500">
                            <span><?= moneda((float)$s['precio']) ?></span>
                            <span>·</span>
                            <span><?= duracionFmt((int)$s['duracion_minutos']) ?></span>
                        </div>
                    </td>

                    <!-- Precio -->
                    <td class="px-5 py-3.5 text-right font-semibold text-zinc-700 hidden sm:table-cell">
                        <?= moneda((float)$s['precio']) ?>
                    </td>

                    <!-- Duración -->
                    <td class="px-5 py-3.5 text-center text-zinc-500 hidden md:table-cell">
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            <?= duracionFmt((int)$s['duracion_minutos']) ?>
                        </span>
                    </td>

                    <!-- Estado -->
                    <td class="px-5 py-3.5 text-center">
                        <?php if ($s['activo']): ?>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-zinc-100 text-zinc-500 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full"></span>Inactivo
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Acciones -->
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1">

                            <!-- Editar -->
                            <a href="<?= url('servicios/editar') ?>?id=<?= $s['id'] ?>"
                               class="p-1.5 rounded-lg text-zinc-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>

                            <!-- Toggle activo -->
                            <form method="POST" action="<?= url('servicios/toggle') ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit"
                                        class="p-1.5 rounded-lg transition-colors <?= $s['activo'] ? 'text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100' : 'text-blue-500 hover:text-blue-700 hover:bg-blue-50' ?>"
                                        title="<?= $s['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i data-lucide="<?= $s['activo'] ? 'eye-off' : 'eye' ?>" class="w-4 h-4"></i>
                                </button>
                            </form>

                            <!-- Eliminar -->
                            <form method="POST" action="<?= url('servicios/eliminar') ?>"
                                  x-data
                                  @submit.prevent="
                                    Swal.fire({
                                        title: '¿Eliminar servicio?',
                                        text: 'Si tiene citas asociadas, solo será desactivado.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar',
                                    }).then(r => r.isConfirmed && $el.submit())
                                  ">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-zinc-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                        title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer de tabla -->
    <div class="px-5 py-3 border-t border-stone-100 bg-stone-50 text-xs text-zinc-400">
        <?= count($servicios) ?> servicio<?= count($servicios) !== 1 ? 's' : '' ?> registrado<?= count($servicios) !== 1 ? 's' : '' ?>
        &nbsp;·&nbsp;
        <?= count(array_filter($servicios, fn($s) => $s['activo'])) ?> activo<?= count(array_filter($servicios, fn($s) => $s['activo'])) !== 1 ? 's' : '' ?>
    </div>
    <?php endif; ?>

</div>

<?php
// Pasar CSRF token para los formularios inline (toggle/eliminar)
$csrf_token = $csrf_token ?? '';
?>
