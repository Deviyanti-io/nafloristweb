<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.html';</script>";
    exit;
}

$username = $_SESSION['username'];
$metode = $_POST['metode'] ?? '';
$detail = $_POST['detail'] ?? '';
$edit_id = $_POST['edit_id'] ?? '';

// Upload bukti pembayaran
$bukti = '';
if (!empty($_FILES['bukti']['name'])) {
    $nama_file = basename($_FILES['bukti']['name']);
    $target_dir = "uploads/";
    $target_file = $target_dir . time() . '_' . $nama_file;
    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
        $bukti = basename($target_file);
    } else {
        echo "<script>alert('Gagal mengupload bukti pembayaran.'); window.location='pembayaran.php';</script>";
        exit;
    }
}

// Jika update
if (!empty($edit_id)) {
    // Jika tidak upload bukti baru, pakai bukti lama
    if (empty($bukti)) {
        $result = $koneksi->query("SELECT bukti FROM pembayaran WHERE id_pembayaran = $edit_id");
        $row = $result->fetch_assoc();
        $bukti = $row['bukti'];
    }

    $stmt = $koneksi->prepare("UPDATE pembayaran SET metode=?, detail=?, bukti=? WHERE id_pembayaran=? AND username=?");
    $stmt->bind_param("sssis", $metode, $detail, $bukti, $edit_id, $username);
    if ($stmt->execute()) {
        echo "<script>alert('Data pembayaran diperbarui'); window.location='pembayaran.php';</script>";
    } else {
        echo "Gagal update: " . $stmt->error;
    }
    $stmt->close();

} else {
    // Insert baru
    $stmt = $koneksi->prepare("INSERT INTO pembayaran (username, metode, detail, bukti) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $metode, $detail, $bukti);
    if ($stmt->execute()) {
        echo "<script>alert('Pembayaran berhasil dikonfirmasi'); window.location='pembayaran.php';</script>";
    } else {
        echo "Gagal simpan: " . $stmt->error;
    }
    $stmt->close();
}
