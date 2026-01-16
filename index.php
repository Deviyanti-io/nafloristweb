<?php
// index.php - SEMUA DALAM SATU FILE (HTML, CSS, PHP)

// Include file koneksi database
include 'koneksi.php';

// --- LOGIKA CRUD (Tabel Produk) ---

// Logika Tambah Produk
if (isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama_produk = $_POST['nama_produk'];
    $jenis_bunga = $_POST['jenis_bunga'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar_produk_url = $_POST['gambar_produk_url'];

    $sql_insert = "INSERT INTO produk (nama_produk, jenis_bunga, harga, stok, gambar_produk_url) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    $stmt_insert->bind_param("ssdis", $nama_produk, $jenis_bunga, $harga, $stok, $gambar_produk_url);

    if ($stmt_insert->execute()) {
        header("Location: index.php?status=sukses#crud-produk");
    } else {
        header("Location: index.php?status=gagal&msg=" . urlencode($stmt_insert->error) . "#crud-produk");
    }
    $stmt_insert->close();
    exit;
}

// Logika Update Produk
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_produk   = $_POST['id_produk  '];
    $nama_produk = $_POST['nama_produk'];
    $jenis_bunga = $_POST['jenis_bunga'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar_produk_url = $_POST['gambar_produk_url'];

    $sql_update = "UPDATE produk SET nama_produk=?, jenis_bunga=?, harga=?, stok=?, gambar_produk_url=? WHERE id_produk =?";
    $stmt_update = $koneksi->prepare($sql_update);
    $stmt_update->bind_param("ssdisi", $nama_produk, $jenis_bunga, $harga, $stok, $gambar_produk_url, $id_produk);

    if ($stmt_update->execute()) {
        header("Location: index.php?status=sukses#crud-produk");
    } else {
        header("Location: index.php?status=gagal&msg=" . urlencode($stmt_update->error) . "#crud-produk");
    }
    $stmt_update->close();
    exit;
}

// Logika Hapus Produk
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_to_delete = $_GET['id'];
    $sql_delete = "DELETE FROM produk WHERE id_produk    = ?";
    $stmt_delete = $koneksi->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_to_delete);

    if ($stmt_delete->execute()) {
        header("Location: index.php?status=sukses#crud-produk");
    } else {
        header("Location: index.php?status=gagal&msg=" . urlencode($stmt_delete->error) . "#crud-produk");
    }
    $stmt_delete->close();
    exit;
}

// Ambil data produk untuk ditampilkan (digunakan di bagian Buket dan CRUD)
$sql_produk_display = "SELECT id_produk , nama_produk, harga FROM produk";
$result_produk_display = $koneksi->query($sql_produk_display);

