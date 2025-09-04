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
    <title>Hakkımızda - Avantaj Tedarik</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Hakkımızda Sayfası Özel Stiller */
        .about-hero {
            background-color: var(--primary-color);
            color: var(--white-color);
            text-align: center;
            padding: 100px 0;
        }

        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .about-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .about-section {
            padding: 80px 0;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-image {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .about-text h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .about-text p {
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .mission-vision {
            background-color: var(--light-gray);
            padding: 80px 0;
        }

        .mission-vision-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .mission-box, .vision-box {
            background-color: var(--white-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .mission-box h3, .vision-box h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
        }

        .mission-box h3 i, .vision-box h3 i {
            margin-right: 15px;
            font-size: 2rem;
        }

        .team-section {
            padding: 80px 0;
        }

        .team-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }

        .team-member {
            background-color: var(--white-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .member-img {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .member-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .team-member:hover .member-img img {
            transform: scale(1.1);
        }

        .member-info {
            padding: 20px;
            text-align: center;
        }

        .member-name {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .member-position {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .member-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .member-social a {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 35px;
            height: 35px;
            background-color: var(--light-gray);
            color: var(--primary-color);
            border-radius: 50%;
            transition: var(--transition);
        }

        .member-social a:hover {
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        .history-section {
            background-color: var(--light-gray);
            padding: 80px 0;
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: var(--primary-color);
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -2px;
        }

        .timeline-item {
            padding: 10px 40px;
            position: relative;
            width: 50%;
            box-sizing: border-box;
        }

        .timeline-item:nth-child(odd) {
            left: 0;
        }

        .timeline-item:nth-child(even) {
            left: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: var(--white-color);
            border: 4px solid var(--primary-color);
            border-radius: 50%;
            top: 15px;
            z-index: 1;
        }

        .timeline-item:nth-child(odd)::after {
            right: -12px;
        }

        .timeline-item:nth-child(even)::after {
            left: -12px;
        }

        .timeline-content {
            padding: 20px;
            background-color: var(--white-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .timeline-year {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .timeline-title {
            margin-bottom: 10px;
            color: var(--dark-color);
        }

        .timeline-text {
            color: var(--gray-color);
        }

        @media (max-width: 992px) {
            .about-content,
            .mission-vision-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .about-image {
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }

            .about-hero p {
                font-size: 1rem;
            }

            .timeline::after {
                left: 40px;
            }

            .timeline-item {
                width: 100%;
                padding-left: 80px;
                padding-right: 0;
            }

            .timeline-item:nth-child(even) {
                left: 0;
            }

            .timeline-item::after {
                left: 30px;
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
                    <li><a href="hakkimizda.php" class="active">Hakkımızda</a></li>
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

    <!-- Hero Başlangıç -->
    <section class="about-hero">
        <div class="container">
            <h1>Hakkımızda</h1>
            <p>2005 yılından beri kurumsal tedarik çözümleri sunarak işletmelerin ihtiyaçlarını karşılıyoruz. Kaliteli ürün ve hizmetlerimizle müşterilerimizin güvenilir iş ortağıyız.</p>
        </div>
    </section>
    <!-- Hero Bitiş -->

    <!-- Hakkımızda İçerik Başlangıç -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Biz Kimiz?</h2>
                    <p>Avantaj Tedarik, 2005 yılında kurumsal müşterilere yönelik tedarik çözümleri sunmak amacıyla kurulmuştur. Kurulduğumuz günden bu yana, müşterilerimizin ihtiyaçlarını en hızlı ve en ekonomik şekilde karşılamak için çalışıyoruz.</p>
                    <p>Ofis malzemeleri, temizlik ürünleri, gıda ürünleri ve elektronik ürünler başta olmak üzere geniş ürün yelpazemizle şirketlerin tüm tedarik ihtiyaçlarını tek bir noktadan karşılıyoruz. Uzman ekibimiz, müşterilerimizin ihtiyaçlarını analiz ederek en uygun çözümleri sunmaktadır.</p>
                    <p>Müşteri memnuniyetini her zaman ön planda tutarak, kaliteli ürünleri uygun fiyatlarla sunmayı ve zamanında teslimat yapmayı ilke edindik. Tedarik süreçlerini kolaylaştırarak müşterilerimizin iş süreçlerine değer katmayı hedefliyoruz.</p>
                </div>
                <div class="about-image">
                    <img src="images/depo.jpg" alt="Avantaj Tedarik">
                </div>
            </div>
        </div>
    </section>
    <!-- Hakkımızda İçerik Bitiş -->

    <!-- Misyon ve Vizyon Başlangıç -->
    <section class="mission-vision">
        <div class="container">
            <h2 class="section-title">Misyon ve Vizyonumuz</h2>
            <div class="mission-vision-content">
                <div class="mission-box">
                    <h3><i class="fas fa-bullseye"></i> Misyonumuz</h3>
                    <p>Müşterilerimizin tedarik süreçlerini kolaylaştırarak, iş verimliliğini artırmak ve maliyetlerini düşürmek için kaliteli ürün ve hizmetler sunmak. Her zaman müşteri memnuniyetini ön planda tutarak, güvenilir ve sürdürülebilir iş ortaklıkları kurmak.</p>
                </div>
                <div class="vision-box">
                    <h3><i class="fas fa-binoculars"></i> Vizyonumuz</h3>
                    <p>Kurumsal tedarik sektöründe lider konuma gelerek, yenilikçi çözümler ve dijital dönüşüm ile müşterilerimizin tedarik süreçlerini optimize etmek. Sürdürülebilir iş modelleri geliştirerek, çevreye duyarlı tedarik çözümleri sunmak ve sektörde örnek bir firma olmak.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Misyon ve Vizyon Bitiş -->

   

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