<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
  echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.html';</script>";
  exit;
}

$username = $_SESSION['username'];

// Ambil user_id dari tabel users
$stmtUser = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
$stmtUser->bind_param("s", $username);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();
$userId = $userData['id'] ?? 0;
$stmtUser->close();

// Ambil transaksi terbaru user yang statusnya "Menunggu Pembayaran"
$sql = "SELECT * FROM transaksi 
        WHERE user_id = ? AND status = 'Menunggu Pembayaran' 
        ORDER BY id DESC LIMIT 1";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran - NaFlorist</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('asset/background.png');
      background-size: cover;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    .container {
      background-color: white;
      max-width: 700px;
      margin: 50px auto;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      text-align: center;
    }

    h2 {
      color: #e86c8c;
      margin-bottom: 30px;
      font-weight: 600;
      font-size: 28px;
    }

    .order-summary {
      background: linear-gradient(135deg, #ffe6f0 0%, #fff0f5 100%);
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 30px;
      text-align: left;
      border: 1px solid #f0c2d8;
    }

    .order-summary h3 {
      color: #e86c8c;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .order-summary p {
      margin: 12px 0;
      font-size: 14px;
      color: #333;
    }

    .total-amount {
      background: #e86c8c;
      color: white;
      padding: 15px;
      border-radius: 10px;
      font-size: 18px;
      font-weight: 600;
      margin: 20px 0;
    }

    .payment-methods {
      margin: 30px 0;
      text-align: left;
    }

    .payment-methods h3 {
      color: #e86c8c;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center;
    }

.payment-option input[type="radio"] {
  position: absolute;
  width: 1px;
  height: 1px;
  opacity: 0;
  pointer-events: none; /* Ini kunci agar tidak menghalangi klik */
}


    .payment-option {
      background: white;
      border: 2px solid #f0f0f0;
      border-radius: 12px;
      padding: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }

    .payment-option:hover {
      border-color: #e86c8c;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(232, 108, 140, 0.1);
    }

    .payment-option.selected {
      border-color: #e86c8c;
      background: #fff8fa;
    }

    .payment-option input[type="radio"] {
      position: absolute;
      opacity: 0;
    }

    .payment-header {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 15px;
    }

    .payment-icon {
      width: 40px;
      height: 40px;
      background: #f8f9fa;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 14px;
    }

    .ovo-icon { background: #4c3494; color: white; }
    .gopay-icon { background: #00AA5B; color: white; }
    .dana-icon { background: #118EEA; color: white; }
    .bank-icon { background: #FF6B6B; color: white; }

    .payment-name {
      font-weight: 600;
      color: #333;
    }

    .payment-details {
      font-size: 13px;
      color: #666;
      margin-top: 10px;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 6px;
      display: none;
    }

    .payment-option.selected .payment-details {
      display: block;
    }

    .account-info {
      background: #e8f5e8;
      padding: 10px;
      border-radius: 6px;
      margin: 5px 0;
      font-family: monospace;
      font-weight: 500;
    }

    .upload-section {
      background: #f8f9fa;
      padding: 25px;
      border-radius: 15px;
      margin: 30px 0;
      border: 2px dashed #ddd;
    }

    .upload-section h4 {
      color: #e86c8c;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .file-input-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .file-input {
      position: absolute;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }

    .file-input-button {
      background: linear-gradient(135deg, #e86c8c 0%, #d14c70 100%);
      color: white;
      padding: 15px 30px;
      border-radius: 10px;
      display: inline-block;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      width: 100%;
      box-sizing: border-box;
    }

    .file-input-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(232, 108, 140, 0.3);
    }

    .file-selected {
      margin-top: 10px;
      color: #28a745;
      font-weight: 500;
    }

    .submit-btn {
      background: linear-gradient(135deg, #e86c8c 0%, #d14c70 100%);
      color: white;
      padding: 15px 40px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      max-width: 300px;
      margin: 20px auto;
      display: block;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(232, 108, 140, 0.3);
    }

    .submit-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .no-order {
      text-align: center;
      padding: 40px;
      color: #666;
    }

    .no-order-icon {
      font-size: 48px;
      margin-bottom: 20px;
    }

    .back-btn {
      background: #6c757d;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 20px;
      text-decoration: none;
      display: inline-block;
      margin: 20px 10px;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      background: #545b62;
      transform: translateY(-2px);
    }

    footer {
      text-align: center;
      padding: 30px;
      margin-top: 50px;
      color: #666;
    }

    @media (max-width: 768px) {
      .container {
        margin: 20px;
        padding: 25px;
      }

      .payment-options {
        grid-template-columns: 1fr;
      }

      h2 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Pembayaran</h2>

  <?php if ($pesanan): ?>
    <!-- Order Summary -->
    <div class="order-summary">
      <h3>Ringkasan Pesanan</h3>
      <p><strong>Jenis Buket:</strong> <?= htmlspecialchars($pesanan['nama_produk']) ?></p>
      <p><strong>Jumlah:</strong> <?= $pesanan['jumlah'] ?> buket</p>
      <p><strong>Harga Satuan:</strong> Rp <?= number_format($pesanan['harga'], 0, ',', '.') ?></p>
      <p><strong>Alamat Pengiriman:</strong> <?= htmlspecialchars($pesanan['alamat']) ?></p>
      <?php if (!empty($pesanan['catatan'])): ?>
      <p><strong>Catatan:</strong> <?= htmlspecialchars($pesanan['catatan']) ?></p>
      <?php endif; ?>
      
      <div class="total-amount">
        <strong>Total Pembayaran: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong>
      </div>
    </div>

    <!-- Payment Methods -->
    <div class="payment-methods">
      <h3>Pilih Metode Pembayaran</h3>
      
      <form action="upload_bukti.php" method="POST" enctype="multipart/form-data" id="paymentForm">
        <input type="hidden" name="id_transaksi" value="<?= $pesanan['id'] ?>">
        
        <div class="payment-options">
          <!-- OVO -->
          <div class="payment-option" onclick="selectPayment('ovo')">
            <input type="radio" name="payment_method" value="ovo" id="ovo">
            <div class="payment-header">
              <div class="payment-icon ovo-icon">OVO</div>
              <div class="payment-name">OVO</div>
            </div>
            <div class="payment-details">
              <p><strong>Cara Pembayaran:</strong></p>
              <p>1. Buka aplikasi OVO</p>
              <p>2. Transfer ke nomor:</p>
              <div class="account-info">085123456789</div>
              <p>3. Masukkan nominal: <strong>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong></p>
              <p>4. Screenshot bukti transfer</p>
            </div>
          </div>

          <!-- GoPay -->
          <div class="payment-option" onclick="selectPayment('gopay')">
            <input type="radio" name="payment_method" value="gopay" id="gopay">
            <div class="payment-header">
              <div class="payment-icon gopay-icon">GP</div>
              <div class="payment-name">GoPay</div>
            </div>
            <div class="payment-details">
              <p><strong>Cara Pembayaran:</strong></p>
              <p>1. Buka aplikasi Gojek</p>
              <p>2. Pilih GoPay > Transfer</p>
              <p>3. Transfer ke nomor:</p>
              <div class="account-info">085123456789</div>
              <p>4. Masukkan nominal: <strong>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong></p>
              <p>5. Screenshot bukti transfer</p>
            </div>
          </div>

          <!-- DANA -->
          <div class="payment-option" onclick="selectPayment('dana')">
            <input type="radio" name="payment_method" value="dana" id="dana">
            <div class="payment-header">
              <div class="payment-icon dana-icon">DANA</div>
              <div class="payment-name">DANA</div>
            </div>
            <div class="payment-details">
              <p><strong>Cara Pembayaran:</strong></p>
              <p>1. Buka aplikasi DANA</p>
              <p>2. Pilih Kirim > ke Nomor HP</p>
              <p>3. Transfer ke nomor:</p>
              <div class="account-info">085123456789</div>
              <p>4. Masukkan nominal: <strong>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong></p>
              <p>5. Screenshot bukti transfer</p>
            </div>
          </div>

          <!-- Transfer Bank -->
          <div class="payment-option" onclick="selectPayment('bank')">
            <input type="radio" name="payment_method" value="bank" id="bank">
            <div class="payment-header">
              <div class="payment-icon bank-icon">BANK</div>
              <div class="payment-name">Transfer Bank</div>
            </div>
            <div class="payment-details">
              <p><strong>Rekening Tujuan:</strong></p>
              <div class="account-info">
                <strong>Bank BCA</strong><br>
                No. Rek: 1234567890<br>
                A/N: NaFlorist
              </div>
              <div class="account-info">
                <strong>Bank Mandiri</strong><br>
                No. Rek: 0987654321<br>
                A/N: NaFlorist
              </div>
              <p><strong>Nominal Transfer:</strong> Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>
              <p>Transfer sesuai nominal exacto untuk mempercepat verifikasi</p>
            </div>
          </div>
        </div>

        <!-- Upload Section -->
        <div class="upload-section">
          <h4>Upload Bukti Pembayaran</h4>
          <p style="color: #666; margin-bottom: 15px;">
            Silakan upload screenshot atau foto bukti pembayaran Anda
          </p>
          
          <div class="file-input-wrapper">
            <input type="file" name="bukti" id="bukti" class="file-input" accept="image/*" required onchange="showFileName(this)">
            <label for="bukti" class="file-input-button">
              Pilih File Bukti Pembayaran
            </label>
          </div>
          <div id="fileName" class="file-selected"></div>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn" disabled>
          Kirim Bukti Pembayaran
        </button>
      </form>
    </div>

  <?php else: ?>
    <div class="no-order">
      <div class="no-order-icon">🛒</div>
      <h3>Tidak Ada Pesanan</h3>
      <p>Tidak ada pesanan yang menunggu pembayaran.</p>
      <a href="order.php" class="back-btn">Buat Pesanan Baru</a>
    </div>
  <?php endif; ?>
  
  <a href="index.html" class="back-btn">Kembali ke Beranda</a>
</div>

<footer>
  &copy; 2025 NaFlorist. All rights reserved.
</footer>

<script>
function selectPayment(method) {
  // Remove selected class from all options
  document.querySelectorAll('.payment-option').forEach(option => {
    option.classList.remove('selected');
  });
  
  // Add selected class to clicked option
  document.querySelector(`#${method}`).closest('.payment-option').classList.add('selected');
  
  // Check the radio button
  document.querySelector(`#${method}`).checked = true;
  
  // Enable submit button
  checkFormValidation();
}

function showFileName(input) {
  const fileName = input.files[0] ? input.files[0].name : '';
  const fileNameDiv = document.getElementById('fileName');
  
  if (fileName) {
    fileNameDiv.innerHTML = `✅ File dipilih: <strong>${fileName}</strong>`;
    fileNameDiv.style.display = 'block';
  } else {
    fileNameDiv.style.display = 'none';
  }
  
  checkFormValidation();
}

function checkFormValidation() {
  const paymentSelected = document.querySelector('input[name="payment_method"]:checked');
  const fileSelected = document.getElementById('bukti').files.length > 0;
  const submitBtn = document.getElementById('submitBtn');
  
  if (paymentSelected && fileSelected) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Kirim Bukti Pembayaran';
  } else {
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Pilih metode pembayaran dan upload bukti';
  }
}

// Form submission handling
document.getElementById('paymentForm').addEventListener('submit', function(e) {
  const submitBtn = document.getElementById('submitBtn');
  submitBtn.disabled = true;
  submitBtn.innerHTML = 'Mengirim...';
});
</script>

</body>
</html>