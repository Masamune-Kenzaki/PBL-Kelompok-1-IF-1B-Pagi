<?php
header('Content-Type: application/json');
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: lihat data yang diterima
    error_log("Data POST: " . print_r($_POST, true));
    
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $masuk = mysqli_real_escape_string($conn, $_POST['waktu']); // Perbaikan: ambil dari 'waktu'
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    
    // Debug data
    error_log("Nama: $nama, Email: $email, Tanggal: $tanggal, Masuk: $masuk, Keperluan: $keperluan");
    
    // Set waktu keluar default (null untuk sementara)
    $keluar = null;
    
    // Pastikan nama tabel sesuai
    $sql = "INSERT INTO data_kunjungan (nama, email, tanggal, masuk, keluar, keperluan) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $email, $tanggal, $masuk, $keluar, $keperluan);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
        } else {
            $error = mysqli_error($conn);
            error_log("Error execute: " . $error);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $error]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error = mysqli_error($conn);
        error_log("Error prepare: " . $error);
        echo json_encode(['success' => false, 'message' => 'Gagal mempersiapkan query: ' . $error]);
    }
    
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>