<?php
header('Content-Type: application/json');
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil data dari POST
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $masuk = mysqli_real_escape_string($conn, $_POST['waktu']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    $keluar = NULL; // Default NULL untuk waktu keluar
    
    // Debug log
    error_log("Data diterima - Nama: $nama, Email: $email, Tanggal: $tanggal, Masuk: $masuk, Keperluan: $keperluan");
    
    // Validasi data wajib
    if (empty($nama) || empty($email) || empty($tanggal) || empty($masuk) || empty($keperluan)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }
    
    // Query INSERT
    $sql = "INSERT INTO data_kunjungan (nama, email, tanggal, masuk, keluar, keperluan) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $email, $tanggal, $masuk, $keluar, $keperluan);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
        } else {
            $error = mysqli_error($conn);
            error_log("MySQL Error: " . $error);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $error]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error = mysqli_error($conn);
        error_log("Prepare Error: " . $error);
        echo json_encode(['success' => false, 'message' => 'Error database: ' . $error]);
    }
    
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>