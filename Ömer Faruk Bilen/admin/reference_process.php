<?php
session_start();
require_once 'config.php';

// Kullanıcı girişi kontrolü
if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

// Referans ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_reference"])) {
    $name = sanitize($_POST["name"]);
    
    // Logo yükleme işlemi
    $logo = "default_logo.jpg";
    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["logo"]["name"];
        $filetype = $_FILES["logo"]["type"];
        $filesize = $_FILES["logo"]["size"];
        
        // Dosya uzantısını doğrula
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $_SESSION["error_message"] = "Hata: Lütfen geçerli bir dosya formatı seçin. (JPG, JPEG, PNG, GIF)";
            header("location: references.php");
            exit;
        }
        
        // Dosya boyutunu kontrol et (5MB max)
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $_SESSION["error_message"] = "Hata: Dosya boyutu çok büyük. Maksimum dosya boyutu: 5MB";
            header("location: references.php");
            exit;
        }
        
        // Dosya türünü kontrol et
        if (in_array($filetype, $allowed)) {
            // Yeni dosya adı oluştur
            $logo = time() . "_" . $filename;
            
            // Dosyayı yükle
            if (!file_exists("../uploads/references")) {
                mkdir("../uploads/references", 0777, true);
            }
            
            $target = "../uploads/references/" . $logo;
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target)) {
                // Dosya başarıyla yüklendi
            } else {
                $_SESSION["error_message"] = "Hata: Dosya yüklenirken bir sorun oluştu.";
                header("location: references.php");
                exit;
            }
        } else {
            $_SESSION["error_message"] = "Hata: Dosya türü desteklenmiyor.";
            header("location: references.php");
            exit;
        }
    }
    
    // Veritabanına ekle
    $sql = "INSERT INTO references (name, logo) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $name, $logo);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["success_message"] = "Referans başarıyla eklendi.";
            header("location: references.php");
            exit;
        } else {
            $_SESSION["error_message"] = "Hata: " . mysqli_error($conn);
            header("location: references.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt);
}

// Referans güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_reference"])) {
    $id = $_POST["id"];
    $name = sanitize($_POST["name"]);
    
    // Mevcut logoyu al
    $sql = "SELECT logo FROM references WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $current_logo = $row["logo"];
    
    // Yeni logo yüklendi mi kontrol et
    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["logo"]["name"];
        $filetype = $_FILES["logo"]["type"];
        $filesize = $_FILES["logo"]["size"];
        
        // Dosya uzantısını doğrula
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $_SESSION["error_message"] = "Hata: Lütfen geçerli bir dosya formatı seçin. (JPG, JPEG, PNG, GIF)";
            header("location: references.php");
            exit;
        }
        
        // Dosya boyutunu kontrol et (5MB max)
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $_SESSION["error_message"] = "Hata: Dosya boyutu çok büyük. Maksimum dosya boyutu: 5MB";
            header("location: references.php");
            exit;
        }
        
        // Dosya türünü kontrol et
        if (in_array($filetype, $allowed)) {
            // Eski logoyu sil (varsayılan logo değilse)
            if ($current_logo != "default_logo.jpg" && file_exists("../uploads/references/" . $current_logo)) {
                unlink("../uploads/references/" . $current_logo);
            }
            
            // Yeni dosya adı oluştur
            $new_logo = time() . "_" . $filename;
            
            // Dosyayı yükle
            if (!file_exists("../uploads/references")) {
                mkdir("../uploads/references", 0777, true);
            }
            
            $target = "../uploads/references/" . $new_logo;
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target)) {
                // Veritabanını güncelle
                $sql = "UPDATE references SET name = ?, logo = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssi", $name, $new_logo, $id);
            } else {
                $_SESSION["error_message"] = "Hata: Dosya yüklenirken bir sorun oluştu.";
                header("location: references.php");
                exit;
            }
        } else {
            $_SESSION["error_message"] = "Hata: Dosya türü desteklenmiyor.";
            header("location: references.php");
            exit;
        }
    } else {
        // Sadece ismi güncelle
        $sql = "UPDATE references SET name = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["success_message"] = "Referans başarıyla güncellendi.";
        header("location: references.php");
        exit;
    } else {
        $_SESSION["error_message"] = "Hata: " . mysqli_error($conn);
        header("location: references.php");
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Referans silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete"])) {
    $id = $_GET["delete"];
    
    // Logoyu al ve sil
    $sql = "SELECT logo FROM references WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row["logo"] != "default_logo.jpg" && file_exists("../uploads/references/" . $row["logo"])) {
        unlink("../uploads/references/" . $row["logo"]);
    }
    
    // Veritabanından sil
    $sql = "DELETE FROM references WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["success_message"] = "Referans başarıyla silindi.";
        header("location: references.php");
        exit;
    } else {
        $_SESSION["error_message"] = "Hata: " . mysqli_error($conn);
        header("location: references.php");
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Bağlantıyı kapat
mysqli_close($conn);
?>