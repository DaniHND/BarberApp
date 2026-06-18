<?php /** @var array $barberos */ ?>

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-base font-bold text-zinc-900">Barberos</h2>
        <p class="text-xs text-zinc-500 mt-0.5">Gestiona el equipo y sus horarios de disponibilidad</p>
    </div>
    <a href="<?= url('barberos/crear') ?>"
       class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold
              px-4 py-2 rounded-lg transition-colors shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        Agregar barbero
    </a>
</div>

<div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">

    <?php if (empty($barberos)): ?>
    <div class="p-12 text-center">
        <div class="w-14 h-14 bg-stone-100 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="user-x" class="w-6 h-6 text-zinc-400"></i>
        </div>
        <p class="text-sm font-semibold text-zinc-600">Sin barberos registrados</p>
        <p class="text-xs text-zinc-400 mt-1 mb-4">
            Agrega los barberos de tu equipo para que los clientes puedan elegirlos al reservar
        </p>
        <a href="<?= url('barberos/crear') ?>"
           class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm
                  font-semibold px-4 py-2 rounded-lg transition-colors">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Agregar primer barbero
        </a>
    </div>

    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide">Barbero</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide hidden sm:table-cell">Horario</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide">Estado</th>
                    <th class="px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wide text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                <?php foreach ($barberos as $b): ?>
                <tr class="hover:bg-stone-50 transition-colors">

                    <!-- Nombre + descripción -->
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-700 font-bold text-sm">
                                    <?= strtoupper(mb_substr($b['nombre'], 0, 1)) ?>
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-zinc-800"><?= htmlspecialchars($b['nombre']) ?></div>
                                <?php if ($b['descripcion']): ?>
                                <div class="text-xs text-zinc-400 mt-0.5"><?= htmlspecialchars($b['descripcion']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <!-- Horario (solo desktop) -->
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell">
                        <a href="<?= url('barberos/horario') ?>?id=<?= $b['id'] ?>"
                           class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-medium
                                  bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-full transition-colors">
                            <i data-lucide="calendar-cog" class="w-3.5 h-3.5"></i>
                            Gestionar horario
                        </a>
                    </td>

                    <!-- Estado -->
                    <td class="px-5 py-3.5 text-center">
                        <?php if ($b['activo']): ?>
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

                            <!-- Horario (móvil) -->
                            <a href="<?= url('barberos/horario') ?>?id=<?= $b['id'] ?>"
                               class="sm:hidden p-1.5 rounded-lg text-zinc-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                               title="Horario">
                                <i data-lucide="calendar-cog" class="w-4 h-4"></i>
                            </a>

                            <!-- Editar -->
                            <a href="<?= url('barberos/editar') ?>?id=<?= $b['id'] ?>"
                               class="p-1.5 rounded-lg text-zinc-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>

                            <!-- Toggle activo/inactivo -->
                            <form method="POST" action="<?= url('barberos/toggle') ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                <button type="submit"
                                        class="p-1.5 rounded-lg transition-colors <?= $b['activo'] ? 'text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100' : 'text-blue-500 hover:text-blue-700 hover:bg-blue-50' ?>"
                                        title="<?= $b['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i data-lucide="<?= $b['activo'] ? 'eye-off' : 'eye' ?>" class="w-4 h-4"></i>
                                </button>
                            </form>

                            <!-- Eliminar -->
                            <form method="POST" action="<?= url('barberos/eliminar') ?>"
                                  x-data
                                  @submit.prevent="
                                    Swal.fire({
                                        title: '¿Eliminar barbero?',
                                        text: 'Se eliminarán su horario y días bloqueados. Las citas existentes no se verán afectadas.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar',
                                    }).then(r => r.isConfirmed && $el.submit())
                                  ">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
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
    <div class="px-5 py-3 border-t border-stone-100 bg-stone-50 text-xs text-zinc-400">
        <?= count($barberos) ?> barbero<?= count($barberos) !== 1 ? 's' : '' ?> registrado<?= count($barberos) !== 1 ? 's' : '' ?>
        &nbsp;·&nbsp;
        <?= count(array_filter($barberos, fn($b) => $b['activo'])) ?> activo<?= count(array_filter($barberos, fn($b) => $b['activo'])) !== 1 ? 's' : '' ?>
    </div>
    <?php endif; ?>
</div>
