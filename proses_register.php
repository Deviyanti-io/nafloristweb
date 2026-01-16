<?php
include 'koneksi.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username && $password) {
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah terdaftar!'); window.location='register.php';</script>";
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')");
        if ($simpan) {
            echo "<script>alert('Berhasil mendaftar! Silakan login.'); window.location='login.html';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data!'); window.location='register.php';</script>";
        }
    }
} else {
    echo "<script>alert('Lengkapi semua data!'); window.location='register.php';</script>";
}
?>