// Ambil data produk untuk form edit (jika ada ID di URL)
$data_produk_edit = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql_edit = "SELECT * FROM produk WHERE id_produk    = ?";
    $stmt_edit = $koneksi->prepare($sql_edit);
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    if ($result_edit->num_rows > 0) {
        $data_produk_edit = $result_edit->fetch_assoc();
    }
    $stmt_edit->close();
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NaFlorist</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Global Reset & Font Setup */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            /* Menggunakan Poppins sebagai font utama */
            background: url(asset/backgroundcover.jpg) no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            justify-content: space-between;
            /* Menyesuaikan untuk logo di kanan, nav di kiri */
            align-items: center;
            padding: 20px 50px;
            background: rgba(255, 255, 255, 0.8);
            /* Background sedikit transparan */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            /* Sedikit shadow */
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav a {
            text-decoration: none;
            color: #555;
            /* Warna teks navbar */
            font-weight: 500;
            /* Sedikit lebih tipis dari bold */
            transition: color 0.3s ease, border-bottom 0.3s ease;
        }

        nav a:hover {
            color: #e86c8c;
            /* Hover color */
        }

        /* Highlight menu aktif */
        nav a.active {
            color: #e86c8c;
            /* warna pink khas */
            border-bottom: 3px solid #e86c8c;
            padding-bottom: 5px;
        }

        .logo {
            margin-left: 20px;
            /* Jarak dari sisi kanan, jika nav di kiri */
        }

        .logo img {
            height: 50px;
        }

        /* --- SECTION MAIN (HOME) --- */
        .main {
            position: relative;
            /* Untuk background pattern */
            display: flex;
            justify-content: center;
            /* Menggunakan justify-content center */
            align-items: center;
            padding: 80px 50px;
            max-width: 1200px;
            margin: 40px auto;
            /* Margin atas bawah */
            background: rgba(255, 255, 255, 0.9);
            /* Background untuk section utama */
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            /* Untuk pattern */
        }

        .main::before {
            /* Background pattern */
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg width="600" height="600" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M300 0C134.314 0 0 134.314 0 300C0 465.686 134.314 600 300 600C465.686 600 600 465.686 600 300C600 134.314 465.686 0 300 0ZM300 550C161.734 550 50 438.266 50 300C50 161.734 161.734 50 300 50C438.266 50 550 161.734 550 300C550 438.266 438.266 550 300 550Z" fill="%23ffd5d5" fill-opacity="0.3"/><path d="M250 100C167.157 100 100 167.157 100 250C100 332.843 167.157 400 250 400C332.843 400 400 332.843 400 250C400 167.157 332.843 100 250 100ZM250 350C192.428 350 145 302.572 145 250C145 197.428 192.428 150 250 150C307.572 150 355 197.428 355 250C355 302.572 307.572 350 250 350Z" fill="%23ffd5d5" fill-opacity="0.2"/><path d="M400 150C367.157 150 340 177.157 340 210C340 242.843 367.157 270 400 270C432.843 270 460 242.843 460 210C460 177.157 432.843 150 400 150ZM400 240C383.431 240 370 226.569 370 210C370 193.431 383.431 180 400 180C416.569 180 430 193.431 430 210C430 226.569 416.569 240 400 240Z" fill="%23ffd5d5" fill-opacity="0.1"/></svg>');
            background-size: cover;
            opacity: 0.4;
            z-index: 0;
        }

        .left {
            flex: 1;
            /* Menggunakan flexbox */
            text-align: left;
            padding-right: 50px;
            z-index: 1;
            /* Di atas pattern */
        }

        .left h1 {
            font-family: 'Dancing Script', cursive;
            /* Menggunakan Dancing Script untuk tagline */
            font-size: 5.5em;
            /* Ukuran font lebih besar */
            color: #e86c8c;
            line-height: 1.1;
            text-align: left;
            margin-bottom: 20px;
        }

        .left p {
            margin-top: 20px;
            font-size: 1.1em;
            color: #666;
            text-align: left;
            line-height: 1.5;
        }

        .left .buttons {
            margin-top: 30px;
            text-align: left;
        }

        .btn-primary {
            background-color: #e86c8c;
            color: #fff;
            text-decoration: none;
            padding: 12px 30px;
            /* Ukuran tombol lebih besar */
            border-radius: 30px;
            font-weight: 600;
            /* Lebih tebal */
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #d55778;
            transform: translateY(-2px);
        }

        .image-area {
            flex: 1;
            /* Menggunakan flexbox */
            text-align: right;
            /* Gambar di kanan */
            z-index: 1;
            /* Di atas pattern */
        }

        .custom-image {
            display: block;
            width: 450px;
            /* Ukuran gambar utama */
            height: auto;
            max-width: 100%;
            /* Pastikan responsif */
            object-fit: contain;
            transform: translateX(0);
            /* Hapus transform */
            border-radius: 10px;
            /* Sedikit border radius */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            /* Sedikit shadow */
        }


        /* --- SECTION ABOUT --- */
        .about-section {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 50px;
            padding: 80px 50px;
            max-width: 1200px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .about-text {
            flex: 1;
            max-width: 600px;
            text-align: justify;
            /* Teks about justify */
        }

        .about-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5em;
            /* Ukuran h1 about */
            color: #e86c8c;
            margin-bottom: 20px;
            text-align: center;
            /* Judul about di tengah */
        }

        .about-text p {
            font-size: 1.05em;
            line-height: 1.6;
            color: #555;
            text-align: left;
            /* Teks di kiri */
        }

        .about-image {
            flex: 1;
            max-width: 500px;
            text-align: center;
            /* Pusatkan gambar */
        }

        .about-image img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* --- SECTION BOUQUET --- */
        .bouquet-section {
            padding: 60px 50px;
            max-width: 1200px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .bouquet-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5em;
            /* Ukuran h1 bouquet */
            color: #e86c8c;
            text-align: center;
            margin-bottom: 40px;
        }

        .bouquet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            /* Ukuran kartu disesuaikan */
            gap: 30px;
            justify-content: center;
            /* Untuk pusatkan kartu jika tidak penuh */
        }

        .bouquet-card {
            background: #fff;
            /* Latar belakang kartu putih solid */
            border-radius: 15px;
            /* Sedikit lebih kecil dari 20px */
            padding: 25px;
            /* Padding sedikit lebih besar */
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            /* Shadow sedikit lebih jelas */
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .bouquet-card:hover {
            transform: translateY(-8px);
            /* Efek hover lebih kentara */
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .bouquet-card img {
            max-width: 100%;
            height: 250px;
            /* Fixed height untuk konsistensi */
            object-fit: cover;
            border-radius: 10px;
            /* Radius gambar lebih kecil */
            margin-bottom: 20px;
            /* Jarak lebih besar */
        }

        .bouquet-card h3 {
            font-size: 1.6em;
            /* Ukuran h3 di kartu */
            color: #e86c8c;
            margin-bottom: 10px;
        }

        .bouquet-card p {
            font-size: 0.95em;
            margin-bottom: 15px;
            color: #666;
            line-height: 1.4;
        }

        .bouquet-card a.order-now {
            /* Memberi kelas untuk tombol Order Now */
            display: inline-block;
            padding: 10px 25px;
            background-color: #e86c8c;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1em;
        }

        .bouquet-card a.order-now:hover {
            background-color: #d55778;
            transform: translateY(-2px);
        }

        .instagram-section {
            text-align: center;
            margin-top: 50px;
            /* Jarak yang cukup */
        }

        .btn-instagram-bottom {
            display: inline-block;
            padding: 12px 30px;
            background-color: #E1306C;
            /* warna khas Instagram */
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            font-size: 18px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(225, 48, 108, 0.4);
        }

        .btn-instagram-bottom:hover {
            background-color: #AD1457;
            box-shadow: 0 6px 15px rgba(173, 20, 87, 0.6);
            transform: translateY(-2px);
        }

        /* --- CRUD Section for Products (Manage Products) --- */
        #crud-produk {
            padding: 60px 50px;
            max-width: 1200px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        #crud-produk h2 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5em;
            color: #e86c8c;
            margin-bottom: 40px;
            text-align: center;
        }

        #crud-produk h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: #e86c8c;
            margin-bottom: 20px;
            text-align: center;
        }

        #crud-produk .button {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px 5px 15px 0;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        #crud-produk .button:hover {
            opacity: 0.9;
        }

        .button.add {
            background-color: #4CAF50;
        }

        .button.edit {
            background-color: #2196F3;
        }

        .button.delete {
            background-color: #f44336;
        }

        #crud-produk table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        #crud-produk table th,
        #crud-produk table td {
            border: 1px solid #eee;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        #crud-produk table th {
            background-color: #f8f8f8;
            color: #555;
            font-weight: 600;
        }

        #crud-produk table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #crud-produk table td img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Form styling for CRUD */
        #crud-produk form {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
            font-size: 1.05em;
        }

        .form-group input[type="text"],
        .form-group input[type="number"] {
            width: calc(100% - 20px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }

        .form-group input[type="submit"] {
            width: auto;
            background-color: #e86c8c;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .form-group input[type="submit"]:hover {
            background-color: #d55778;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #4CAF50;
        }

        .message.error {
            background-color: #f44336;
        }

        /* --- OTHER PLACEHOLDER SECTIONS --- */
        .placeholder-section {
            padding: 100px 50px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 40px auto;
        }

        .placeholder-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5em;
            color: #999;
            margin-bottom: 30px;
        }

        .placeholder-section p {
            color: #777;
            font-size: 1.1em;
        }

        .contact-container {
            max-width: 1100px;
            margin: 100px auto;
            padding: 60px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        .contact-container h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5em;
            color: #e86c8c;
            margin-bottom: 40px;
        }

        .contact-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 40px;
            margin-bottom: 50px;
        }

        .contact-item {
            flex: 1 1 220px;
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-8px);
        }

        .contact-item i {
            font-size: 35px;
            color: #e86c8c;
            margin-bottom: 15px;
        }

        .contact-item p {
            font-size: 16px;
            color: #555;
            margin: 0;
        }

        /* Social Buttons */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .social-links a {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background-color: #e86c8c;
            color: white;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .social-links a:hover {
            transform: scale(1.1);
            background-color: #d64c72;
        }


        /* --- FOOTER --- */
        footer {
            text-align: center;
            padding: 20px;
            color: #fff;
            /* Warna teks footer */
            font-size: 14px;
            background-color: #e86c8c;
            /* Latar belakang footer */
            margin-top: auto;
        }

        /* Responsive Design */
        @media (max-width: 992px) {

            header,
            .main,
            .about-section,
            .bouquet-section,
            #crud-produk,
            .placeholder-section {
                padding: 20px;
            }

            header {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .logo {
                margin: 0;
            }


            .main {
                flex-direction: column;
                text-align: center;
            }

            .left {
                max-width: 100%;
                text-align: center;
                padding-right: 0;
                margin-bottom: 40px;
            }

            .left h1,
            .left p,
            .left .buttons {
                text-align: center;
            }

            .left h1 {
                font-size: 4em;
            }

            .left p {
                font-size: 1em;
            }

            .image-area {
                max-width: 100%;
            }

            .custom-image {
                width: 300px;
            }

            .about-section {
                flex-direction: column;
                text-align: center;
            }

            .about-text,
            .about-image {
                max-width: 100%;
            }

            .about-text h1,
            .about-text p {
                text-align: center;
            }

            .bouquet-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .bouquet-card img {
                height: 180px;
            }

            #crud-produk table th,
            #crud-produk table td {
                padding: 8px;
                font-size: 12px;
            }

            #crud-produk table td img {
                width: 40px;
                height: 40px;
            }

            #crud-produk .button {
                padding: 6px 10px;
                font-size: 0.8em;
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 3em;
            }

            .main h1 {
                font-size: 3.5em;
            }

            .about-text h1,
            .bouquet-section h1,
            #crud-produk h2,
            .placeholder-section h2,
            .contact-container h1 {
                font-size: 2.5em;
            }

            #crud-produk h3 {
                font-size: 1.5em;
            }

            .bouquet-card img {
                height: 150px;
            }

            .btn-instagram-bottom {
                padding: 10px 25px;
                font-size: 16px;
            }

            #crud-produk table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .contact-grid {
                flex-direction: column;
                align-items: center;
            }

            .contact-item {
                flex: none;
                width: 80%;
                /* Atur lebar item contact di mobile */
            }
        }

        @media (max-width: 480px) {

            header,
            .main,
            .about-section,
            .bouquet-section,
            #crud-produk,
            .placeholder-section {
                padding: 15px;
            }

            .main h1 {
                font-size: 2.8em;
            }

            .main p {
                font-size: 0.9em;
            }

            .about-text h1,
            .bouquet-section h1,
            #crud-produk h2,
            .placeholder-section h2,
            .contact-container h1 {
                font-size: 2em;
            }

            #crud-produk h3 {
                font-size: 1.2em;
            }

            .btn-primary {
                padding: 8px 18px;
                font-size: 0.9em;
            }

            .bouquet-card {
                padding: 15px;
            }

            .bouquet-card img {
                height: 120px;
            }

            .bouquet-card h3 {
                font-size: 1.2em;
            }

            .bouquet-card a.order-now {
                padding: 6px 15px;
                font-size: 0.8em;
            }

            .btn-instagram-bottom {
                padding: 8px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="cover.html">HOME</a></li>
                <li><a href="about.html">ABOUT</a></li>
                <li><a href="bouquet.html">BOUQUETS</a></li>
                <li><a href="order.html">ORDER</a></li>
                <li><a href="pembayaran.html">PAYMENT</a></li>
                <li><a href="contact.html">CONTACT</a></li>
            </ul>
        </nav>
        <div class="logo">
            <img src="asset/logo baru.png" alt="NaFlorist Logo" style="height: 50px;" />
        </div>
    </header>

    <section class="main">
        <div class="left">
            <h1>a bouquet<br />of love</h1>
            <p>
                Karena bunga bukan hanya indah,<br />
                tetapi juga mampu menyampaikan<br />
                yang tak bisa diucap oleh kata.
            </p>
            <div class="buttons">
                <a href="bouquet.html" class="btn-primary">Bouquet</a>
            </div>
        </div>
        <div class="image-area">
            <img src="asset/buket3.png" alt="Bouquet Image" class="custom-image" />
        </div>
    </section>

    <section id="about" class="about-section">
        <div class="about-text">
            <h1>Tentang NaFlorist</h1>
            <p>
                NaFlorist hadir dari cinta dan dedikasi untuk menghadirkan keindahan bunga dalam setiap momen spesial Anda.
                Kami percaya bahwa bunga tidak hanya sebagai hiasan, namun sebagai media untuk mengungkapkan perasaan yang sulit diucapkan.
                Dengan tim florist profesional dan kreatif, kami menghadirkan berbagai macam rangkaian bunga yang elegan, personal, dan bermakna.
            </p>
            <p style="margin-top:20px;">
                Baik untuk ulang tahun, pernikahan, anniversary, hingga ungkapan duka, NaFlorist selalu siap membantu Anda menyampaikan pesan dengan bunga terbaik.
            </p>
        </div>
        <div class="about-image">
            <img src="asset/Backgroundabout.png" alt="NaFlorist Team or Shop">
        </div>
    </section>

    <section id="bouquets" class="bouquet-section">
        <h1>Rekomendasi Buket</h1>
        <div class="bouquet-grid">
            <?php
            // Pastikan result_produk_display belum diambil sebelumnya atau reset pointer
            if ($result_produk_display->num_rows > 0) {
                $result_produk_display->data_seek(0); // Reset pointer untuk Bouquets section
                while ($row = $result_produk_display->fetch_assoc()) {
                    echo '<div class="bouquet-card">';
                    // Gunakan gambar dari database, jika tidak ada, gunakan placeholder
                    $gambar_url = $row['gambar_produk_url'] ? htmlspecialchars($row['gambar_produk_url']) : 'https://via.placeholder.com/250x250?text=No+Image';
                    echo '<img src="' . $gambar_url . '" alt="' . htmlspecialchars($row['nama_produk']) . '">';
                    echo '<h3>' . htmlspecialchars($row['nama_produk']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['jenis_bunga']) . '</p>';
                    echo '<p>Harga: Rp ' . number_format($row["harga"], 0, ',', '.') . '</p>';
                    echo '<a href="#order" class="order-now">Order Now</a>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align: center; width: 100%; color: #888;">Belum ada buket yang tersedia. Silakan tambahkan dari bagian "Manage Products".</p>';
            }
            ?>
        </div>
        <section class="instagram-section">
            <a href="https://instagram.com/na_florist" target="_blank" class="btn-instagram-bottom">Follow us on Instagram</a>
        </section>
    </section>

    <section id="crud-produk">
        <div class="container">
            <h2>Manage Products</h2>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'sukses') {
                    echo '<div class="message success">Operasi berhasil dilakukan!</div>';
                } else {
                    $error_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Terjadi kesalahan.';
                    echo '<div class="message error">Operasi gagal! ' . $error_msg . '</div>';
                }
            }
            ?>

            <div>
                <h3><?php echo ($data_produk_edit ? 'Edit Produk' : 'Tambah Produk Baru'); ?></h3>
                <form action="index.php#crud-produk" method="POST">
                    <input type="hidden" name="action" value="<?php echo ($data_produk_edit ? 'edit' : 'tambah'); ?>">
                    <?php if ($data_produk_edit): ?>
                        <input type="hidden" name="id_produk    " value="<?php echo htmlspecialchars($data_produk_edit['id_produk   ']); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nama_produk">Nama Produk:</label>
                        <input type="text" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($data_produk_edit['nama_produk'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_bunga">Jenis Bunga:</label>
                        <input type="text" id="jenis_bunga" name="jenis_bunga" value="<?php echo htmlspecialchars($data_produk_edit['jenis_bunga'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <input type="number" step="0.01" id="harga" name="harga" value="<?php echo htmlspecialchars($data_produk_edit['harga'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok:</label>
                        <input type="number" id="stok" name="stok" value="<?php echo htmlspecialchars($data_produk_edit['stok'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gambar_produk_url">URL Gambar Produk (opsional):</label>
                        <input type="text" id="gambar_produk_url" name="gambar_produk_url" value="<?php echo htmlspecialchars($data_produk_edit['gambar_produk_url'] ?? ''); ?>">
                        <?php if ($data_produk_edit && $data_produk_edit['gambar_produk_url']): ?>
                            <p style="margin-top: 5px; font-size: 0.9em;"><img src="<?php echo htmlspecialchars($data_produk_edit['gambar_produk_url']); ?>" alt="Current Image" style="max-width: 100px; height: auto; border-radius: 5px; vertical-align: middle; margin-right: 10px;">Gambar saat ini.</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="<?php echo ($data_produk_edit ? 'Update Produk' : 'Simpan Produk'); ?>">
                        <?php if ($data_produk_edit): ?>
                            <a href="index.php#crud-produk" class="button">Batal Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <h3>Daftar Produk</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Jenis Bunga</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_produk_display->num_rows > 0) {
                        $result_produk_display->data_seek(0);
                        while ($row = $result_produk_display->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["id_produk  "]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["nama_produk"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["jenis_bunga"]) . "</td>";
                            echo "<td>" . number_format($row["harga"], 2, ',', '.') . "</td>";
                            echo "<td>" . htmlspecialchars($row["stok"]) . "</td>";
                            echo "<td>";
                            if ($row["gambar_produk_url"]) {
                                echo '<a href="' . htmlspecialchars($row["gambar_produk_url"]) . '" target="_blank"><img src="' . htmlspecialchars($row["gambar_produk_url"]) . '" alt="Gambar Produk" style="width: 50px; height: 50px; object-fit: cover; border-radius: 3px;"></a>';
                            } else {
                                echo '-';
                            }
                            echo "</td>";
                            echo "<td>";
                            echo "<a href='index.php?edit_id=" . $row["id_produk    "] . "#crud-produk' class='button edit'>Edit</a> ";
                            echo "<a href='index.php?action=hapus&id=" . $row["id_produk    "] . "#crud-produk' class='button delete' onclick='return confirm(\"Yakin ingin menghapus produk ini?\")'>Hapus</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Tidak ada produk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <section id="order" class="placeholder-section">
        <div class="container">
            <h2>Order Form (Coming Soon)</h2>
            <p>Formulir pemesanan akan ada di sini.</p>
        </div>
    </section>

    <section id="payment" class="placeholder-section">
        <div class="container">
            <h2>Payment Details (Coming Soon)</h2>
            <p>Halaman pembayaran akan ada di sini.</p>
        </div>
    </section>

    <section id="contact" class="contact-container">
        <h1>Contact Us</h1>
        <div class="contact-grid">
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <p>Jalan Utama No. 123, Kota Bunga</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <p>+62 812 3456 7890</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <p>info@naflorist.com</p>
            </div>
        </div>
        <div class="social-links">
            <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://instagram.com/na_florist" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
        </div>
    </section>

    <footer>
        &copy; 2025 NaFlorist. All rights reserved.
    </footer>

</body>

</html>
<?php
// Tutup koneksi database di akhir file
$koneksi->close();
?>