<?php
$datos = $datos ?? [];
?>

<div class="max-w-lg">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-5">
        <a href="<?= url('barberos') ?>" class="hover:text-zinc-600 transition-colors">Barberos</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-zinc-600">Agregar barbero</span>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-6">
        <h2 class="text-base font-bold text-zinc-900 mb-1">Nuevo barbero</h2>
        <p class="text-xs text-zinc-500 mb-5">Después podrás configurar su horario semanal y días libres.</p>

        <?php if (!empty($errores)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 text-sm text-red-700 space-y-0.5">
            <?php foreach ($errores as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('barberos/guardar') ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <!-- Nombre -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre"
                       value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>"
                       placeholder="Ej: Marco Ramírez"
                       class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       autofocus required>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                    Especialidad <span class="text-zinc-400 font-normal">(opcional)</span>
                </label>
                <input type="text" name="descripcion"
                       value="<?= htmlspecialchars($datos['descripcion'] ?? '') ?>"
                       placeholder="Ej: Especialista en degradados"
                       class="w-full border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Orden -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                    Orden de aparición <span class="text-zinc-400 font-normal">(0 = primero)</span>
                </label>
                <input type="number" name="orden" min="0" max="99"
                       value="<?= htmlspecialchars($datos['orden'] ?? '0') ?>"
                       class="w-32 border border-zinc-200 rounded-lg px-3.5 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5
                               rounded-lg transition-colors text-sm">
                    Guardar y configurar horario →
                </button>
                <a href="<?= url('barberos') ?>"
                   class="px-4 py-2.5 rounded-lg border border-stone-200 text-sm font-medium
                          text-zinc-600 hover:bg-stone-50 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
