<?php
// Yapılandırma dosyasını dahil et
require_once "config.php";

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
redirectToLogin();

// POST isteği kontrolü
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Değişkenleri tanımla
    $name = $id = "";
    $name_err = "";
    
    // Kategori adını doğrula
    if(empty(trim($_POST["name"]))){
        $name_err = "Lütfen kategori adını girin.";
    } else{
        $name = sanitize($_POST["name"]);
    }
    
    // Hata yoksa devam et
    if(empty($name_err)){
        // Düzenleme mi yoksa ekleme mi olduğunu kontrol et
        if(isset($_POST["id"]) && !empty(trim($_POST["id"]))){
            // Kategori güncelleme
            $id = sanitize($_POST["id"]);
            
            $sql = "UPDATE categories SET name = ? WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "si", $param_name, $param_id);
                
                $param_name = $name;
                $param_id = $id;
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: categories.php?success=updated");
                    exit();
                } else{
                    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                mysqli_stmt_close($stmt);
            }
        } else{
            // Yeni kategori ekleme
            $sql = "INSERT INTO categories (name) VALUES (?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "s", $param_name);
                
                $param_name = $name;
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: categories.php?success=added");
                    exit();
                } else{
                    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        // Hata varsa kategoriler sayfasına yönlendir
        header("location: categories.php?error=invalid_input");
        exit();
    }
} else {
    // POST isteği değilse kategoriler sayfasına yönlendir
    header("location: categories.php");
    exit();
}
?>