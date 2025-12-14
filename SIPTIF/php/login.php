<?php
session_start();

// Koneksi database
$koneksi = mysqli_connect("localhost", "root", "", "siptif");

// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Cek username
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

$user = mysqli_fetch_assoc($result);

if ($user) {
    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];

        // Redirect ke admin
        header("Location: admin.html");
        exit;
    } else {
        echo "Password salah!";
    }
} else {
    echo "Username tidak ditemukan!";
}
?>
