<?php
// Yapılandırma dosyasını dahil et
require_once "config.php";

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
redirectToLogin();

// Değişkenleri tanımla
$id = $name = $description = $price = $category_id = $image = "";
$name_err = $price_err = $category_err = $image_err = "";
$is_edit = false;

// Uploads klasörünü kontrol et ve yoksa oluştur
if (!file_exists("../uploads")) {
    mkdir("../uploads", 0777, true);
}

// Düzenleme modu için ürün bilgilerini al
if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = sanitize($_GET["id"]);
    $is_edit = true;
    
    // Ürün bilgilerini al
    $sql = "SELECT * FROM products WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                $name = $row["name"];
                $description = $row["description"];
                $price = $row["price"];
                $category_id = $row["category_id"];
                $image = $row["image"];
            } else{
                // Ürün bulunamadı, ürünler sayfasına yönlendir
                header("location: products.php");
                exit();
            }
        } else{
            echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Form gönderildiğinde işlem yap
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Ürün adını doğrula
    if(empty(trim($_POST["name"]))){
        $name_err = "Lütfen ürün adını girin.";
    } else{
        $name = sanitize($_POST["name"]);
    }
    
    // Açıklamayı al
    $description = isset($_POST["description"]) ? sanitize($_POST["description"]) : "";
    
    // Fiyat alanı kaldırıldı
    $price = 0; // Varsayılan değer
    
    // Kategoriyi doğrula
    if(empty(trim($_POST["category_id"]))){
        $category_err = "Lütfen bir kategori seçin.";
    } else{
        $category_id = sanitize($_POST["category_id"]);
    }
    
    // Resim yükleme işlemi
    $upload_image = true;
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed_types = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif");
        $file_name = $_FILES["image"]["name"];
        $file_type = $_FILES["image"]["type"];
        $file_size = $_FILES["image"]["size"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        
        // Dosya uzantısını al
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Dosya türünü kontrol et
        if(!array_key_exists($file_ext, $allowed_types) || !in_array($file_type, $allowed_types)){
            $image_err = "Sadece JPG, JPEG, PNG ve GIF dosyaları yükleyebilirsiniz.";
            $upload_image = false;
        }
        
        // Dosya boyutunu kontrol et (2MB)
        if($file_size > 2097152){
            $image_err = "Dosya boyutu 2MB'dan küçük olmalıdır.";
            $upload_image = false;
        }
        
        if($upload_image){
            // Benzersiz bir dosya adı oluştur
            $new_file_name = uniqid() . "." . $file_ext;
            $upload_path = "../uploads/" . $new_file_name;
            
            // Dosyayı yükle
            if(move_uploaded_file($file_tmp, $upload_path)){
                // Eğer düzenleme modundaysa ve eski resim varsa sil
                if($is_edit && !empty($image) && $image != "default.jpg" && file_exists("../uploads/" . $image)){
                    unlink("../uploads/" . $image);
                }
                $image = $new_file_name;
            } else{
                $image_err = "Dosya yüklenirken bir hata oluştu.";
            }
        }
    } else if(!$is_edit){
        // Yeni ürün ekleniyorsa ve resim yüklenmemişse varsayılan resmi kullan
        $image = "default.jpg";
    }
    // Düzenleme modunda ve yeni resim yüklenmemişse mevcut resmi koru
    
    // Hata yoksa veritabanına kaydet
    if(empty($name_err) && empty($category_err) && empty($image_err)){
        if($is_edit){
            // Ürünü güncelle
            $sql = "UPDATE products SET name=?, description=?, price=?, category_id=?, image=? WHERE id=?";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "ssdiis", $param_name, $param_description, $param_price, $param_category_id, $param_image, $param_id);
                
                $param_name = $name;
                $param_description = $description;
                $param_price = $price;
                $param_category_id = $category_id;
                $param_image = $image;
                $param_id = $id;
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: products.php?success=updated");
                    exit();
                } else{
                    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                mysqli_stmt_close($stmt);
            }
        } else{
            // Yeni ürün ekle
            $sql = "INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "ssdis", $param_name, $param_description, $param_price, $param_category_id, $param_image);
                
                $param_name = $name;
                $param_description = $description;
                $param_price = $price;
                $param_category_id = $category_id;
                $param_image = $image;
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: products.php?success=added");
                    exit();
                } else{
                    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? "Ürün Düzenle" : "Yeni Ürün Ekle"; ?> - Avantaj Tedarik Yönetim Paneli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0056b3;
            --secondary-color: #003366;
            --accent-color: #0077cc;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --text-color: #333;
            --white-color: #fff;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --transition: all 0.3s ease;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--dark-color);
            color: var(--white-color);
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            color: var(--white-color);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--light-gray);
            font-size: 0.9rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--white-color);
            border-left-color: var(--accent-color);
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .user-avatar i {
            font-size: 1.2rem;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--light-gray);
        }

        .logout-btn {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white-color);
            border-radius: 5px;
            text-decoration: none;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.7rem;
        }

        /* Form Styles */
        .form-container {
            background-color: var(--white-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 119, 204, 0.2);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border-radius: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: var(--gray-color);
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .current-image {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            border: 1px solid var(--light-gray);
        }

        /* Mobile Responsive */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 1001;
            transition: var(--transition);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .menu-toggle.active {
                left: 270px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Avantaj Tedarik</h2>
                <p>Yönetim Paneli</p>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Kontrol Paneli</span>
                </a>
                <a href="products.php" class="menu-item active">
                    <i class="fas fa-box"></i>
                    <span>Ürünler</span>
                </a>
                <a href="categories.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Kategoriler</span>
                </a>
                <a href="../index.php" class="menu-item">
                    <i class="fas fa-globe"></i>
                    <span>Siteyi Görüntüle</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></div>
                        <div class="user-role">Yönetici</div>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="page-header">
                <h1><?php echo $is_edit ? "Ürün Düzenle" : "Yeni Ürün Ekle"; ?></h1>
                <div class="breadcrumb">
                    <span>Ana Sayfa</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Ürünler</span>
                    <i class="fas fa-chevron-right"></i>
                    <span><?php echo $is_edit ? "Ürün Düzenle" : "Yeni Ürün Ekle"; ?></span>
                </div>
            </div>

            <div class="form-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . ($is_edit ? "?id=" . $id : ""); ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Ürün Adı</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
                    </div>
                    
                    <!-- Fiyat alanı kaldırıldı -->
                    
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category_id" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>">
                            <option value="">Kategori Seçin</option>
                            <?php
                            // Kategorileri al
                            $sql = "SELECT id, name FROM categories ORDER BY name";
                            $result = mysqli_query($conn, $sql);
                            while($row = mysqli_fetch_assoc($result)){
                                echo "<option value='" . $row["id"] . "'" . ($category_id == $row["id"] ? " selected" : "") . ">" . $row["name"] . "</option>";
                            }
                            ?>
                        </select>
                        <span class="invalid-feedback"><?php echo $category_err; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Ürün Resmi</label>
                        <?php if($is_edit && !empty($image)): ?>
                        <div class="current-image">
                            <p>Mevcut Resim:</p>
                            <img src="../uploads/<?php echo file_exists("../uploads/" . $image) ? $image : "default.jpg"; ?>" alt="<?php echo $name; ?>">
                        </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                        <small>Desteklenen formatlar: JPG, JPEG, PNG, GIF. Maksimum boyut: 2MB.</small>
                        <span class="invalid-feedback"><?php echo $image_err; ?></span>
                    </div>
                    
                    <div class="form-actions">
                        <input type="submit" class="btn" value="<?php echo $is_edit ? 'Güncelle' : 'Ekle'; ?>">
                        <a href="products.php" class="btn btn-secondary">İptal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    </script>
</body>
</html>