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
    echo 'error: Akses ditolak - hanya admin yang dapat menghapus transaksi';
    exit;
}

// Validasi input POST
if (!isset($_POST['id'])) {
    echo 'error: ID transaksi tidak ditemukan';
    exit;
}

$transactionId = intval($_POST['id']);

// Validasi ID transaksi
if ($transactionId <= 0) {
    echo 'error: ID transaksi tidak valid';
    exit;
}

// Cek apakah transaksi exists dan ambil info foto untuk dihapus
$checkStmt = $koneksi->prepare("SELECT id, foto FROM transaksi WHERE id = ?");
$checkStmt->bind_param("i", $transactionId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    echo 'error: Transaksi tidak ditemukan';
    exit;
}

$transactionData = $checkResult->fetch_assoc();
$photoPath = $transactionData['foto'];
$checkStmt->close();

// Mulai transaction untuk memastikan konsistensi data
$koneksi->autocommit(FALSE);

try {
    // Hapus record dari database
    $deleteStmt = $koneksi->prepare("DELETE FROM transaksi WHERE id = ?");
    $deleteStmt->bind_param("i", $transactionId);
    
    if (!$deleteStmt->execute()) {
        throw new Exception("Gagal menghapus dari database: " . $koneksi->error);
    }
    
    if ($deleteStmt->affected_rows === 0) {
        throw new Exception("Tidak ada data yang dihapus");
    }
    
    $deleteStmt->close();
    
    // Hapus file foto jika ada
    if (!empty($photoPath) && file_exists($photoPath)) {
        if (!unlink($photoPath)) {
            // Log error tapi jangan gagalkan proses (foto mungkin sudah tidak ada)
            error_log("Gagal menghapus file foto: " . $photoPath);
        }
    }
    
    // Commit transaksi
    $koneksi->commit();
    echo 'success';
    
} catch (Exception $e) {
    // Rollback jika ada error
    $koneksi->rollback();
    echo 'error: ' . $e->getMessage();
}

// Restore autocommit
$koneksi->autocommit(TRUE);
$koneksi->close();
?>