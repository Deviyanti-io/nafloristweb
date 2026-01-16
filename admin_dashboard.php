<?php
session_start();
include 'koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak. Hanya admin yang dapat mengakses halaman ini.'); window.location='login.php';</script>";
    exit;
}

$riwayat = $koneksi->query("SELECT * FROM pembayaran");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NaFlorist</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #fdf4f6;
        }
        h1 {
            color: #d94880;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #fce4ec;
            color: #6a1b9a;
        }
        a.logout {
            background: #d32f2f;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            float: right;
        }
        a.logout:hover {
            background: #b71c1c;
        }
    </style>
</head>
<body>

<h1>Admin Dashboard</h1>
<a href="logout.php" class="logout">Logout</a>

<h2>Riwayat Semua Pembayaran</h2>
<table>
    <tr>
        <th>Username</th>
        <th>Tanggal</th>
        <th>Metode</th>
        <th>Detail</th>
        <th>Bukti</th>
    </tr>
    <?php while ($row = $riwayat->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['metode']) ?></td>
            <td><?= htmlspecialchars($row['detail']) ?></td>
            <td><a href="uploads/<?= htmlspecialchars($row['bukti']) ?>" target="_blank">Lihat</a></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
