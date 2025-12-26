<?php
// ubah_data_kunjungan.php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $instansi = mysqli_real_escape_string($conn, $_POST['instansi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $masuk = mysqli_real_escape_string($conn, $_POST['masuk']);
    $keluar = $_POST['keluar'] ? mysqli_real_escape_string($conn, $_POST['keluar']) : NULL;
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    
    // Jika keperluan adalah "Lainnya", ambil dari input lainnya
    if ($keperluan == 'Lainnya' && isset($_POST['keperluan_lainnya'])) {
        $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan_lainnya']);
    }
    
    $result = mysqli_query($conn, "UPDATE data_kunjungan SET 
        nama='$nama', 
        email='$email',
        instansi='$instansi', 
        tanggal='$tanggal', 
        masuk='$masuk',
        keluar=" . ($keluar ? "'$keluar'" : "NULL") . ", 
        keperluan='$keperluan' 
        WHERE id='$id'") or die(mysqli_error($conn));

    if ($result) {
        // PERUBAHAN DI SINI: Redirect ke .php bukan .html
        header('Location: data_kunjungan.php?success=1');
        exit();
    } else {
        header('Location: data_kunjungan.php?error=1');
        exit();
    }
}
?>