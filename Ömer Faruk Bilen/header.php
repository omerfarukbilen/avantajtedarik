<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Navbar Başlangıç -->
<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="index.php"><img src="images/logo.png" alt="Avantaj Tedarik Logo" style="height: 80px;"></a>
        </div>
        <div class="menu">
            <ul>
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Ana Sayfa</a></li>
                <li><a href="urunler.php" <?php echo basename($_SERVER['PHP_SELF']) == 'urunler.php' ? 'class="active"' : ''; ?>>Ürünler</a></li>
                <li><a href="hakkimizda.php" <?php echo basename($_SERVER['PHP_SELF']) == 'hakkimizda.php' ? 'class="active"' : ''; ?>>Hakkımızda</a></li>
                <li><a href="referanslar.php" <?php echo basename($_SERVER['PHP_SELF']) == 'referanslar.php' ? 'class="active"' : ''; ?>>Referanslar</a></li>
                <li><a href="iletisim.php" <?php echo basename($_SERVER['PHP_SELF']) == 'iletisim.php' ? 'class="active"' : ''; ?>>İletişim</a></li>
            </ul>
        </div>
        <div class="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>
<!-- Navbar Bitiş -->