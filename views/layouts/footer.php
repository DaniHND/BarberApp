    </main>

    <!-- Footer desktop -->
    <footer class="hidden md:block px-4 md:px-6 py-3 text-center text-xs text-zinc-400 border-t border-stone-200 bg-white">
        BarberApp <?= APP_VERSION ?> &nbsp;·&nbsp; <?= date('Y') ?>
    </footer>

</div><!-- /main wrapper -->

<!-- ── Bottom Navigation (móvil) ──────────────────────────
     Aparece solo en < md. Los 5 accesos principales del admin.
     Se coloca ANTES de lucide.createIcons() para que los íconos
     se inicialicen en el mismo pase.
─────────────────────────────────────────────────────────── -->
<?php if (isset($activeNav)): ?>
<nav class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-stone-200 flex md:hidden shadow-[0_-2px_12px_rgba(0,0,0,.06)]"
     style="padding-bottom: env(safe-area-inset-bottom, 0px);">
    <?php
    $bnItems = [
        ['url' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Inicio',   'key' => 'dashboard'],
        ['url' => 'citas',     'icon' => 'calendar-days',    'label' => 'Citas',    'key' => 'citas'],
        ['url' => 'espera',    'icon' => 'users',            'label' => 'Espera',   'key' => 'espera'],
        ['url' => 'clientes',  'icon' => 'heart-handshake',  'label' => 'Clientes', 'key' => 'clientes'],
        ['url' => 'reportes',  'icon' => 'bar-chart-3',      'label' => 'Reportes', 'key' => 'reportes'],
    ];
    foreach ($bnItems as $item):
        $bnActive = ($activeNav ?? '') === $item['key'];
    ?>
    <a href="<?= url($item['url']) ?>"
       class="relative flex-1 flex flex-col items-center justify-center py-2.5 gap-0.5 transition-colors
              <?= $bnActive ? 'text-blue-500' : 'text-zinc-400' ?>">
        <?php if ($bnActive): ?>
        <span class="absolute top-0 inset-x-1/4 h-0.5 bg-blue-500 rounded-b-full"></span>
        <?php endif; ?>
        <i data-lucide="<?= $item['icon'] ?>" class="w-[22px] h-[22px]"></i>
        <span class="text-[10px] leading-none <?= $bnActive ? 'font-bold' : 'font-normal' ?>">
            <?= $item['label'] ?>
        </span>
    </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/locales/es.global.min.js"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>lucide.createIcons();</script>

<!-- JS principal -->
<script src="<?= url('assets/js/main.js') ?>"></script>

<?php if (!empty($flash)): ?>
<script>
Swal.fire({
    icon: '<?= htmlspecialchars($flash['type']) ?>',
    title: '<?= addslashes(htmlspecialchars($flash['title'])) ?>',
    <?php if (!empty($flash['message'])): ?>
    text: '<?= addslashes(htmlspecialchars($flash['message'])) ?>',
    <?php endif; ?>
    timer: 3500,
    timerProgressBar: true,
    showConfirmButton: false,
    toast: true,
    position: 'top-end',
});
</script>
<?php endif; ?>

</body>
</html>
