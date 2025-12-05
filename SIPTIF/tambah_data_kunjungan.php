<?php
include 'db_config.php';
$nama = $_POST['nama'];
$email = $_POST['email'];
$tanggal = $_POST['tanggal'];
$masuk = $_POST['masuk'];
$keluar = $_POST['keluar'];
$keperluan = $_POST['keperluan'];
$input = mysqli_query($conn, "INSERT INTO data_kunjungan (nama, email, tanggal, masuk, keluar, keperluan) 
VALUES('$nama', '$email', '$tanggal', '$masuk', '$keluar', '$keperluan')") 
or die(mysqli_error($conn));

if ($input) {
    echo "<script>
    alert('Data Berhasil Disimpan');
    window.location.href = 'data_kunjungan.html';
    </script>";
} else {
    echo "<script>
    alert('Gagal Menyimpan Data');
    window.location.href = 'data_kunjungan.html';
    </script>";
}
?>
