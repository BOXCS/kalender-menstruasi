<?php
session_start();
session_destroy();
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Logout Berhasil!',
                text: 'Anda telah keluar.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'login.php';
            });
        });
      </script>";
exit;
?>
