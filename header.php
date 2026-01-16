<?php
if (!isset($_SESSION)) session_start();
?>

<div class="navbar">
  <div class="logo">NaFlorist</div>
  <div class="user-info">
    <?php if (isset($_SESSION['username'])): ?>
      Halo, <strong><?= $_SESSION['username'] ?></strong> |
      <a href="logout.php" class="logout-btn">Logout</a>
    <?php endif; ?>
  </div>
</div>

<style>
.navbar {
  background-color: #f8d1dc;
  padding: 10px 20px;
  font-family: 'Roboto', sans-serif;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.logo {
  font-family: 'Playfair Display', serif;
  font-size: 22px;
}
.user-info {
  font-size: 16px;
}
.logout-btn {
  margin-left: 10px;
  text-decoration: none;
  color: #d63384;
}
</style>
