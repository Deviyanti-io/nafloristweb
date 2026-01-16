<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.html';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_transaksi = $_POST['id_transaksi'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    if (empty($id_transaksi) || empty($payment_method)) {
        echo "<script>alert('Data tidak lengkap!'); window.history.back();</script>";
        exit;
    }

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['bukti']['type'];
        $file_size = $_FILES['bukti']['size'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Tipe file tidak diizinkan!'); window.history.back();</script>";
            exit;
        }

        if ($file_size > $max_size) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 5MB.'); window.history.back();</script>";
            exit;
        }

        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $new_filename = 'bukti_' . $id_transaksi . '_' . time() . '.' . $ext;
        $upload_dir = 'uploads/bukti_pembayaran/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_path)) {
            // Update sesuai struktur tabel
            $sql = "UPDATE transaksi SET 
                        status = 'Menunggu Verifikasi',
                        payment_method = ?,
                        bukti_pembayaran = ?,
                        tanggal_bayar = NOW()
                    WHERE id = ?";

            $stmt = $koneksi->prepare($sql);
            if (!$stmt) {
                echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.history.back();</script>";
                exit;
            }

            $stmt->bind_param("ssi", $payment_method, $upload_path, $id_transaksi);

            if ($stmt->execute()) {
                $stmt->close();
                echo "<script>alert('Bukti pembayaran berhasil dikirim!'); window.location='riwayat.php';</script>";
                exit;
            } else {
                unlink($upload_path);
                echo "<script>alert('Gagal menyimpan ke database!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Gagal mengupload file!'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('File bukti belum diupload atau terjadi error!'); window.history.back();</script>";
        exit;
    }
} else {
    echo "Halaman ini hanya dapat diakses melalui form.";
    echo "<br><a href='pembayaran.php'>Kembali ke pembayaran</a>";
    exit;
}
