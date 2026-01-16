<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.html';</script>";
    exit;
}

$username = $_SESSION['username'];

// Ambil user_id dan role dari tabel users
$stmtUser = $koneksi->prepare("SELECT id, role FROM users WHERE username = ?");
$stmtUser->bind_param("s", $username);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();
$userId = $userData['id'] ?? 0;
$userRole = $userData['role'] ?? 'user';
$stmtUser->close();

// Cek apakah user adalah admin
$isAdmin = ($userRole === 'admin' || $username === 'admin');

// Query berbeda untuk admin dan user biasa
if ($isAdmin) {
    // Admin melihat semua transaksi dengan informasi username
    $sql = "SELECT t.id, t.user_id, t.produk_id, t.nama_produk, t.harga, t.jumlah, t.total_harga, 
                   t.alamat, t.catatan, t.tanggal_pesan, t.foto, t.status, u.username as customer_name
            FROM transaksi t
            LEFT JOIN users u ON t.user_id = u.id
            ORDER BY t.id DESC";
    
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
} else {
    // User biasa hanya melihat transaksi sendiri
    $sql = "SELECT id, user_id, produk_id, nama_produk, harga, jumlah, total_harga, alamat, catatan, tanggal_pesan, foto, status 
            FROM transaksi 
            WHERE user_id = ? 
            ORDER BY id DESC";
            
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

$result = $stmt->get_result();
$transaksi = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - NaFlorist</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #e91e63;
            margin-bottom: 30px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 13px;
        }

        table th {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            min-width: 100px;
        }

        .status-menunggu {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-proses {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-ditolak {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 50px;
        }

        /* Improved Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 25px;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover:before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #e91e63, #ad1457);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #c2185b, #8e0038);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 30, 99, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #545b62, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        /* Status Action Container */
        .status-action-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 200px;
        }

        .current-status {
            display: flex;
            justify-content: center;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 12px;
            border-radius: 20px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-edit {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-save {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
        }

        .btn-save:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        /* Status Form Styling */
        .status-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 160px;
        }

        .status-select {
            padding: 8px 12px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            background: white;
            transition: all 0.3s ease;
        }

        .status-select:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .footer-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding: 20px 0;
        }

        .foto-preview {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .data-missing {
            color: #999;
            font-style: italic;
            font-size: 11px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .status-action-container {
                min-width: 160px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 6px;
            }
            
            .btn-small {
                padding: 6px 12px;
                font-size: 11px;
            }
            
            .status-form {
                min-width: 120px;
            }
        }

        /* Loading Animation */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading:after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
    </div>
</header>

<div class="container">
    <h2><?= $isAdmin ? 'Semua Riwayat Pemesanan (Admin)' : 'Riwayat Pemesanan Anda' ?></h2>

    <?php if (!empty($transaksi)): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <?php if ($isAdmin): ?>
                        <th>Customer</th>
                        <?php endif; ?>
                        <th>Jenis Buket</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Alamat</th>
                        <th>Catatan</th>
                        <th>Foto</th>
                        <th>Tanggal</th>
                        <th>Status & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $item): ?>
                        <?php $status = $item['status'] ?? 'Menunggu'; ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <?php if ($isAdmin): ?>
                            <td>
                                <strong><?= htmlspecialchars($item['customer_name'] ?? 'Unknown') ?></strong>
                                <br><small style="color:#666;">ID: <?= $item['user_id'] ?></small>
                            </td>
                            <?php endif; ?>
                            <td>
                                <?php 
                                $nama_produk = $item['nama_produk'] ?: '-';
                                echo htmlspecialchars($nama_produk);
                                ?>
                            </td>
                            <td>
                                <?php 
                                $harga = $item['harga'] ?: 0;
                                $total_harga = $item['total_harga'] ?: 0;
                                $jumlah = $item['jumlah'] ?: 1;
                                
                                // Jika harga kosong/0, coba hitung dari total_harga / jumlah
                                if ($harga == 0 && $total_harga > 0 && $jumlah > 0) {
                                    $harga_terhitung = $total_harga / $jumlah;
                                    echo 'Rp ' . number_format($harga_terhitung, 0, ',', '.');
                                } elseif ($harga > 0) {
                                    echo 'Rp ' . number_format($harga, 0, ',', '.');
                                } else {
                                    echo '<span class="data-missing">Data tidak tersedia</span>';
                                }
                                ?>
                            </td>
                            <td><?= $item['jumlah'] ?: 1 ?></td>
                            <td>
                                <?php 
                                $total = $item['total_harga'] ?: 0;
                                echo 'Rp ' . number_format($total, 0, ',', '.');
                                ?>
                            </td>
                            <td><?= htmlspecialchars($item['alamat'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($item['catatan'] ?: '-') ?></td>
                            <td>
                                <?php if (!empty($item['foto']) && file_exists($item['foto'])): ?>
                                    <img src="<?= htmlspecialchars($item['foto']) ?>" alt="Foto" class="foto-preview">
                                <?php else: ?>
                                    <span class="data-missing">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($item['tanggal_pesan'])) {
                                    echo date('d-m-Y', strtotime($item['tanggal_pesan']));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($isAdmin): ?>
                                <div class="status-action-container">
                                    <!-- Current Status Display -->
                                    <div class="current-status">
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $status)) ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                        <button onclick="editStatus(<?= $item['id'] ?>, '<?= htmlspecialchars($status) ?>')" 
                                                class="btn-small btn-edit">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </button>
                                        <button onclick="deleteTransaction(<?= $item['id'] ?>)" 
                                                class="btn-small btn-delete">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $status)) ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>Belum ada riwayat pemesanan.</p>
            <a href="order.php" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i>
                Mulai Pesan Sekarang
            </a>
        </div>
    <?php endif; ?>

    <!-- Footer Buttons -->
    <div class="footer-buttons">
        <a href="index.html" class="btn btn-primary">
            <i class="fas fa-home"></i>
            Kembali ke Beranda
        </a>
        <a href="logout.php" class="btn btn-secondary">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>
