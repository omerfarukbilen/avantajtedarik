<?php
// Yapılandırma dosyasını dahil et
require_once "config.php";

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
redirectToLogin();

// Banner bilgilerini getir (AJAX isteği için)
if(isset($_GET["action"]) && $_GET["action"] == "get" && isset($_GET["id"])){
    $id = sanitize($_GET["id"]);
    
    $sql = "SELECT * FROM banners WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){
                $banner = mysqli_fetch_assoc($result);
                echo json_encode(["success" => true, "banner" => $banner]);
                exit();
            } else {
                echo json_encode(["success" => false, "message" => "Banner bulunamadı."]);
                exit();
            }
        } else {
            echo json_encode(["success" => false, "message" => "Bir hata oluştu."]);
            exit();
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "message" => "Bir hata oluştu."]);
        exit();
    }
}

// Banner ekleme işlemi
if(isset($_POST["action"]) && $_POST["action"] == "add"){
    // Form verilerini al
    $title = sanitize($_POST["title"]);
    $description = isset($_POST["description"]) ? sanitize($_POST["description"]) : "";
    $link = isset($_POST["link"]) ? sanitize($_POST["link"]) : "";
    $status = isset($_POST["status"]) ? 1 : 0;
    
    // Resim yükleme işlemi
    $target_dir = "../uploads/banners/";
    
    // Uploads klasörü yoksa oluştur
    if(!file_exists("../uploads/")){
        mkdir("../uploads/", 0777, true);
    }
    
    // Banners klasörü yoksa oluştur
    if(!file_exists($target_dir)){
        mkdir($target_dir, 0777, true);
    }
    
    $image_name = "";
    $upload_error = false;
    
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed_types = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
        
        if(in_array($_FILES["image"]["type"], $allowed_types)){
            $image_name = time() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                // Resim başarıyla yüklendi
            } else {
                $upload_error = true;
            }
        } else {
            $upload_error = true;
        }
    } else {
        $upload_error = true;
    }
    
    if($upload_error){
        header("location: banners.php?error=upload");
        exit();
    }
    
    // Veritabanına kaydet
    $sql = "INSERT INTO banners (title, description, image, link, status) VALUES (?, ?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssi", $param_title, $param_description, $param_image, $param_link, $param_status);
        
        $param_title = $title;
        $param_description = $description;
        $param_image = $image_name;
        $param_link = $link;
        $param_status = $status;
        
        if(mysqli_stmt_execute($stmt)){
            header("location: banners.php?success=added");
            exit();
        } else {
            echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
}

// Banner düzenleme işlemi
if(isset($_POST["action"]) && $_POST["action"] == "edit"){
    // Form verilerini al
    $id = sanitize($_POST["id"]);
    $title = sanitize($_POST["title"]);
    $description = isset($_POST["description"]) ? sanitize($_POST["description"]) : "";
    $link = isset($_POST["link"]) ? sanitize($_POST["link"]) : "";
    $status = isset($_POST["status"]) ? 1 : 0;
    
    // Mevcut resim bilgisini al
    $current_image = "";
    $sql = "SELECT image FROM banners WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt, $current_image);
                mysqli_stmt_fetch($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Yeni resim yüklendi mi kontrol et
    $image_name = $current_image;
    $upload_error = false;
    
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $target_dir = "../uploads/banners/";
        $allowed_types = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
        
        if(in_array($_FILES["image"]["type"], $allowed_types)){
            $image_name = time() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                // Eski resmi sil
                if(!empty($current_image) && file_exists("../uploads/banners/" . $current_image)){
                    unlink("../uploads/banners/" . $current_image);
                }
            } else {
                $upload_error = true;
            }
        } else {
            $upload_error = true;
        }
    }
    
    if($upload_error && isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        header("location: banners.php?error=upload");
        exit();
    }
    
    // Veritabanını güncelle
    $sql = "UPDATE banners SET title = ?, description = ?, image = ?, link = ?, status = ? WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssii", $param_title, $param_description, $param_image, $param_link, $param_status, $param_id);
        
        $param_title = $title;
        $param_description = $description;
        $param_image = $image_name;
        $param_link = $link;
        $param_status = $status;
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            header("location: banners.php?success=updated");
            exit();
        } else {
            echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
}
?>