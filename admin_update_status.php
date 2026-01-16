<?php
session_start();
include 'koneksi.php';

// Set content type untuk response
header('Content-Type: text/plain');

// Validasi user login dan admin
if (!isset($_SESSION['username'])) {
    echo 'error: Tidak ada akses';
    exit;
}

$username = $_SESSION['username'];

// Cek apakah user adalah admin
$stmtUser = $koneksi->prepare("SELECT id, role FROM users WHERE username = ?");
$stmtUser->bind_param("s", $username);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();
$userRole = $userData['role'] ?? 'user';
$stmtUser->close();

$isAdmin = ($userRole === 'admin' || $username === 'admin');

if (!$isAdmin) {
    echo 'error: Akses ditolak - hanya admin yang dapat mengubah status';
    exit;
}

// Validasi input POST
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo 'error: Data tidak lengkap';
    exit;
}

$transactionId = intval($_POST['id']);
$newStatus = trim($_POST['status']);

// Validasi ID transaksi
if ($transactionId <= 0) {
    echo 'error: ID transaksi tidak valid';
    exit;
}

// Validasi status tidak kosong
if (empty($newStatus)) {
    echo 'error: Status tidak boleh kosong';
    exit;
}

// List status yang diizinkan (opsional - untuk validasi lebih ketat)
$allowedStatuses = [
    'Menunggu Konfirmasi',
    'Dikonfirmasi',
    'Dalam Proses',
    'Siap Dikirim',
    'Dikirim',
    'Selesai',
    'Ditolak',
    'Dibatalkan'
];

// Cek apakah transaksi existe
$checkStmt = $koneksi->prepare("SELECT id FROM transaksi WHERE id = ?");
$checkStmt->bind_param("i", $transactionId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    echo 'error: Transaksi tidak ditemukan';
    exit;
}
$checkStmt->close();

// Update status
$updateStmt = $koneksi->prepare("UPDATE transaksi SET status = ? WHERE id = ?");
$updateStmt->bind_param("si", $newStatus, $transactionId);

if ($updateStmt->execute()) {
    if ($updateStmt->affected_rows > 0) {
        echo 'success';
    } else {
        echo 'error: Tidak ada perubahan data';
    }
} else {
    echo 'error: Gagal mengupdate status - ' . $koneksi->error;
}

$updateStmt->close();
$koneksi->close();
?>