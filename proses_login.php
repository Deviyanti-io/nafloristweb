<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Cek username dan password di tabel users
$query = $koneksi->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");

if ($query->num_rows > 0) {
  $user = $query->fetch_assoc();
  $_SESSION['username'] = $user['username'];
  $_SESSION['role'] = $user['role'];

  // Arahkan berdasarkan role
  if ($user['role'] === 'admin') {
    echo "<script>alert('Selamat datang, Admin!'); window.location='riwayat.php';</script>";
  } else {
    echo "<script>alert('Selamat datang, $username!'); window.location='order.php';</script>";
  }
} else {
  echo "<script>alert('Login gagal. Username atau password salah.'); window.location='login.html';</script>";
}
?>
