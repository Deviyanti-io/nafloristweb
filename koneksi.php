<?php
// koneksi.php

$host = "localhost"; // Sesuaikan jika host database berbeda
$username = "root";  // Username database kamu
$password = "";      // Password database kamu (biasanya kosong untuk XAMPP/WAMPServer)
$database = "buket_bunga"; // Nama database yang sudah kamu buat

// Buat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set charset ke utf8mb4 jika diperlukan
$koneksi->set_charset("utf8mb4");

//echo "Koneksi database berhasil!"; // Bisa dihapus setelah dipastikan koneksi berhasil
