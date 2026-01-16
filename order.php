<?php
session_start();
if (!isset($_SESSION['username'])) {
  echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.html';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form Pemesanan - NaFlorist</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('asset/background.png');
      background-size: cover;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    header, footer {
      background-color: white;
      padding: 10px 30px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }

    nav a {
      text-decoration: none;
      font-weight: bold;
      color: #e91e63;
    }

    .order-container {
      max-width: 900px;
      margin: 80px auto;
      padding: 40px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 6px;
    }

    select, input, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    input[readonly] {
      background-color: #f8f8f8;
      font-weight: bold;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #e91e63;
      color: white;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #c2185b;
    }
  </style>
</head>
<body>

<header>
  <nav>
    <ul>
      <li><a href="index.html">HOME</a></li>
      <li><a href="about.html">ABOUT</a></li>
      <li><a href="bouquet.html">BOUQUETS</a></li>
      <li><a href="order.php">ORDER</a></li>
      <li><a href="pembayaran.php">PAYMENT</a></li>
      <li><a href="contact.html">CONTACT</a></li>
      <li><a href="riwayat.php">RIWAYAT</a></li>
    </ul>
  </nav>
  <div style="text-align:right;">
    <div class="logo-logout" style="display: flex; align-items: center; gap: 15px;">
      <img src="asset/logo baru.png" alt="NaFlorist Logo" style="height: 50px;">
      <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
      <a href="logout.php"><img src="asset/logout.png" alt="Logout" style="height:35px;"></a>
    </div>
</header>

<div class="order-container">
  <h2>Form Pemesanan Buket</h2>
  <form action="proses_order.php" method="POST" enctype="multipart/form-data">

    <!-- Jenis Buket -->
    <div class="form-group">
      <label for="jenis_buket">Jenis Buket</label>
      <select name="jenis_buket" id="jenis_buket" required onchange="updateTotal()">
        <option value="">-- Pilih Jenis Buket --</option>
        <option value="Birthday Bouquet">Birthday Bouquet</option>
        <option value="Anniversary Bouquet">Anniversary Bouquet</option>
        <option value="Wedding Bouquet">Wedding Bouquet</option>
        <option value="Graduation Bouquet">Graduation Bouquet</option>
        <option value="Custom Bouquet">Custom Bouquet</option>
        <option value="Valentine Bouquet">Valentine Bouquet</option>
        <option value="Rose Premium">Rose Premium</option>
        <option value="Wedding Premium">Wedding Premium</option>
        <option value="Luxury Arrangement">Luxury Arrangement</option>
        <option value="Special Event Mix">Special Event Mix</option>
      </select>
    </div>

    <!-- Harga -->
    <div class="form-group">
      <label for="harga">Harga Buket</label>
      <select name="harga" id="harga" onchange="updateTotal()" required>
        <option value="">-- Pilih Harga --</option>
        <option value="18000">Rp. 18.000</option>
        <option value="25000">Rp. 25.000</option>
        <option value="40000">Rp. 40.000</option>
        <option value="50000">Rp. 50.000</option>
        <option value="65000">Rp. 65.000</option>
        <option value="85000">Rp. 85.000</option>
        <option value="110000">Rp. 110.000</option>
        <option value="150000">Rp. 150.000</option>
        <option value="185000">Rp. 185.000</option>
        <option value="280000">Rp. 280.000</option>
      </select>
    </div>

    <!-- Jumlah -->
    <div class="form-group">
      <label for="jumlah">Jumlah</label>
      <input type="number" name="jumlah" id="jumlah" min="1" value="1" onchange="updateTotal()" required>
    </div>

    <!-- Total Harga -->
    <div class="form-group">
      <label for="total_harga">Total Harga</label>
      <input type="text" id="total_harga" readonly>
    </div>

    <!-- Alamat -->
    <div class="form-group">
      <label>Alamat Pengiriman</label>
      <textarea name="alamat" rows="3" required></textarea>
    </div>

    <!-- Catatan -->
    <div class="form-group">
      <label>Catatan (Opsional)</label>
      <textarea name="catatan" rows="2"></textarea>
    </div>

    <!-- Upload -->
    <div class="form-group">
      <label>Upload Gambar Referensi (Opsional)</label>
      <input type="file" name="foto" accept="image/*">
    </div>

    <!-- Hidden untuk total harga numerik -->
    <input type="hidden" name="total_harga" id="total_harga_raw">

    <button type="submit">Pesan Sekarang</button>

  </form>
</div>

<footer>
  <p>&copy; 2025 NaFlorist. All rights reserved.</p>
</footer>

<script>
  function updateTotal() {
    const harga = parseInt(document.getElementById('harga').value) || 0;
    const jumlah = parseInt(document.getElementById('jumlah').value) || 1;
    const total = harga * jumlah;

    document.getElementById('total_harga').value = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_harga_raw').value = total;
  }
</script>

</body>
</html>
