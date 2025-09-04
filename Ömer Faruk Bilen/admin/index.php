<?php
// Yapılandırma dosyasını dahil et
require_once "config.php";

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
redirectToLogin();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli - Avantaj Tedarik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0056b3;
            --secondary-color: #003366;
            --accent-color: #0077cc;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --text-color: #333;
            --white-color: #fff;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--dark-color);
            color: var(--white-color);
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            color: var(--white-color);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--light-gray);
            font-size: 0.9rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--white-color);
            border-left-color: var(--accent-color);
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .user-avatar i {
            font-size: 1.2rem;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--light-gray);
        }

        .logout-btn {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white-color);
            border-radius: 5px;
            text-decoration: none;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.7rem;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: var(--white-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background-color: rgba(0, 86, 179, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1.2rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .card-description {
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        /* Mobile Responsive */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 1001;
            transition: var(--transition);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .menu-toggle.active {
                left: 270px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Avantaj Tedarik</h2>
                <p>Yönetim Paneli</p>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-tachometer-alt active"></i>
                    <span>Kontrol Paneli</span>
                </a>
                <a href="products.php" class="menu-item ">
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
                <a href="references.php" class="menu-item">
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

        <!-- Main Content -->
        <main class="main-content">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="page-header">
                <h1>Kontrol Paneli</h1>
                <div class="breadcrumb">
                    <span>Ana Sayfa</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Kontrol Paneli</span>
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3 class="card-title">Toplam Ürün</h3>
                    <?php
                    // Toplam ürün sayısını al
                    $sql = "SELECT COUNT(*) as total FROM products";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $total_products = $row['total'];
                    ?>
                    <div class="card-value"><?php echo $total_products; ?></div>
                    <p class="card-description">Sistemde kayıtlı toplam ürün sayısı</p>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="card-title">Toplam Kategori</h3>
                    <?php
                    // Toplam kategori sayısını al
                    $sql = "SELECT COUNT(*) as total FROM categories";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $total_categories = $row['total'];
                    ?>
                    <div class="card-value"><?php echo $total_categories; ?></div>
                    <p class="card-description">Sistemde kayıtlı toplam kategori sayısı</p>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="card-title">Toplam Kullanıcı</h3>
                    <?php
                    // Toplam kullanıcı sayısını al
                    $sql = "SELECT COUNT(*) as total FROM users";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $total_users = $row['total'];
                    ?>
                    <div class="card-value"><?php echo $total_users; ?></div>
                    <p class="card-description">Sistemde kayıtlı toplam kullanıcı sayısı</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-copyright"></i>
                    </div>
                    <h3 class="card-title">Toplam Marka</h3>
                    <?php
                    // Toplam marka sayısını al
                    $sql = "SELECT COUNT(*) as total FROM brands";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $total_brands = $row['total'];
                    ?>
                    <div class="card-value"><?php echo $total_brands; ?></div>
                    <p class="card-description">Sistemde kayıtlı toplam marka sayısı</p>
                </div>
            </div>

            <div class="card">
                <h3 class="card-title">Hızlı Erişim</h3>
                <p class="card-description">Aşağıdaki bağlantıları kullanarak hızlıca işlem yapabilirsiniz.</p>
                <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="products.php" style="padding: 10px 20px; background-color: var(--primary-color); color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i> Yeni Ürün Ekle
                    </a>
                    <a href="categories.php" style="padding: 10px 20px; background-color: var(--accent-color); color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i> Yeni Kategori Ekle
                    </a>
                    <a href="brands.php" style="padding: 10px 20px; background-color: var(--info-color); color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i> Yeni Marka Ekle
                    </a>
                    <a href="../index.php" style="padding: 10px 20px; background-color: var(--gray-color); color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-globe" style="margin-right: 8px;"></i> Siteyi Görüntüle
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    </script>
</body>
</html>