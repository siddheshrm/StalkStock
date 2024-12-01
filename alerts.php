<?php
// Check if an alert is set and is an array
if (isset($_SESSION['alert']) && is_array($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                text: '" . htmlspecialchars($alert['text']) . "',
                icon: '" . htmlspecialchars($alert['type']) . "',
                confirmButtonText: 'OK'
            });
        });
    </script>";

    unset($_SESSION['alert']);
}
?>