<?php
$visitas = (int)$cliente['total_visitas'];
if ($visitas >= 10)     { $badgeClass = 'bg-blue-100 text-blue-700'; $badgeLabel = 'VIP'; }
elseif ($visitas >= 5)  { $badgeClass = 'bg-blue-100 text-blue-700';   $badgeLabel = 'Frecuente'; }
else                    { $badgeClass = 'bg-zinc-100 text-zinc-600';    $badgeLabel = 'Regular'; }
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-xs text-zinc-400 mb-5">
    <a href="<?= url('clientes') ?>" class="hover:text-blue-600 transition-colors">Clientes Frecuentes</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-zinc-600 font-medium"><?= htmlspecialchars($cliente['nombre']) ?></span>
</div>

<!-- Card del cliente -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 mb-5">
    <div class="flex items-center gap-4 flex-wrap">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center
                    text-xl font-bold text-blue-700 flex-shrink-0">
            <?= mb_strtoupper(mb_substr($cliente['nombre'], 0, 1)) ?>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-bold text-zinc-900"><?= htmlspecialchars($cliente['nombre']) ?></h2>
            <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-zinc-500">
                <?php if ($cliente['telefono']): ?>
                <span class="flex items-center gap-1">
                    <i data-lucide="phone" class="w-3 h-3"></i>
                    <?= htmlspecialchars($cliente['telefono']) ?>
                </span>
                <?php endif; ?>
                <?php if ($cliente['primera_visita']): ?>
                <span class="flex items-center gap-1">
                    <i data-lucide="calendar-plus" class="w-3 h-3"></i>
                    Cliente desde <?= fechaEsp($cliente['primera_visita']) ?>
                </span>
                <?php endif; ?>
                <?php if ($cliente['ultima_visita']): ?>
                <span class="flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    Última visita <?= fechaEsp($cliente['ultima_visita']) ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 <?= $badgeClass ?> text-xs font-bold
                     px-3 py-1.5 rounded-full flex-shrink-0">
            <i data-lucide="star" class="w-3.5 h-3.5"></i>
            <?= $badgeLabel ?> · <?= $visitas ?> visita<?= $visitas !== 1 ? 's' : '' ?>
        </span>
    </div>
</div>

<!-- Historial de visitas -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-stone-100 flex items-center gap-2">
        <i data-lucide="history" class="w-4 h-4 text-blue-500"></i>
        <h3 class="text-sm font-semibold text-zinc-800">Historial de visitas</h3>
        <span class="ml-auto text-xs bg-stone-100 text-zinc-500 font-semibold px-2 py-0.5 rounded-full">
            <?= count($historial) ?> registro<?= count($historial) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if (empty($historial)): ?>
    <div class="p-10 text-center text-sm text-zinc-400">
        No hay visitas registradas aún.
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500">Fecha</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Servicio</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold text-zinc-500">Precio cobrado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                <?php foreach ($historial as $h): ?>
                <tr class="hover:bg-stone-50/60 transition-colors">
                    <td class="px-5 py-3.5 text-zinc-600 text-xs">
                        <?= fechaEsp($h['fecha']) ?>
                    </td>
                    <td class="px-3 py-3.5">
                        <?php if ($h['servicio_nombre']): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700
                                     border border-blue-100 rounded-full px-2.5 py-0.5">
                            <i data-lucide="scissors" class="w-3 h-3"></i>
                            <?= htmlspecialchars($h['servicio_nombre']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-zinc-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3.5 text-right font-semibold text-zinc-800">
                        <?= moneda((float)$h['precio_cobrado']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
