<?php
$mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
?>

<!-- Header de página -->
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-zinc-900">Clientes Frecuentes</h2>
        <p class="text-xs text-zinc-400 mt-0.5">
            Clientes registrados por historial de visitas atendidas
        </p>
    </div>
    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200
                 text-xs font-semibold px-3 py-1.5 rounded-full">
        <i data-lucide="users" class="w-3.5 h-3.5"></i>
        <?= count($clientes) ?> cliente<?= count($clientes) !== 1 ? 's' : '' ?>
    </span>
</div>

<?php if (empty($clientes)): ?>

<!-- Estado vacío -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm">
    <div class="p-12 text-center">
        <div class="w-16 h-16 bg-amber-50 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="heart-handshake" class="w-7 h-7 text-amber-500"></i>
        </div>
        <h3 class="text-base font-semibold text-zinc-700 mb-1">Aún no hay clientes frecuentes</h3>
        <p class="text-sm text-zinc-400 max-w-sm mx-auto">
            Cuando marques una cita como <strong>atendida</strong> en la agenda,
            el cliente quedará registrado aquí automáticamente.
        </p>
        <a href="<?= url('citas') ?>"
           class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-amber-600
                  hover:text-amber-800 underline underline-offset-2">
            <i data-lucide="calendar-days" class="w-4 h-4"></i>
            Ir a la agenda
        </a>
    </div>
</div>

<?php else: ?>

<!-- Tabla de clientes -->
<div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">

    <!-- Leyenda de frecuencia -->
    <div class="px-5 py-3 border-b border-stone-100 bg-stone-50 flex flex-wrap items-center gap-3 text-xs text-zinc-500">
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>VIP — 10+ visitas
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 inline-block"></span>Frecuente — 5-9 visitas
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-zinc-300 inline-block"></span>Regular — 1-4 visitas
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 w-10">#</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500">Cliente</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500 hidden sm:table-cell">Teléfono</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-zinc-500">Visitas</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500 hidden md:table-cell">Primera visita</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500 hidden lg:table-cell">Última visita</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-zinc-500 hidden xl:table-cell">Servicio favorito</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold text-zinc-500">Historial</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                <?php foreach ($clientes as $i => $c):
                    $visitas = (int) $c['total_visitas'];

                    if ($visitas >= 10) {
                        $badge = ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-400', 'label' => 'VIP'];
                    } elseif ($visitas >= 5) {
                        $badge = ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-400', 'label' => 'Frecuente'];
                    } else {
                        $badge = ['bg' => 'bg-zinc-100', 'text' => 'text-zinc-600', 'dot' => 'bg-zinc-300', 'label' => 'Regular'];
                    }
                ?>
                <tr class="hover:bg-stone-50/60 transition-colors">

                    <!-- Rank -->
                    <td class="px-5 py-3.5 text-zinc-400 text-xs font-mono"><?= $i + 1 ?></td>

                    <!-- Nombre + badge -->
                    <td class="px-3 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-zinc-100 flex items-center justify-center
                                        text-xs font-bold text-zinc-500 flex-shrink-0">
                                <?= mb_strtoupper(mb_substr($c['nombre'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-semibold text-zinc-800"><?= htmlspecialchars($c['nombre']) ?></div>
                                <span class="inline-flex items-center gap-1 text-xs font-medium
                                             <?= $badge['text'] ?> mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $badge['dot'] ?>"></span>
                                    <?= $badge['label'] ?>
                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- Teléfono -->
                    <td class="px-3 py-3.5 text-zinc-500 hidden sm:table-cell">
                        <?= $c['telefono'] ? htmlspecialchars($c['telefono']) : '<span class="text-zinc-300 text-xs">—</span>' ?>
                    </td>

                    <!-- Visitas -->
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                     <?= $badge['bg'] ?> <?= $badge['text'] ?> font-bold text-sm">
                            <?= $visitas ?>
                        </span>
                    </td>

                    <!-- Primera visita -->
                    <td class="px-3 py-3.5 text-zinc-500 text-xs hidden md:table-cell">
                        <?= $c['primera_visita']
                            ? fechaEsp($c['primera_visita'])
                            : '<span class="text-zinc-300">—</span>' ?>
                    </td>

                    <!-- Última visita -->
                    <td class="px-3 py-3.5 text-zinc-500 text-xs hidden lg:table-cell">
                        <?= $c['ultima_visita']
                            ? fechaEsp($c['ultima_visita'])
                            : '<span class="text-zinc-300">—</span>' ?>
                    </td>

                    <!-- Servicio favorito -->
                    <td class="px-3 py-3.5 hidden xl:table-cell">
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

                    <!-- Acción -->
                    <td class="px-3 py-3.5 text-right">
                        <a href="<?= url('clientes/historial') ?>?id=<?= (int)$c['id'] ?>"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500
                                  hover:text-amber-600 hover:bg-amber-50 px-2.5 py-1.5 rounded-lg transition-colors border border-stone-200">
                            <i data-lucide="history" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Ver historial</span>
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
