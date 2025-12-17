<?php
// hapus_data_kunjungan.php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $result = mysqli_query($conn, "DELETE FROM data_kunjungan WHERE id='$id'");

    if ($result) {
        // Redirect ke data_kunjungan.php dengan parameter delete_success
        header('Location: data_kunjungan.php?delete_success=1');
        exit();
    } else {
        header('Location: data_kunjungan.php?delete_error=1');
        exit();
    }
} else {
    // Jika tidak ada ID, redirect ke data kunjungan
    header('Location: data_kunjungan.php');
    exit();
}
?>