<?php
// $servicio puede ser el registro de BD o los datos re-enviados tras error de validación
$v = $servicio;
?>

<!-- Breadcrumb -->
<div class="mb-5">
    <a href="<?= url('servicios') ?>"
       class="inline-flex items-center gap-1.5 text-sm text-zinc-500 hover:text-zinc-700 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Volver a servicios
    </a>
</div>

<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-stone-100 flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <i data-lucide="pencil" class="w-4 h-4 text-blue-500"></i>
            </div>
            <h2 class="text-sm font-bold text-zinc-900">Editar servicio</h2>
        </div>

        <!-- Errores -->
        <?php if (!empty($errores)): ?>
        <div class="mx-5 mt-4 bg-red-50 border border-red-200 rounded-lg p-3.5">
            <div class="flex items-start gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                <ul class="text-sm text-red-700 space-y-1">
                    <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="POST" action="<?= url('servicios/actualizar') ?>"
              x-data="{ activo: <?= !empty($v['activo']) ? 'true' : 'false' ?> }"
              class="p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id"         value="<?= (int)($v['id'] ?? 0) ?>">

            <!-- Nombre -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5" for="nombre">
                    Nombre del servicio <span class="text-red-500">*</span>
                </label>
                <input id="nombre" type="text" name="nombre"
                       value="<?= htmlspecialchars($v['nombre'] ?? '') ?>"
                       class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow"
                       placeholder="Ej. Corte Clásico" autofocus required>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5" for="descripcion">
                    Descripción <span class="text-zinc-400 font-normal">(opcional)</span>
                </label>
                <textarea id="descripcion" name="descripcion" rows="2"
                          class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                 transition-shadow resize-none"
                          placeholder="Descripción breve..."><?= htmlspecialchars($v['descripcion'] ?? '') ?></textarea>
            </div>

            <!-- Precio + Duración -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5" for="precio">
                        Precio (L.) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400 font-medium select-none">L.</span>
                        <input id="precio" type="number" name="precio" step="0.50" min="0"
                               value="<?= htmlspecialchars($v['precio'] ?? '') ?>"
                               class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow"
                               placeholder="0.00" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5" for="duracion">
                        Duración (min) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="duracion" type="number" name="duracion_minutos" min="5" step="5"
                               value="<?= htmlspecialchars($v['duracion_minutos'] ?? '30') ?>"
                               class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 pr-12 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow"
                               placeholder="30" required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400 select-none">min</span>
                    </div>
                </div>
            </div>

            <!-- Toggle activo -->
            <div class="flex items-center justify-between p-3.5 bg-stone-50 rounded-lg border border-stone-200">
                <div>
                    <div class="text-xs font-semibold text-zinc-700">Servicio activo</div>
                    <div class="text-xs text-zinc-400 mt-0.5">Los inactivos no aparecen en el sistema de reservas</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-4">
                    <input type="checkbox" name="activo" value="1" class="sr-only peer"
                           x-model="activo" <?= !empty($v['activo']) ? 'checked' : '' ?>>
                    <div class="w-10 h-5 bg-zinc-200 rounded-full peer
                                peer-checked:bg-blue-500
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-between pt-1">
                <a href="<?= url('servicios') ?>"
                   class="text-sm text-zinc-500 hover:text-zinc-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white
                               text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Actualizar servicio
                </button>
            </div>
        </form>
    </div>
</div>
