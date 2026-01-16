<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pembayaran - NaFlorist</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('asset/background.png');
      background-size: cover;
      background-position: center;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      margin: 60px auto;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
      max-width: 700px;
      width: 90%;
    }

    h2 {
      color: #e86c8c;
      text-align: center;
      margin-bottom: 20px;
    }

    .summary {
      background-color: #fff0f5;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
    }

    .summary p {
      margin: 10px 0;
      font-size: 16px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="file"] {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
    }

    button {
      background-color: #e86c8c;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background-color: #d14c70;
    }

    footer {
      margin-top: auto;
      text-align: center;
      padding: 20px;
      background-color: rgba(255, 230, 240, 0.7);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 30px;
      background-color: white;
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

    nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <!-- Header disalin dari about.html -->
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
     <div class="logo-logout" style="display: flex; align-items: center; gap: 15px;">
      <img src="asset/logo baru.png" alt="NaFlorist Logo" style="height: 50px;">
      <?php if (isset($_SESSION['username'])): ?>
        <span style="font-weight: bold; color: #333;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
          <a href="login.html" title="Login">
          <img src="asset/user.png" alt="Login Icon" style="height: 35px; cursor: pointer;">
        </a>
      <?php else: ?>
       <a href="logout.php" title="Logout">
          <img src="asset/logout.png" alt="Logout Icon" style="height: 35px; cursor: pointer;">
        </a>
      <?php endif; ?>
    </div>
  </header>

<!-- Register Form -->
<div class="login-container">
  <div class="login-card">
    <h2>Buat Akun Baru</h2>
    <form action="proses_register.php" method="POST">
      <div class="input-group">
        <i class="fa fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" class="btn-login">Daftar</button>
      <a href="login.html" class="btn-daftar">Sudah punya akun? Login</a>
    </form>
  </div>
</div>

</body>
</html>
