<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
  echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.html';</script>";
  exit;
}

$username = $_SESSION['username'];
$jenis_buket = $_POST['jenis_buket'] ?? '';
$harga = isset($_POST['harga']) ? (int)$_POST['harga'] : 0;
$jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
$alamat = $_POST['alamat'] ?? '-';
$catatan = $_POST['catatan'] ?? '-';
$tanggal = date('Y-m-d H:i:s');
$status = 'Menunggu Pembayaran';
$foto = '';

// Validasi buket dan harga
if (empty($jenis_buket) || $harga <= 0) {
  echo "<script>alert('Jenis buket atau harga belum dipilih.'); window.location='order.php';</script>";
  exit;
}

// Upload gambar (jika ada)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
  $folder = 'uploads/';
  if (!is_dir($folder)) mkdir($folder);
  $nama_file = time() . '_' . basename($_FILES['foto']['name']);
  $foto = $folder . $nama_file;
  move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
}

// Ambil user_id
$user_stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_id = $user['id'] ?? 0;
$user_stmt->close();

$total_harga = $harga * $jumlah;

// Simpan ke tabel transaksi (dengan harga disimpan juga)
$stmt = $koneksi->prepare("INSERT INTO transaksi 
  (user_id, produk_id, nama_produk, jumlah, harga, total_harga, alamat, catatan, tanggal_pesan, foto, status)
  VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt) {
  $stmt->bind_param("isiiisssss", $user_id, $jenis_buket, $jumlah, $harga, $total_harga, $alamat, $catatan, $tanggal, $foto, $status);
  if ($stmt->execute()) {
    echo "<script>alert('Pemesanan berhasil!'); window.location='pembayaran.php';</script>";
    exit;
  } else {
    die("Gagal menyimpan transaksi: " . $stmt->error);
  }
} else {
  die("Gagal menyiapkan query transaksi: " . $koneksi->error);
}
?>
