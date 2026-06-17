<!-- Header de página -->
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-base font-bold text-zinc-900">Clientes Frecuentes</h2>
        <p class="text-xs text-zinc-400 mt-0.5">Registrados automáticamente al atender citas</p>
    </div>
    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200
                 text-xs font-semibold px-3 py-1.5 rounded-full">
        <i data-lucide="users" class="w-3.5 h-3.5"></i>
        <?= count($clientes) ?>
    </span>
</div>

<?php if (empty($clientes)): ?>

<!-- Estado vacío -->
<div class="bg-white rounded-2xl border border-stone-200 shadow-sm">
    <div class="p-10 text-center">
        <div class="w-16 h-16 bg-amber-50 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="heart-handshake" class="w-7 h-7 text-amber-500"></i>
        </div>
        <h3 class="text-base font-semibold text-zinc-700 mb-1">Sin clientes frecuentes aún</h3>
        <p class="text-sm text-zinc-400 max-w-xs mx-auto">
            Cuando marques una cita como <strong>Atendida</strong>, el cliente aparecerá aquí automáticamente.
        </p>
        <a href="<?= url('citas') ?>"
           class="inline-flex items-center gap-2 mt-4 bg-amber-500 hover:bg-amber-600 text-white
                  text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
            <i data-lucide="calendar-days" class="w-4 h-4"></i>
            Ir a la agenda
        </a>
    </div>
</div>

<?php else: ?>

