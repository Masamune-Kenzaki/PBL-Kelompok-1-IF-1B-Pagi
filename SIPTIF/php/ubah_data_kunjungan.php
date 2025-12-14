<?php
include 'db_config.php';
$id = $_POST['id'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$tanggal = $_POST['tanggal'];
$masuk = $_POST['masuk'];
$keluar = $_POST['keluar'];
$keperluan = $_POST['keperluan'];
$result = mysqli_query($conn, "UPDATE data_kunjungan SET nama='$nama', email='$email', tanggal='$tanggal', masuk='$masuk'
, keluar='$keluar', keperluan='$keperluan' WHERE id='$id'") or die(mysqli_error($conn));

if ($result) {
    echo "<script>
    alert('Data berhasil diubah');
    window.location.href = 'data_kunjungan.html';
    </script>";
} else {
    echo "<script>
    alert('Gagal mengubah data');
    window.location.href = 'data_kunjungan.html';
    </script>";
}
?>
