# Avantaj Tedarik Web Sitesi

Bu proje, Avantaj Tedarik şirketi için bir web sitesi ve yönetim paneli içermektedir. Web sitesi HTML, CSS ve JavaScript ile oluşturulmuştur. Yönetim paneli PHP ve MySQL kullanılarak geliştirilmiştir.

## Özellikler

### Web Sitesi
- Anasayfa
- Ürünler sayfası
- Hakkımızda sayfası
- İletişim formu
- Responsive tasarım

### Yönetim Paneli
- Güvenli giriş sistemi
- Dashboard (Genel bakış)
- Ürün yönetimi
- Kategori yönetimi
- Sipariş yönetimi
- Mesaj yönetimi
- Kullanıcı yönetimi

## Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Web sunucusu (Apache, Nginx vb.)

### Adımlar

1. Dosyaları web sunucunuza yükleyin.

2. MySQL veritabanı oluşturun:
   - MySQL'e giriş yapın
   - `veritabani.sql` dosyasını içe aktarın:
     ```
     mysql -u kullanici_adi -p veritabani_adi < veritabani.sql
     ```
   - Veya phpMyAdmin kullanarak `veritabani.sql` dosyasını içe aktarın.

3. `db_connect.php` dosyasını düzenleyin:
   - Veritabanı bağlantı bilgilerinizi güncelleyin (sunucu, kullanıcı adı, şifre, veritabanı adı).

4. Web tarayıcınızdan siteye erişin.

## Yönetim Paneli Erişimi

- URL: `http://siteniz.com/admin/`
- Kullanıcı adı: `admin`
- Şifre: `admin123`

**Not:** Güvenlik için ilk girişten sonra şifrenizi değiştirmeniz önerilir.

## İletişim Formu

İletişim formu, PHP ve MySQL kullanılarak arka planda çalışmaktadır. Form gönderildiğinde:

1. Veriler doğrulanır
2. Veritabanına kaydedilir
3. Kullanıcıya başarılı mesajı gösterilir

Yönetim panelinden gelen mesajları görüntüleyebilir, okundu olarak işaretleyebilir ve silebilirsiniz.

## Dosya Yapısı

```
/
├── index.html              # Anasayfa
├── urunler.html            # Ürünler sayfası
├── hakkimizda.html         # Hakkımızda sayfası
├── iletisim.html           # İletişim sayfası
├── iletisim.php            # İletişim formu işleme
├── db_connect.php          # Veritabanı bağlantısı
├── veritabani.sql          # Veritabanı şeması
├── css/                    # CSS dosyaları
├── js/                     # JavaScript dosyaları
├── img/                    # Resim dosyaları
└── admin/                  # Yönetim paneli
    ├── index.php           # Giriş sayfası
    ├── dashboard.php       # Dashboard
    ├── messages.php        # Mesaj yönetimi
    ├── products.php        # Ürün yönetimi
    ├── categories.php      # Kategori yönetimi
    ├── orders.php          # Sipariş yönetimi
    ├── users.php           # Kullanıcı yönetimi
    ├── settings.php        # Ayarlar
    ├── logout.php          # Çıkış işlemi
    └── css/                # Admin CSS dosyaları
```

## Güvenlik

- Şifreler veritabanında hash'lenerek saklanmaktadır
- Form verileri doğrulanmaktadır
- SQL injection koruması için PDO kullanılmaktadır
- XSS koruması için HTML çıktıları filtrelenmektedir

## Lisans

Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.