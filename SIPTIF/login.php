<?php
// login.php

header('Content-Type: application/json');
// Mengizinkan permintaan dari domain manapun (untuk testing)
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Hanya proses permintaan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Metode permintaan tidak diizinkan.']);
    exit;
}

// Ambil data JSON dari body permintaan (penting untuk AJAX/Fetch)
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// --- BAGIAN LOGIKA OTENTIKASI (Saat ini: Dummy Check) ---
if ($username === 'admin' && $password === 'admin123') {
    // Login Berhasil
    http_response_code(200);
    echo json_encode([
        'message' => 'Login Berhasil!',
        'status' => 'success',
        // Token harusnya dihasilkan menggunakan JWT library PHP (misal: firebase/php-jwt)
        'token' => 'dummy_php_jwt_token_45678'
    ]);
} else {
    // Login Gagal
    http_response_code(401); // Unauthorized
    echo json_encode([
        'message' => 'Username atau Password salah.',
        'status' => 'error'
    ]);
}

// Selesai
exit;

?>