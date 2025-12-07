<?php
// save_kunjungan.php
header('Content-Type: application/json');

// Response minimal
$response = ['success' => false, 'message' => ''];

try {
    // Koneksi database langsung (lebih cepat)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "siptif_db";
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new Exception("Koneksi database gagal");
    }
    
    // Ambil data POST
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $masuk = $_POST['waktu'] ?? date('H:i:s');
    $keperluan = $_POST['keperluan'] ?? '';
    
    // Validasi cepat
    if (strlen($nama) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Nama atau email tidak valid';
        echo json_encode($response);
        exit;
    }
    
    // Escape input untuk keamanan
    $nama = mysqli_real_escape_string($conn, trim($nama));
    $email = mysqli_real_escape_string($conn, trim($email));
    $tanggal = mysqli_real_escape_string($conn, $tanggal);
    $masuk = mysqli_real_escape_string($conn, $masuk);
    $keperluan = mysqli_real_escape_string($conn, trim($keperluan));
    
    // Query INSERT langsung
    $sql = "INSERT INTO data_kunjungan 
            (nama, email, tanggal, masuk, keperluan, created_at) 
            VALUES 
            ('$nama', '$email', '$tanggal', '$masuk', '$keperluan', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $response['success'] = true;
        $response['message'] = 'Data berhasil disimpan';
    } else {
        $response['message'] = 'Gagal menyimpan ke database';
    }
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    $response['message'] = 'Terjadi kesalahan sistem';
}

echo json_encode($response);
exit;
?>