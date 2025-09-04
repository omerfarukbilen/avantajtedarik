<?php
// Veritabanı bağlantısını dahil et
require_once "includes/db_connection.php";

// İletişim bilgilerini veritabanından al
$settings = [];
$sql = "SELECT setting_key, setting_value FROM settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avantaj Tedarik</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* WhatsApp Buton Stilleri */
        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            background-color: #25D366;
            color: white;
            padding: 10px 15px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .whatsapp-btn i {
            font-size: 20px;
            margin-right: 8px;
        }
        
        .whatsapp-btn:hover {
            background-color: #128C7E;
            transform: scale(1.05);
        }
        
        /* Sabit WhatsApp Butonu */
        .floating-whatsapp {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25D366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .floating-whatsapp i {
            font-size: 30px;
        }
        
        .floating-whatsapp:hover {
            transform: scale(1.1);
            background-color: #128C7E;
        }
        
        /* Mobil Banner Düzeltmesi */
        @media (max-width: 768px) {
            .banner-section {
                margin-top: 0;
                padding-top: 0;
            }
            
            .banner-container {
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Başlangıç -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="images/logo.png" alt="Avantaj Tedarik Logo" style="height: 80px;"></a>
            </div>
            <div class="menu">
                <ul>
                    <li><a href="index.php" class="active">Ana Sayfa</a></li>
                    <li><a href="urunler.php">Ürünler</a></li>
                    <li><a href="hakkimizda.php">Hakkımızda</a></li>
                    <li><a href="referanslar.php">Referanslar</a></li>
                    <li><a href="iletisim.php">İletişim</a></li>
                </ul>
            </div>
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>
    <!-- Navbar Bitiş -->

    <!-- Banner Başlangıç -->
    <section class="banner-section">
        <div class="banner-container">
            <div class="banner-wrapper">
                <?php
                // Banners tablosunu oluştur (eğer yoksa)
                $sql = "CREATE TABLE IF NOT EXISTS banners (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    image VARCHAR(255) NOT NULL,
                    link VARCHAR(255),
                    status TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";

                if (!mysqli_query($conn, $sql)) {
                    echo "Tablo oluşturma hatası: " . mysqli_error($conn);
                }
                
                // Aktif bannerları getir
                $sql = "SELECT * FROM banners WHERE status = 1 ORDER BY id DESC";
                $result = mysqli_query($conn, $sql);
                
                $banner_count = mysqli_num_rows($result);
                
                if ($banner_count > 0) {
                    // Veritabanından banner verilerini göster
                    while($row = mysqli_fetch_assoc($result)) {
                        $image_path = "uploads/banners/" . $row["image"];
                        $title = htmlspecialchars($row["title"]);
                        $description = htmlspecialchars($row["description"]);
                        $link = !empty($row["link"]) ? htmlspecialchars($row["link"]) : "urunler.php";
                        
                        echo "<div class='banner-slide'>";
                        echo "<a href='$link' class='banner-link'>";
                        echo "<img src='$image_path' alt='$title'>";
                        echo "</a>";
                        echo "</div>";
                    }
                } else {
                    // Varsayılan banner göster (veritabanında banner yoksa)
                    echo "<div class='banner-slide'>";
                    echo "<a href='urunler.php' class='banner-link'>";
                    echo "<img src='uploads/default-banner.jpg' alt='Avantaj Tedarik' onerror=\"this.src='uploads/68af582a700cc.jpg';\">"; 
                    echo "</a>";
                    echo "</div>";
                }
                ?>
            </div>
            <div class="banner-controls">
                <button class="prev-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="next-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="banner-dots">
                <?php
                // Banner noktalarını dinamik olarak oluştur
                if ($banner_count > 0) {
                    for ($i = 0; $i < $banner_count; $i++) {
                        $active_class = ($i == 0) ? "active" : "";
                        echo "<span class='dot $active_class' data-index='$i'></span>";
                    }
                } else {
                    echo "<span class='dot active' data-index='0'></span>";
                }
                ?>
            </div>
        </div>
    </section>
    <!-- Banner Bitiş -->

    <!-- Neden Avantaj Tedarik Başlangıç -->
    <section class="advantages-section">
        <div class="container">
            <h2 class="section-title">Neden Avantaj Tedarik?</h2>
            <div class="advantages-container">
                <div class="advantage-box">
                    <div class="advantage-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h3>Hızlı Teslimat</h3>
                    <p>Siparişleriniz en kısa sürede hazırlanıp kapınıza teslim edilir.</p>
                </div>
                <div class="advantage-box">
                    <div class="advantage-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Kaliteli Ürünler</h3>
                    <p>Tüm ürünlerimiz kalite standartlarına uygun olarak tedarik edilir.</p>
                </div>
                <div class="advantage-box">
                    <div class="advantage-icon">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <h3>Uygun Fiyatlar</h3>
                    <p>Piyasanın en uygun fiyatlarıyla tedarik çözümleri sunuyoruz.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Neden Avantaj Tedarik Bitiş -->

    <!-- Markalarımız Başlangıç -->
    <section class="brands-section">
        <div class="container">
            <h2 class="section-title">Markalarımız</h2>
            <div class="brands-slider-container">
                <div class="brands-slider">
                    <?php
                    // Markaları veritabanından çek
                    $sql = "SELECT * FROM brands ORDER BY name ASC";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $logo_path = file_exists("uploads/brands/" . $row["logo"]) ? "uploads/brands/" . $row["logo"] : "uploads/default.jpg";
                            
                            echo "<div class='brand-item'>\n";
                            echo "    <img src='" . $logo_path . "' alt='" . htmlspecialchars($row["name"]) . "'>\n";
                            echo "</div>\n";
                        }
                    } else {
                        echo "<p>Henüz marka bulunmamaktadır.</p>";
                    }
                    ?>
                </div>
                <button class="brand-prev-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="brand-next-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>
    <!-- Markalarımız Bitiş -->

    <!-- Öne Çıkan Ürünler Başlangıç -->
    <section class="featured-products-section">
        <div class="container">
            <h2 class="section-title">Öne Çıkan Ürünler</h2>
            <div class="featured-products-container">
                <?php
                // Öne çıkan ürünleri veritabanından çek (son eklenen 4 ürün)
                $sql = "SELECT p.*, c.name as category_name FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        ORDER BY p.id DESC LIMIT 4";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $image_path = "uploads/" . $row["image"];
                        if(!file_exists($image_path) || empty($row["image"])){
                            // uploads klasörünü oluştur (eğer yoksa)
                            if(!is_dir("uploads")){
                                mkdir("uploads", 0755, true);
                            }
                            
                            // Varsayılan resim yoksa oluştur
                            if(!file_exists("uploads/default.jpg")){
                                // GD kütüphanesi yüklü mü kontrol et
                                if(function_exists('imagecreatetruecolor')){
                                    // Basit bir varsayılan resim oluştur
                                    $default_img = imagecreatetruecolor(300, 300);
                                    $blue = imagecolorallocate($default_img, 0, 86, 179); // #0056b3
                                    $white = imagecolorallocate($default_img, 255, 255, 255);
                                    
                                    // Arka planı mavi yap
                                    imagefill($default_img, 0, 0, $blue);
                                    
                                    // Metin ekle
                                    imagestring($default_img, 5, 100, 140, "Urun Resmi", $white);
                                    
                                    // Resmi kaydet
                                    imagejpeg($default_img, "uploads/default.jpg");
                                    imagedestroy($default_img);
                                } else {
                                    // GD kütüphanesi yüklü değilse, boş bir dosya oluştur
                                    file_put_contents("uploads/default.jpg", "");
                                }
                            }
                            
                            $image_path = "uploads/default.jpg";
                        }
                        $price_formatted = number_format($row["price"], 2, ",", ".") . " ₺";
                        
                        echo "<div class='product-card'>";
                        echo "    <div class='product-image'>";
                        echo "        <img src='" . $image_path . "' alt='" . htmlspecialchars($row["name"]) . "'>";
                        echo "    </div>";
                        echo "    <div class='product-info'>";
                        echo "        <h3>" . htmlspecialchars($row["name"]) . "</h3>";
                        echo "        <p class='product-category'>" . htmlspecialchars($row["category_name"]) . "</p>";
                        echo "    </div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Henüz ürün bulunmamaktadır.</p>";
                }
                ?>
            </div>
            <div class="view-all-link">
                <a href="urunler.php" class="btn">Tüm Ürünleri Görüntüle</a>
            </div>
        </div>
    </section>
    <!-- Öne Çıkan Ürünler Bitiş -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // URL'den mesaj parametresini kontrol et
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            
            if (message) {
                // Mesaj varsa ekranda göster
                const alertDiv = document.createElement('div');
                alertDiv.textContent = decodeURIComponent(message);
                alertDiv.style.position = 'fixed';
                alertDiv.style.top = '20px';
                alertDiv.style.left = '50%';
                alertDiv.style.transform = 'translateX(-50%)';
                alertDiv.style.backgroundColor = '#4CAF50';
                alertDiv.style.color = 'white';
                alertDiv.style.padding = '15px 20px';
                alertDiv.style.borderRadius = '4px';
                alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                alertDiv.style.zIndex = '9999';
                
                document.body.appendChild(alertDiv);
                
                // 5 saniye sonra mesajı kaldır
                setTimeout(function() {
                    alertDiv.style.opacity = '0';
                    alertDiv.style.transition = 'opacity 0.5s';
                    setTimeout(function() {
                        document.body.removeChild(alertDiv);
                        // URL'den parametreyi temizle
                        history.replaceState(null, null, window.location.pathname);
                    }, 500);
                }, 5000);
            }
        });
    </script>

    <!-- Footer Başlangıç -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2><?php echo isset($settings['company_name']) ? strtoupper(htmlspecialchars($settings['company_name'])) : 'AVANTAJ TEDARİK'; ?></h2>
                    <p>Tüketim ve tedarik çözümlerinde güvenilir partneriniz.</p>
                </div>
                <div class="footer-links">
                    <h3>Hızlı Erişim</h3>
                    <ul>
                        <li><a href="index.php">Ana Sayfa</a></li>
                        <li><a href="urunler.php">Ürünler</a></li>
                        <li><a href="hakkimizda.php">Hakkımızda</a></li>
                        <li><a href="referanslar.php">Referanslar</a></li>
                        <li><a href="iletisim.php">İletişim</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>İletişim</h3>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo isset($settings['address']) ? htmlspecialchars($settings['address']) : 'Merkez Mah. Tedarik Cad. No:123 İstanbul'; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo isset($settings['phone']) ? htmlspecialchars($settings['phone']) : '+90 212 123 45 67'; ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo isset($settings['email']) ? htmlspecialchars($settings['email']) : 'info@avantajtedarik.com'; ?></p>
                    <?php if(isset($settings['working_hours']) && !empty($settings['working_hours'])): ?>
                    <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($settings['working_hours']); ?></p>
                    <?php endif; ?>
                    <?php if(isset($settings['whatsapp']) && !empty($settings['whatsapp'])): ?>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['whatsapp']); ?>" class="whatsapp-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp'tan Ulaşın
                    </a>
                    <?php endif; ?>
                </div>
                <div class="footer-social">
                    <h3>Sosyal Medya</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 Avantaj Tedarik. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>
    <!-- Footer Bitiş -->

    <script src="script.js"></script>
    
    <?php if(isset($settings['whatsapp']) && !empty($settings['whatsapp'])): ?>
    <!-- Sabit WhatsApp Butonu -->
    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['whatsapp']); ?>" class="floating-whatsapp" target="_blank" title="WhatsApp'tan Ulaşın">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>
</body>
</html>