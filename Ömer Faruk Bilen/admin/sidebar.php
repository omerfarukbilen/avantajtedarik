<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

// Aktif sayfayı belirle
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Kontrol Paneli</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="products.php">
                    <i class="fas fa-box"></i>
                    <span>Ürünler</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'categories.php' ? 'active' : '' ?>" href="categories.php">
                    <i class="fas fa-tags"></i>
                    <span>Kategoriler</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'brands.php' ? 'active' : '' ?>" href="brands.php">
                    <i class="fas fa-copyright"></i>
                    <span>Markalar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'banners.php' ? 'active' : '' ?>" href="banners.php">
                    <i class="fas fa-images"></i>
                    <span>Bannerlar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'references.php' ? 'active' : '' ?>" href="references.php">
                    <i class="fas fa-handshake"></i>
                    <span>Referanslar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>" href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Ayarlar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-globe"></i>
                    <span>Siteyi Görüntüle</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

 <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Avantaj Tedarik</h2>
                <p>Yönetim Paneli</p>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Kontrol Paneli</span>
                </a>
                <a href="products.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Ürünler</span>
                </a>
                <a href="categories.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Kategoriler</span>
                </a>
                <a href="brands.php" class="menu-item">
                    <i class="fas fa-copyright"></i>
                    <span>Markalar</span>
                </a>
                <a href="banners.php" class="menu-item">
                    <i class="fas fa-images"></i>
                    <span>Bannerlar</span>
                </a>
                <a href="references.php" class="menu-item active">
                    <i class="fas fa-handshake"></i>
                    <span>Referanslar</span>
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Ayarlar</span>
                </a>
                <a href="../index.php" class="menu-item">
                    <i class="fas fa-globe"></i>
                    <span>Siteyi Görüntüle</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></div>
                        <div class="user-role">Yönetici</div>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </div>
        </aside>
