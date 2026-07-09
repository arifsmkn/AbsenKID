
<form id="auto-logout-form" method="POST" action="<?php echo e(route('peserta.logout')); ?>" class="hidden">
    <?php echo csrf_field(); ?>
</form>
<script>
(function () {
    var TIMEOUT_MS = 3 * 60 * 1000; // 3 menit
    var STORAGE_KEY = 'absenkid_peserta_last_activity';
    var timer;

    function markActivity() {
        try { localStorage.setItem(STORAGE_KEY, Date.now().toString()); } catch (e) {}
    }

    function checkIdle() {
        var last = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10) || Date.now();
        var elapsed = Date.now() - last;
        if (elapsed >= TIMEOUT_MS) {
            document.getElementById('auto-logout-form').submit();
        } else {
            clearTimeout(timer);
            timer = setTimeout(checkIdle, TIMEOUT_MS - elapsed + 500);
        }
    }

    ['mousemove', 'keydown', 'click', 'touchstart', 'scroll'].forEach(function (evt) {
        document.addEventListener(evt, markActivity, { passive: true });
    });
    markActivity();
    timer = setTimeout(checkIdle, TIMEOUT_MS);
})();
</script>
<?php /**PATH /var/www/AbsenKID/resources/views/peserta/partials/auto-logout.blade.php ENDPATH**/ ?>