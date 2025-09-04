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

// Referansları getir
$references = [];
$sql = "SELECT * FROM `references` ORDER BY name";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $references[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referanslar - Avantaj Tedarik</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'includes/styles.php'; ?>
    <style>
        /* Referanslar Sayfası Özel Stiller */
        .references-hero {
            background-color: var(--primary-color);
            color: var(--white-color);
            text-align: center;
            padding: 100px 0;
        }

        .references-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .references-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1>Referanslarımız</h1>
            <p>Güvenilir iş ortaklarımız ve başarılı projelerimiz ile sizlere en iyi hizmeti sunuyoruz.</p>
        </div>
    </section>

    <!-- References Section -->
    <section class="references-section">
        <div class="container">
            <h2 class="section-title">İş Ortaklarımız</h2>
            <div class="references-grid">
                <?php foreach($references as $reference): ?>
                    <div class="reference-card">
                        <img src="uploads/references/<?= htmlspecialchars($reference['logo']); ?>" 
                             alt="<?= htmlspecialchars($reference['name']); ?>" 
                             class="reference-logo">
                        <h3 class="reference-name"><?= htmlspecialchars($reference['name']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="projects-section">
        
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
       
    </section>

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

    <script src="js/main.js"></script>
</body>
</html>