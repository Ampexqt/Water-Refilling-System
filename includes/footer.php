<?php
// Include the modal HTML
include __DIR__ . '/logout-modal.php';
// Include delete order modal if on forms page
if (basename($_SERVER['PHP_SELF']) === 'forms.php') {
    include __DIR__ . '/delete-order-modal.php';
}
?>
<script src="<?php echo BASE_URL; ?>/assets/js/modal.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/modal-fix.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/forms-toast.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/order-actions.js?v=<?php echo time(); ?>"></script>
</body>

</html>