    </main>

    <!-- Footer -->
    <footer class="px-4 md:px-6 py-3 text-center text-xs text-zinc-400 border-t border-stone-200 bg-white">
        BarberApp <?= APP_VERSION ?> &nbsp;·&nbsp; <?= date('Y') ?>
    </footer>

</div><!-- /main wrapper -->

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
