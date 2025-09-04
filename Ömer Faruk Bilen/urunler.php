<?php
// Veritabanı bağlantısını dahil et
require_once "includes/db_connection.php";

// İletişim bilgilerini veritabanından al
$settings = [];
$settings_sql = "SELECT setting_key, setting_value FROM settings";
$settings_result = $conn->query($settings_sql);

if ($settings_result->num_rows > 0) {
    while($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Kategorileri al
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);

// Ürünleri al
$products_sql = "SELECT p.*, c.name as category_name FROM products p 
               LEFT JOIN categories c ON p.category_id = c.id 
               ORDER BY p.id DESC";
$products_result = mysqli_query($conn, $products_sql);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler - Avantaj Tedarik</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Ürünler Sayfası Özel Stiller */
        .products-section {
            padding: 80px 0;
        }
        
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

        .product-categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 40px;
        }

        .category-btn {
            padding: 10px 20px;
            background-color: var(--light-gray);
            color: var(--dark-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
        }

        .category-btn.active, .category-btn:hover {
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background-color: var(--white-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .product-card:hover .product-img img {
            transform: scale(1.1);
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            display: inline-block;
            background-color: var(--light-gray);
            color: var(--primary-color);
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .product-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark-color);
        }

        .product-description {
            color: var(--gray-color);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-actions .btn {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .product-actions .info-link {
            color: var(--gray-color);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .product-actions .info-link:hover {
            color: var(--primary-color);
        }

        /* Filtreleme için */
        .product-card.hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .products-container {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .products-container {
                grid-template-columns: 1fr;
            }
        }

        /* Ürün bulunamadı mesajı */
        .no-products {
            text-align: center;
            padding: 40px;
            background-color: var(--white-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            grid-column: 1 / -1;
        }

        .no-products i {
            font-size: 3rem;
            color: var(--gray-color);
            margin-bottom: 20px;
        }

        .no-products h3 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .no-products p {
            color: var(--gray-color);
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
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="urunler.php" class="active">Ürünler</a></li>
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

    <!-- Ürünler Başlangıç -->
    <section class="products-section">
        <div class="container">
            <h2 class="section-title">Ürünlerimiz</h2>
            
            <div class="product-categories">
                <button class="category-btn active" data-category="all">Tümü</button>
                <?php 
                // Kategorileri listele
                if(mysqli_num_rows($categories_result) > 0){
                    while($category = mysqli_fetch_assoc($categories_result)){
                        echo '<button class="category-btn" data-category="' . $category["id"] . '">' . htmlspecialchars($category["name"]) . '</button>';
                    }
                }
                ?>
            </div>
            
            <div class="products-container">
                <?php
                // Ürünleri listele
                if(mysqli_num_rows($products_result) > 0){
                    while($product = mysqli_fetch_assoc($products_result)){
                        // Ürün resmi kontrolü
                        $image_path = "uploads/" . $product["image"];
                        if(!file_exists($image_path) || empty($product["image"])){
                            $image_path = "uploads/default.jpg";
                        }
                        
                        // Debug bilgisi ekle
                        error_log("Ürün ID: " . $product["id"] . ", Resim: " . $product["image"] . ", Yol: " . $image_path . ", Var mı: " . (file_exists($image_path) ? "Evet" : "Hayır"));
                        
                        // Resim yolunu düzelt
                        if(!file_exists($image_path) && !empty($product["image"])){
                            // uploads klasörünü oluştur (eğer yoksa)
                            if(!is_dir("uploads")){
                                mkdir("uploads", 0755, true);
                            }
                            
                            // Varsayılan resim yoksa, hazır bir resim kullan
                            if(!file_exists("uploads/default.jpg")){
                                // Hazır bir varsayılan resim kopyala (eğer varsa)
                                if(file_exists("uploads/68af582a700cc.jpg")) {
                                    copy("uploads/68af582a700cc.jpg", "uploads/default.jpg");
                                } else {
                                    // Hiçbir resim yoksa boş bir dosya oluştur
                                    file_put_contents("uploads/default.jpg", "");
                                }
                            }
                            
                            $image_path = "uploads/default.jpg";
                        }
                        
                        // Fiyat formatı
                        $price = number_format($product["price"], 2, ',', '.');
                        
                        echo '<div class="product-card" data-category="' . $product["category_id"] . '">
                                <div class="product-img">
                                    <img src="' . $image_path . '" alt="' . htmlspecialchars($product["name"]) . '">
                                </div>
                                <div class="product-info">
                                    <span class="product-category">' . htmlspecialchars($product["category_name"]) . '</span>
                                    <h3 class="product-title">' . htmlspecialchars($product["name"]) . '</h3>
                                    <p class="product-description">' . htmlspecialchars($product["description"]) . '</p>
                                    <div class="product-actions">
                                        <a href="iletisim.php?product=' . $product["id"] . '" class="btn">Teklif Al</a>
                                    </div>
                                </div>
                            </div>';
                    }
                } else {
                    // Ürün bulunamadı mesajı
                    echo '<div class="no-products">
                            <i class="fas fa-box-open"></i>
                            <h3>Henüz ürün bulunmuyor</h3>
                            <p>Çok yakında yeni ürünlerimiz eklenecektir.</p>
                          </div>';
                }
                ?>
            </div>
        </div>
    </section>
    <!-- Ürünler Bitiş -->

    <!-- İletişim Kartı Başlangıç -->
    <section class="contact-card-section">
        <div class="container">
            <div class="contact-card">
                <div class="contact-card-content">
                    <h2>Bizimle Çalışmak İçin İletişime Geçin</h2>
                    <p>Kurumsal tedarik çözümleri için hemen bize ulaşın. Size özel teklifler sunalım.</p>
                    <div class="contact-info-items">
                        <div class="contact-info-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo isset($settings['phone']) ? htmlspecialchars($settings['phone']) : '+90 212 123 45 67'; ?></span>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo isset($settings['email']) ? htmlspecialchars($settings['email']) : 'info@avantajtedarik.com'; ?></span>
                        </div>
                    </div>
                    <a href="iletisim.php" class="btn contact-btn">Hemen İletişime Geç</a>
                </div>
                <div class="contact-card-image">
                    <img src="images/depo.jpg" alt="İletişim">
                </div>
            </div>
        </div>
    </section>
    <!-- İletişim Kartı Bitiş -->

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
    <script>
        // Ürün Kategorileri Filtreleme
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.category-btn');
            const productCards = document.querySelectorAll('.product-card');
            
            categoryButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Aktif buton sınıfını güncelle
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    
                    const category = button.getAttribute('data-category');
                    
                    // Ürünleri filtrele
                    productCards.forEach(card => {
                        if (category === 'all' || card.getAttribute('data-category') === category) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                });
            });
        });
    </script>
    
    <?php if(isset($settings['whatsapp']) && !empty($settings['whatsapp'])): ?>
    <!-- Sabit WhatsApp Butonu -->
    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['whatsapp']); ?>" class="floating-whatsapp" target="_blank" title="WhatsApp'tan Ulaşın">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>
</body>
</html>