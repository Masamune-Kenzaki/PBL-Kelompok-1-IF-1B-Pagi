<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

$result = mysqli_query($conn, "DELETE FROM data_kunjungan WHERE id='$id'");

if ($result) {
    echo "<script>
    alert('Data berhasil dihapus');
    window.location.href = 'data_kunjungan.html';
    </script>";
} else {
    echo "<script>
    alert('Gagal menghapus data');
    window.location.href = 'data_kunjungan.html';
    </script>";
}
}
?>
