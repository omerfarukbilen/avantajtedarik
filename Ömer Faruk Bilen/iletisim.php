<?php
require_once 'includes/db_connection.php';

// İletişim bilgilerini veritabanından al
$settings = [];
$sql = "SELECT setting_key, setting_value FROM settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - Avantaj Tedarik</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* İletişim Sayfası Özel Stiller */
        .contact-hero {
            background-color: var(--primary-color);
            color: var(--white-color);
            text-align: center;
            padding: 100px 0;
        }

        .contact-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .contact-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-section {
            padding: 80px 0;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }

        .contact-info {
            background-color: var(--white-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .contact-info h3 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 15px;
        }

        .contact-info h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .contact-details {
            margin-bottom: 30px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: var(--light-gray);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .contact-text h4 {
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .contact-text p {
            color: var(--gray-color);
            line-height: 1.6;
        }

        .contact-social {
            margin-top: 30px;
        }

        .contact-social h4 {
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            background-color: var(--light-gray);
            color: var(--primary-color);
            border-radius: 50%;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background-color: var(--primary-color);
            color: var(--white-color);
            transform: translateY(-5px);
        }

        .contact-form {
            background-color: var(--white-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .contact-form h3 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 15px;
        }

        .contact-form h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        .map-section {
            padding: 0 0 80px;
        }

        .map-container {
            height: 450px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        .branches-section {
            background-color: var(--light-gray);
            padding: 80px 0;
        }

        .branches-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .branch-card {
            background-color: var(--white-color);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .branch-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .branch-title {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
        }

        .branch-title i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .branch-address {
            margin-bottom: 15px;
            color: var(--gray-color);
            line-height: 1.6;
        }

        .branch-contact {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .branch-contact a {
            display: flex;
            align-items: center;
            color: var(--dark-color);
            transition: var(--transition);
        }

        .branch-contact a i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .branch-contact a:hover {
            color: var(--primary-color);
        }

        @media (max-width: 992px) {
            .contact-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .contact-hero h1 {
                font-size: 2.5rem;
            }

            .contact-hero p {
                font-size: 1rem;
            }
        }

        @media (max-width: 768px) {
            .contact-info,
            .contact-form {
                padding: 30px;
            }

            .map-container {
                height: 350px;
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
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="urunler.php">Ürünler</a></li>
                    <li><a href="hakkimizda.php">Hakkımızda</a></li>
                    <li><a href="referanslar.php">Referanslar</a></li>
                    <li><a href="iletisim.php" class="active">İletişim</a></li>
                </ul>
            </div>
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>
    <!-- Navbar Bitiş -->

    <!-- Hero Başlangıç -->
    <section class="contact-hero">
        <div class="container">
            <h1>İletişim</h1>
            <p>Sorularınız, önerileriniz veya iş birliği talepleriniz için bizimle iletişime geçebilirsiniz. Size en kısa sürede dönüş yapacağız.</p>
        </div>
    </section>
    <!-- Hero Bitiş -->

    <!-- İletişim Başlangıç -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-container" style="grid-template-columns: 1fr;">
                <div class="contact-info">
                    <h3>İletişim Bilgilerimiz</h3>
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Adres</h4>
                                <p><?php echo isset($settings['company_address']) ? nl2br(htmlspecialchars($settings['company_address'])) : 'Merkez Mah. Tedarik Cad. No:123<br>Şişli / İstanbul'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Telefon</h4>
                                <p><?php echo isset($settings['company_phone']) ? nl2br(htmlspecialchars($settings['company_phone'])) : '+90 212 123 45 67<br>+90 212 123 45 68'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h4>E-posta</h4>
                                <p><?php echo isset($settings['company_email']) ? nl2br(htmlspecialchars($settings['company_email'])) : 'info@avantajtedarik.com<br>satis@avantajtedarik.com'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Çalışma Saatleri</h4>
                                <p><?php echo isset($settings['working_hours']) ? nl2br(htmlspecialchars($settings['working_hours'])) : 'Pazartesi - Cuma: 09:00 - 18:00<br>Cumartesi: 09:00 - 13:00'; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-social">
                        <h4>Sosyal Medyada Biz</h4>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- İletişim Bitiş -->

    <!-- Harita Başlangıç -->
    <section class="map-section">
        <div class="container">
            <div class="map-container">
                <?php if(isset($settings['map_embed']) && !empty($settings['map_embed'])): ?>
                    <?php echo $settings['map_embed']; ?>
                <?php else: ?>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d48168.83165063414!2d28.94992871953124!3d41.03703389999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7650656bd63%3A0x8ca058b28c20b6c3!2zxZ5pxZ9saSwgxLBzdGFuYnVs!5e0!3m2!1str!2str!4v1625145124352!5m2!1str!2str" allowfullscreen="" loading="lazy"></iframe>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- Harita Bitiş -->

    <!-- Şubeler Başlangıç -->
    <section class="branches-section">
        <div class="container">
            <h2 class="section-title">Şubelerimiz</h2>
            <div class="branches-container">
                <?php if(isset($settings['company_name']) && !empty($settings['company_name'])): ?>
                <div class="branch-card">
                    <h3 class="branch-title"><i class="fas fa-building"></i> <?php echo htmlspecialchars($settings['company_name']); ?></h3>
                    <p class="branch-address"><?php echo nl2br(htmlspecialchars($settings['company_address'] ?? 'Merkez Mah. Tedarik Cad. No:123<br>Şişli / İstanbul')); ?></p>
                    <div class="branch-contact">
                        <a href="tel:<?php echo preg_replace('/\s+/', '', $settings['company_phone'] ?? '+902121234567'); ?>"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($settings['company_phone'] ?? '+90 212 123 45 67'); ?></a>
                        <a href="mailto:<?php echo htmlspecialchars($settings['company_email'] ?? 'info@avantajtedarik.com'); ?>"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($settings['company_email'] ?? 'info@avantajtedarik.com'); ?></a>
                    </div>
                </div>
                <?php else: ?>
                <div class="branch-card">
                    <h3 class="branch-title"><i class="fas fa-building"></i> İstanbul (Merkez)</h3>
                    <p class="branch-address">Merkez Mah. Tedarik Cad. No:123<br>Şişli / İstanbul</p>
                    <div class="branch-contact">
                        <a href="tel:+902121234567"><i class="fas fa-phone"></i> +90 212 123 45 67</a>
                        <a href="mailto:istanbul@avantajtedarik.com"><i class="fas fa-envelope"></i> istanbul@avantajtedarik.com</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- Şubeler Bitiş -->

    <!-- Footer Başlangıç -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2>AVANTAJ TEDARİK</h2>
                    <p>Tüketim ve tedarik çözümlerinde güvenilir partneriniz.</p>
                </div>
                <div class="footer-links">
                    <h3>Hızlı Erişim</h3>
                    <ul>
                        <li><a href="index.html">Ana Sayfa</a></li>
                        <li><a href="urunler.html">Ürünler</a></li>
                        <li><a href="hakkimizda.html">Hakkımızda</a></li>
                        <li><a href="referanslar.html">Referanslar</a></li>
                        <li><a href="iletisim.html">İletişim</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>İletişim</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Merkez Mah. Tedarik Cad. No:123 İstanbul</p>
                    <p><i class="fas fa-phone"></i> +90 212 123 45 67</p>
                    <p><i class="fas fa-envelope"></i> info@avantajtedarik.com</p>
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
</body>
</html>