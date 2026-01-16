<?php
session_start();
include 'koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  echo "<script>alert('Akses ditolak. Hanya admin yang bisa mengakses.'); window.location='login.html';</script>";
  exit;
}

$pesanan = $koneksi->query("SELECT * FROM pesanan ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin - Kelola Pesanan</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Kelola Pesanan Pelanggan</h2>

  <table border="1" cellpadding="8" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Buket</th>
      <th>Jumlah</th>
      <th>Total</th>
      <th>Status</th>
      <th>Bukti</th>
      <th>Aksi</th>
    </tr>
    <?php while ($row = $pesanan->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['username'] ?></td>
      <td><?= $row['nama_produk'] ?></td>
      <td><?= $row['jumlah'] ?></td>
      <td>Rp<?= number_format($row['total']) ?></td>
      <td><?= $row['status'] ?></td>
      <td>
        <?php if ($row['bukti_pembayaran']): ?>
          <a href="bukti/<?= $row['bukti_pembayaran'] ?>" target="_blank">Lihat Bukti</a>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td>
        <form action="update_status.php" method="POST">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <select name="status" required>
            <option disabled selected>Pilih</option>
            <option value="Pesanan Diproses">Pesanan Diproses</option>
            <option value="Dikirim">Dikirim</option>
            <option value="Selesai">Selesai</option>
          </select>
          <button type="submit">Ubah</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