<!-- ── VISTA MÓVIL: Cards apiladas ──────────────────────── -->
<div class="block md:hidden space-y-3">
    <?php foreach ($clientes as $i => $c):
        $visitas = (int)$c['total_visitas'];
        if ($visitas >= 10)    { $badgeBg = 'bg-amber-100'; $badgeText = 'text-amber-700'; $badgeDot = 'bg-amber-400'; $badgeLabel = 'VIP'; }
        elseif ($visitas >= 5) { $badgeBg = 'bg-blue-100';  $badgeText = 'text-blue-700';  $badgeDot = 'bg-blue-400';  $badgeLabel = 'Frecuente'; }
        else                   { $badgeBg = 'bg-zinc-100';  $badgeText = 'text-zinc-600';  $badgeDot = 'bg-zinc-300';  $badgeLabel = 'Regular'; }
    ?>
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-4 flex items-center gap-3">

        <!-- Posición + Avatar -->
        <div class="relative flex-shrink-0">
            <div class="w-12 h-12 rounded-full <?= $badgeBg ?> flex items-center justify-center
                        text-xl font-bold <?= $badgeText ?>">
                <?= mb_strtoupper(mb_substr($c['nombre'], 0, 1)) ?>
            </div>
            <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-zinc-700 text-white
                         text-[10px] font-bold flex items-center justify-center leading-none">
                <?= $i + 1 ?>
            </span>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
            <div class="font-semibold text-zinc-900 text-sm truncate"><?= htmlspecialchars($c['nombre']) ?></div>
            <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5 mt-0.5">
                <?php if ($c['telefono']): ?>
                <span class="text-xs text-zinc-400 flex items-center gap-1">
                    <i data-lucide="phone" class="w-3 h-3"></i><?= htmlspecialchars($c['telefono']) ?>
                </span>
                <?php endif; ?>
                <?php if ($c['servicio_favorito']): ?>
                <span class="text-xs text-amber-600 flex items-center gap-1 truncate max-w-[120px]">
                    <i data-lucide="scissors" class="w-3 h-3 flex-shrink-0"></i><?= htmlspecialchars($c['servicio_favorito']) ?>
                </span>
                <?php endif; ?>
            </div>
            <?php if ($c['ultima_visita']): ?>
            <div class="text-xs text-zinc-400 mt-0.5">Última: <?= fechaEsp($c['ultima_visita']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Visitas + Acción -->
        <div class="flex flex-col items-end gap-2 flex-shrink-0">
            <div class="text-center">
                <span class="text-2xl font-black <?= $badgeText ?>"><?= $visitas ?></span>
                <div class="text-[10px] text-zinc-400 leading-none">visit<?= $visitas !== 1 ? 'as' : 'a' ?></div>
            </div>
            <a href="<?= url('clientes/historial') ?>?id=<?= (int)$c['id'] ?>"
               class="flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-800
                      bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition-colors">
                <i data-lucide="history" class="w-3.5 h-3.5"></i>
                Ver
            </a>
        </div>

    </div>
    <?php endforeach; ?>
    <p class="text-xs text-center text-zinc-400 py-2">
        Ordenados por número de visitas
    </p>
</div>

<!-- ── VISTA DESKTOP: Tabla ─────────────────────────────── -->
<div class="hidden md:block bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">

    <div class="px-5 py-3 border-b border-stone-100 bg-stone-50 flex flex-wrap items-center gap-3 text-xs text-zinc-500">
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>VIP — 10+ visitas</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-400 inline-block"></span>Frecuente — 5-9 visitas</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-zinc-300 inline-block"></span>Regular — 1-4 visitas</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 w-10">#</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Cliente</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Teléfono</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-zinc-500">Visitas</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Primera visita</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Última visita</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Servicio favorito</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold text-zinc-500">Historial</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                <?php foreach ($clientes as $i => $c):
                    $visitas = (int)$c['total_visitas'];
                    if ($visitas >= 10)    { $badge = ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-400', 'label' => 'VIP']; }
                    elseif ($visitas >= 5) { $badge = ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'dot' => 'bg-blue-400',  'label' => 'Frecuente']; }
                    else                   { $badge = ['bg' => 'bg-zinc-100',  'text' => 'text-zinc-600',  'dot' => 'bg-zinc-300',  'label' => 'Regular']; }
                ?>
                <tr class="hover:bg-stone-50/60 transition-colors">
                    <td class="px-5 py-3.5 text-zinc-400 text-xs font-mono"><?= $i + 1 ?></td>
                    <td class="px-3 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full <?= $badge['bg'] ?> flex items-center justify-center
                                        text-xs font-bold <?= $badge['text'] ?> flex-shrink-0">
                                <?= mb_strtoupper(mb_substr($c['nombre'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-semibold text-zinc-800"><?= htmlspecialchars($c['nombre']) ?></div>
                                <span class="inline-flex items-center gap-1 text-xs font-medium <?= $badge['text'] ?> mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $badge['dot'] ?>"></span>
                                    <?= $badge['label'] ?>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5 text-zinc-500 text-sm">
                        <?= $c['telefono'] ? htmlspecialchars($c['telefono']) : '<span class="text-zinc-300 text-xs">—</span>' ?>
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                     <?= $badge['bg'] ?> <?= $badge['text'] ?> font-bold text-sm">
                            <?= $visitas ?>
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-zinc-500 text-xs">
                        <?= $c['primera_visita'] ? fechaEsp($c['primera_visita']) : '<span class="text-zinc-300">—</span>' ?>
                    </td>
                    <td class="px-3 py-3.5 text-zinc-500 text-xs">
                        <?= $c['ultima_visita'] ? fechaEsp($c['ultima_visita']) : '<span class="text-zinc-300">—</span>' ?>
                    </td>
                    <td class="px-3 py-3.5">
                        <?php if ($c['servicio_favorito']): ?>
                        <span class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-700
                                     border border-amber-100 rounded-full px-2 py-0.5">
                            <i data-lucide="scissors" class="w-3 h-3"></i>
                            <?= htmlspecialchars($c['servicio_favorito']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-zinc-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3.5 text-right">
                        <a href="<?= url('clientes/historial') ?>?id=<?= (int)$c['id'] ?>"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500
                                  hover:text-amber-600 hover:bg-amber-50 px-2.5 py-1.5 rounded-lg transition-colors border border-stone-200">
                            <i data-lucide="history" class="w-3.5 h-3.5"></i>
                            Ver historial
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="px-5 py-3 border-t border-stone-100 bg-stone-50 text-xs text-zinc-400">
        Ordenados por número de visitas · Se actualiza automáticamente al atender citas
    </div>

</div>

<?php endif; ?>
