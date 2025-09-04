-- Veritabanı oluşturma
CREATE DATABASE IF NOT EXISTS avantaj_tedarik;
USE avantaj_tedarik;

-- Markalar tablosu
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT 'default_logo.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Ürün kategorileri tablosu
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE
);

-- Ürünler tablosu
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Örnek veriler
-- Admin kullanıcısı (şifre: admin123)
INSERT INTO users (username, password, email, full_name) VALUES
('admin', '$2y$10$8MNwKnFgVTYYOI0.cQmVZeRVWVbN0Ck2N9KZP8QL.IQZJJ9xqUUdm', 'admin@avantajtedarik.com', 'Admin Kullanıcı');

-- Kategoriler
INSERT INTO categories (name, slug) VALUES
('Ofis Malzemeleri', 'ofis'),
('Temizlik Ürünleri', 'temizlik'),
('Gıda Ürünleri', 'gida'),
('Elektronik', 'elektronik');

-- Referanslar tablosu
CREATE TABLE IF NOT EXISTS `references` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT 'default_logo.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Örnek markalar
INSERT INTO brands (name, logo) VALUES
('HP', 'hp_logo.png'),
('Canon', 'canon_logo.png'),
('Faber-Castell', 'faber_castell_logo.png'),
('Domestos', 'domestos_logo.png'),
('Nescafe', 'nescafe_logo.png'),
('Lipton', 'lipton_logo.png'),
('SanDisk', 'sandisk_logo.png'),
('Logitech', 'logitech_logo.png');

-- Örnek ürünler
INSERT INTO products (category_id, name, description, price, image) VALUES
(1, 'A4 Fotokopi Kağıdı (80gr)', 'Yüksek kaliteli, 500 yaprak A4 fotokopi kağıdı. Çift taraflı baskı için uygundur.', 120.00, 'kagit_a4.jpg'),
(1, 'Tükenmez Kalem Seti', 'Mavi, siyah ve kırmızı renklerde 10\'lu tükenmez kalem seti.', 45.50, 'kalem_seti.jpg'),
(1, 'Zımba Makinesi', 'Metal gövdeli, dayanıklı zımba makinesi. 20 sayfaya kadar zımbalama kapasitesi.', 65.75, 'zimba.jpg'),
(2, 'Yüzey Temizleyici', 'Tüm yüzeyler için antibakteriyel temizleyici. 1 litre.', 35.90, 'yuzey_temizleyici.jpg'),
(2, 'Çöp Poşeti (Büyük Boy)', '80x110 cm, 10\'lu paket, dayanıklı çöp poşeti.', 28.50, 'cop_poseti.jpg'),
(3, 'Filtre Kahve', 'Öğütülmüş filtre kahve, 250gr paket.', 89.90, 'filtre_kahve.jpg'),
(3, 'Çay', 'Siyah çay, 1kg paket.', 120.00, 'cay.jpg'),
(4, 'USB Bellek 32GB', '32GB kapasiteli, USB 3.0 flash bellek.', 175.00, 'usb_bellek.jpg'),
(4, 'Kablosuz Mouse', 'Ergonomik tasarımlı, kablosuz optik mouse.', 220.00, 'mouse.jpg');