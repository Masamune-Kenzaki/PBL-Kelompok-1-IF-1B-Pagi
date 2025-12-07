<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "siptif_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>