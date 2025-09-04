<?php
// Yapılandırma dosyasını dahil et
require_once "config.php";

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
redirectToLogin();

// Banner silme işlemi
if(isset($_GET["delete"]) && !empty($_GET["delete"])){
    $id = sanitize($_GET["delete"]);
    
    // Önce banner resmini kontrol et ve sil
    $sql = "SELECT image FROM banners WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt, $image);
                if(mysqli_stmt_fetch($stmt)){
                    // Eğer dosya varsa sil
                    if(file_exists("../uploads/banners/" . $image)){
                        unlink("../uploads/banners/" . $image);
                    }
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Banner'ı veritabanından sil
    $sql = "DELETE FROM banners WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            header("location: banners.php?success=deleted");
            exit();
        } else{
            echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Başarı mesajları için değişken
$success_message = "";
if(isset($_GET["success"])){
    if($_GET["success"] == "added"){
        $success_message = "Banner başarıyla eklendi.";
    } else if($_GET["success"] == "updated"){
        $success_message = "Banner başarıyla güncellendi.";
    } else if($_GET["success"] == "deleted"){
        $success_message = "Banner başarıyla silindi.";
    }
}

// Banners tablosunu oluştur (eğer yoksa)
$sql = "CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $sql)) {
    echo "Tablo oluşturma hatası: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bannerlar - Avantaj Tedarik Yönetim Paneli</title>
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

        .user-name {
            font-weight: 600;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
        }

        .logout-btn:hover {
            color: var(--white-color);
        }

        .logout-btn i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
            transition: var(--transition);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--dark-color);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--gray-color);
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.7rem;
        }

        .add-btn {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .add-btn i {
            margin-right: 8px;
        }

        .add-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Table Styles */
        .table-container {
            background-color: var(--white-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        .table th {
            background-color: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .banner-image {
            width: 100px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .edit-btn, .delete-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .edit-btn {
            background-color: var(--warning-color);
            color: var(--dark-color);
        }

        .edit-btn:hover {
            background-color: #e0a800;
        }

        .delete-btn {
            background-color: var(--danger-color);
            color: var(--white-color);
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background-color: var(--white-color);
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-20px);
            transition: var(--transition);
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 1.5rem;
            color: var(--dark-color);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-color);
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--dark-color);
        }

        .modal-body {
            padding: 20px;
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

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .form-check-input {
            margin-right: 10px;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid var(--light-gray);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: var(--light-gray);
            color: var(--dark-color);
        }

        .btn-secondary:hover {
            background-color: var(--gray-color);
            color: var(--white-color);
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

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .menu-toggle.active {
                left: 270px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .add-btn {
                margin-top: 15px;
            }
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 700px;
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
                <a href="products.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Ürünler</span>
                </a>
                <a href="categories.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Kategoriler</span>
                </a>
                <a href="brands.php" class="menu-item">
                    <i class="fas fa-copyright"></i>
                    <span>Markalar</span>
                </a>
                <a href="banners.php" class="menu-item active">
                    <i class="fas fa-images"></i>
                    <span>Bannerlar</span>
                </a>
                <a href="references.php" class="menu-item">
                    <i class="fas fa-handshake"></i>
                    <span>Referanslar</span>
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Ayarlar</span>
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
                <h1>Bannerlar</h1>
                <div class="breadcrumb">
                    <span>Ana Sayfa</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Bannerlar</span>
                </div>
            </div>

            <?php if(!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <div class="page-actions">
                <a href="#" class="add-btn" id="addBannerBtn">
                    <i class="fas fa-plus"></i> Yeni Banner Ekle
                </a>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Resim</th>
                            <th>Başlık</th>
                            <th>Açıklama</th>
                            <th>Link</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Bannerları getir
                        $sql = "SELECT * FROM banners ORDER BY id DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $image_path = "../uploads/banners/" . $row["image"];
                                $status = $row["status"] == 1 ? "<span style='color: var(--success-color);'>Aktif</span>" : "<span style='color: var(--danger-color);'>Pasif</span>";
                                
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td><img src='" . $image_path . "' alt='" . htmlspecialchars($row["title"]) . "' class='banner-image'></td>";
                                echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
                                echo "<td>" . htmlspecialchars(substr($row["description"], 0, 50)) . (strlen($row["description"]) > 50 ? "..." : "") . "</td>";
                                echo "<td>" . htmlspecialchars($row["link"]) . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "<td class='action-btns'>";
                                echo "<a href='#' class='edit-btn edit-banner' data-id='" . $row["id"] . "'><i class='fas fa-edit'></i></a>";
                                echo "<a href='banners.php?delete=" . $row["id"] . "' class='delete-btn' onclick='return confirm(\"Bu banner'ı silmek istediğinizden emin misiniz?\");'><i class='fas fa-trash'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align: center;'>Henüz banner eklenmemiş.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Banner Modal -->
    <div class="modal-overlay" id="addBannerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Yeni Banner Ekle</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form action="banner_process.php" method="post" enctype="multipart/form-data" id="addBannerForm">
                    <div class="form-group">
                        <label for="title">Başlık</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Banner Resmi</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                        <small class="text-muted">Önerilen boyut: 1200x400 piksel</small>
                    </div>
                    <div class="form-group">
                        <label for="link">Link (Opsiyonel)</label>
                        <input type="text" class="form-control" id="link" name="link">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" value="1" checked>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="add">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">İptal</button>
                <button type="submit" form="addBannerForm" class="btn btn-primary">Kaydet</button>
            </div>
        </div>
    </div>

    <!-- Edit Banner Modal -->
    <div class="modal-overlay" id="editBannerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Banner Düzenle</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form action="banner_process.php" method="post" enctype="multipart/form-data" id="editBannerForm">
                    <div class="form-group">
                        <label for="edit_title">Başlık</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Açıklama</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_image">Banner Resmi</label>
                        <input type="file" class="form-control" id="edit_image" name="image">
                        <small class="text-muted">Önerilen boyut: 1200x400 piksel. Boş bırakırsanız mevcut resim korunacaktır.</small>
                        <div id="current_image_container" style="margin-top: 10px;">
                            <p>Mevcut Resim:</p>
                            <img id="current_image" src="" alt="Mevcut Banner" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_link">Link (Opsiyonel)</label>
                        <input type="text" class="form-control" id="edit_link" name="link">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_status" name="status" value="1">
                            <label class="form-check-label" for="edit_status">Aktif</label>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="action" value="edit">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">İptal</button>
                <button type="submit" form="editBannerForm" class="btn btn-primary">Güncelle</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    this.classList.toggle('active');
                });
            }

            // Modal functionality
            const addBannerBtn = document.getElementById('addBannerBtn');
            const addBannerModal = document.getElementById('addBannerModal');
            const editBannerModal = document.getElementById('editBannerModal');
            const modalCloseBtns = document.querySelectorAll('.modal-close, .modal-close-btn');
            const editBannerBtns = document.querySelectorAll('.edit-banner');

            // Open add banner modal
            if (addBannerBtn && addBannerModal) {
                addBannerBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addBannerModal.classList.add('active');
                });
            }

            // Close modals
            modalCloseBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (addBannerModal) addBannerModal.classList.remove('active');
                    if (editBannerModal) editBannerModal.classList.remove('active');
                });
            });

            // Edit banner functionality
            editBannerBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bannerId = this.getAttribute('data-id');
                    
                    // AJAX request to get banner data
                    fetch('banner_process.php?action=get&id=' + bannerId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('edit_id').value = data.banner.id;
                                document.getElementById('edit_title').value = data.banner.title;
                                document.getElementById('edit_description').value = data.banner.description;
                                document.getElementById('edit_link').value = data.banner.link;
                                document.getElementById('edit_status').checked = data.banner.status == 1;
                                document.getElementById('current_image').src = '../uploads/banners/' + data.banner.image;
                                
                                editBannerModal.classList.add('active');
                            } else {
                                alert('Banner bilgileri alınamadı.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Bir hata oluştu.');
                        });
                });
            });
        });
    </script>
</body>
</html>