<?php
// Veritabanı bağlantı bilgileri
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'avantaj_tedarik');

// Veritabanına bağlanma
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Bağlantıyı kontrol etme
if($conn === false){
    die("HATA: Veritabanına bağlanılamadı. " . mysqli_connect_error());
}

// Oturum başlatma
session_start();

// Kullanıcı giriş yapmış mı kontrol etme fonksiyonu
function isLoggedIn() {
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        return true;
    } else {
        return false;
    }
}

// Kullanıcı giriş yapmamışsa login sayfasına yönlendirme fonksiyonu
function redirectToLogin() {
    if(!isLoggedIn()) {
        header("location: login.php");
        exit;
    }
}

// XSS saldırılarına karşı koruma fonksiyonu
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
?>