</div>

<footer>
    <p>&copy; 2025 NaFlorist. All rights reserved.</p>
</footer>

<script>
function editStatus(transactionId, currentStatus) {
    // Create a custom modal with select options
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    `;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        min-width: 300px;
        max-width: 400px;
    `;
    
    modalContent.innerHTML = `
        <h3 style="margin-top: 0; color: #e91e63; text-align: center;">Edit Status Transaksi</h3>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">ID Transaksi: ${transactionId}</p>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Pilih Status:</label>
            <select id="statusSelect" style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; font-family: 'Poppins', sans-serif;">
                <option value="Menunggu Pembayaran" ${currentStatus === 'Menunggu Pembayaran' ? 'selected' : ''}>Menunggu Pembayaran</option>
                <option value="Menunggu Verifikasi" ${currentStatus === 'Menunggu Verifikasi' ? 'selected' : ''}>Menunggu Verifikasi</option>
                <option value="Diproses" ${currentStatus === 'Diproses' ? 'selected' : ''}>Diproses</option>
                <option value="Dikirim" ${currentStatus === 'Dikirim' ? 'selected' : ''}>Dikirim</option>
                <option value="Selesai" ${currentStatus === 'Selesai' ? 'selected' : ''}>Selesai</option>
                <option value="Ditolak" ${currentStatus === 'Ditolak' ? 'selected' : ''}>Ditolak</option>
                <option value="Batal" ${currentStatus === 'Batal' ? 'selected' : ''}>Batal</option>
            </select>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button id="saveBtn" class="btn-small btn-save" style="padding: 10px 20px;">
                <i class="fas fa-save"></i> Simpan
            </button>
            <button id="cancelBtn" class="btn-small" style="background: #6c757d; color: white; padding: 10px 20px;">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>
    `;
    
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Event listeners for buttons
    const saveBtn = modal.querySelector('#saveBtn');
    const cancelBtn = modal.querySelector('#cancelBtn');
    const statusSelect = modal.querySelector('#statusSelect');
    
    saveBtn.addEventListener('click', function() {
        const newStatus = statusSelect.value;
        if (newStatus !== currentStatus) {
            saveBtn.classList.add('loading');
            saveBtn.disabled = true;
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin_update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    
                    if (xhr.status === 200) {
                        if (xhr.responseText.trim() === 'success') {
                            alert('Status berhasil diubah!');
                            location.reload();
                        } else {
                            alert('Gagal mengubah status: ' + xhr.responseText);
                        }
                    } else {
                        alert('Terjadi kesalahan koneksi');
                    }
                    document.body.removeChild(modal);
                }
            };
            xhr.send('id=' + transactionId + '&status=' + encodeURIComponent(newStatus));
        } else {
            document.body.removeChild(modal);
        }
    });
    
    cancelBtn.addEventListener('click', function() {
        document.body.removeChild(modal);
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

function deleteTransaction(transactionId) {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        // Add loading state
        const button = event.target;
        button.classList.add('loading');
        button.disabled = true;
        
        // Kirim request AJAX untuk delete
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'admin_delete_transaction.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                button.classList.remove('loading');
                button.disabled = false;
                
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        alert('Transaksi berhasil dihapus!');
                        location.reload();
                    } else {
                        alert('Gagal menghapus transaksi: ' + xhr.responseText);
                    }
                } else {
                    alert('Terjadi kesalahan koneksi');
                }
            }
        };
        xhr.send('id=' + transactionId);
    }
}

// Add form submission enhancement
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.status-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('.btn-save');
            button.classList.add('loading');
            button.disabled = true;
        });
    });
});
</script>

</body>
</html>