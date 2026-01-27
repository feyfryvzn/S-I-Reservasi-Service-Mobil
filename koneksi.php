<?php
$host = '127.0.0.1'; // Ganti jika menggunakan 'localhost' atau IP lain
$username = 'root';   // Ganti dengan username MySQL Anda
$password = '';       // Ganti dengan password MySQL Anda (kosong jika default)
$database = 'dbdppl';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>