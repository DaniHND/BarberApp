<!-- Footer public -->
<footer class="mt-8 pb-6 text-center">
    <p class="text-xs text-zinc-400">
        <?= htmlspecialchars($cfg['nombre_barberia'] ?? APP_NAME) ?>
        &nbsp;·&nbsp; BarberApp <?= APP_VERSION ?>
    </p>
</footer>

<?php if (!empty($flash)): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
        icon:             '<?= htmlspecialchars($flash['type']) ?>',
        title:            '<?= addslashes(htmlspecialchars($flash['title'])) ?>',
        <?php if (!empty($flash['message'])): ?>
        text:             '<?= addslashes(htmlspecialchars($flash['message'])) ?>',
        <?php endif; ?>
        timer:            3500,
        timerProgressBar: true,
        showConfirmButton: false,
        toast:            true,
        position:         'top-end',
    });
});
</script>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>lucide.createIcons();</script>

</body>
</html>
