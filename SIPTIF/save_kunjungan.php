<?php
header('Content-Type: application/json');
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $masuk = mysqli_real_escape_string($conn, $_POST['masuk']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Set waktu keluar default (null untuk sementara)
    $keluar = null;
    
    $sql = "INSERT INTO kunjungan (nim, nama, status, tanggal, masuk, keluar, keperluan, email) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $nim, $nama, $status, $tanggal, $masuk, $keluar, $keperluan, $email);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>