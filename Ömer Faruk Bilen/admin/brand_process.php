<?php
require_once "config.php";

// Oturum kontrolü
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Yeni marka ekleme işlemi
if (isset($_POST["add_brand"])) {
    $name = sanitize($_POST["name"]);
    $logo = "default_logo.jpg"; // Varsayılan logo
    
    // Logo yükleme işlemi
    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["logo"]["name"];
        $filetype = $_FILES["logo"]["type"];
        $filesize = $_FILES["logo"]["size"];
        
        // Dosya uzantısını doğrula
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $_SESSION["error_message"] = "Hata: Lütfen geçerli bir dosya formatı seçin. (JPG, JPEG, PNG, GIF)";
            header("location: brands.php");
            exit;
        }
        
        // Dosya boyutunu kontrol et (5MB max)
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $_SESSION["error_message"] = "Hata: Dosya boyutu çok büyük. Maksimum dosya boyutu: 5MB";
            header("location: brands.php");
            exit;
        }
        
        // Dosya türünü kontrol et
        if (in_array($filetype, $allowed)) {
            // Dosya zaten var mı kontrol et
            $new_filename = uniqid() . "." . $ext;
            $upload_path = "../uploads/brands/" . $new_filename;
            
            // Uploads/brands klasörü yoksa oluştur
            if (!file_exists("../uploads/brands/")) {
                mkdir("../uploads/brands/", 0777, true);
            }
            
            // Dosyayı yükle
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $upload_path)) {
                $logo = $new_filename;
            } else {
                $_SESSION["error_message"] = "Hata: Dosya yüklenirken bir sorun oluştu.";
                header("location: brands.php");
                exit;
            }
        } else {
            $_SESSION["error_message"] = "Hata: Dosya türü desteklenmiyor.";
            header("location: brands.php");
            exit;
        }
    }
    
    // Veritabanına marka ekle
    $sql = "INSERT INTO brands (name, logo) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $name, $logo);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["success_message"] = "Marka başarıyla eklendi.";
    } else {
        $_SESSION["error_message"] = "Marka eklenirken bir hata oluştu: " . mysqli_error($conn);
    }
    
    header("location: brands.php");
    exit;
}

// Marka düzenleme işlemi
if (isset($_POST["edit_brand"])) {
    $id = $_POST["id"];
    $name = sanitize($_POST["name"]);
    
    // Mevcut logoyu al
    $sql = "SELECT logo FROM brands WHERE id = ?";
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
            header("location: brands.php");
            exit;
        }
        
        // Dosya boyutunu kontrol et (5MB max)
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $_SESSION["error_message"] = "Hata: Dosya boyutu çok büyük. Maksimum dosya boyutu: 5MB";
            header("location: brands.php");
            exit;
        }
        
        // Dosya türünü kontrol et
        if (in_array($filetype, $allowed)) {
            // Eski logoyu sil (varsayılan logo değilse)
            if ($current_logo != "default_logo.jpg") {
                $old_logo_path = "../uploads/" . $current_logo;
                if (file_exists($old_logo_path)) {
                    unlink($old_logo_path);
                }
            }
            
            // Yeni logoyu yükle
            $new_filename = uniqid() . "." . $ext;
            $upload_path = "../uploads/" . $new_filename;
            
            // Uploads klasörü yoksa oluştur
            if (!file_exists("../uploads/")) {
                mkdir("../uploads/", 0777, true);
            }
            
            // Dosyayı yükle
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $upload_path)) {
                $logo = $new_filename;
                
                // Veritabanını güncelle (logo dahil)
                $sql = "UPDATE brands SET name = ?, logo = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssi", $name, $logo, $id);
            } else {
                $_SESSION["error_message"] = "Hata: Dosya yüklenirken bir sorun oluştu.";
                header("location: brands.php");
                exit;
            }
        } else {
            $_SESSION["error_message"] = "Hata: Dosya türü desteklenmiyor.";
            header("location: brands.php");
            exit;
        }
    } else {
        // Sadece ismi güncelle
        $sql = "UPDATE brands SET name = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["success_message"] = "Marka başarıyla güncellendi.";
    } else {
        $_SESSION["error_message"] = "Marka güncellenirken bir hata oluştu: " . mysqli_error($conn);
    }
    
    header("location: brands.php");
    exit;
}

// İşlem yoksa brands.php'ye yönlendir
header("location: brands.php");
exit;
?>