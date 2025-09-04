<?php
require_once "config.php";

// Oturum kontrolü
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Marka silme işlemi
if (isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $id = $_GET["delete"];
    
    // Önce marka logosunu sil
    $sql = "SELECT logo FROM brands WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $logo = $row["logo"];
        if ($logo != "default_logo.jpg") {
            $logo_path = "../uploads/" . $logo;
            if (file_exists($logo_path)) {
                unlink($logo_path);
            }
        }
    }
    
    // Markayı sil
    $sql = "DELETE FROM brands WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["success_message"] = "Marka başarıyla silindi.";
    } else {
        $_SESSION["error_message"] = "Marka silinirken bir hata oluştu.";
    }
    
    header("location: brands.php");
    exit;
}

// Başarı mesajı kontrolü
$success_message = "";
if (isset($_SESSION["success_message"])) {
    $success_message = $_SESSION["success_message"];
    unset($_SESSION["success_message"]);
}

// Hata mesajı kontrolü
$error_message = "";
if (isset($_SESSION["error_message"])) {
    $error_message = $_SESSION["error_message"];
    unset($_SESSION["error_message"]);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markalar - Avantaj Tedarik Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --transition: all 0.3s ease;
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
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .sidebar-nav {
            padding: 20px 0;
            flex-grow: 1;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--white-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
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
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            color: var(--white-color);
            text-decoration: none;
            font-size: 0.9rem;
            padding: 8px 0;
        }
        
        .logout-btn i {
            margin-right: 8px;
        }
        
        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }
        
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--dark-color);
            font-size: 1.5rem;
            cursor: pointer;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 101;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--gray-color);
        }
        
        .breadcrumb i {
            margin: 0 8px;
            font-size: 0.7rem;
        }
        
        .add-button {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .add-button:hover {
            background-color: var(--secondary-color);
        }
        
        .add-button i {
            margin-right: 8px;
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: var(--white-color);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
        }
        
        .data-table th {
            background-color: var(--primary-color);
            color: var(--white-color);
            font-weight: 600;
        }
        
        .data-table tr:nth-child(even) {
            background-color: var(--light-color);
        }
        
        .data-table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .edit-btn, .delete-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }
        
        .edit-btn {
            background-color: var(--warning-color);
            color: var(--dark-color);
        }
        
        .delete-btn {
            background-color: var(--danger-color);
            color: var(--white-color);
        }
        
        .edit-btn i, .delete-btn i {
            margin-right: 5px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
            padding: 20px;
        }
        
        .modal-content {
            background-color: var(--white-color);
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .modal-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .close-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-color);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--light-gray);
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white-color);
        }
        
        .btn-secondary {
            background-color: var(--gray-color);
            color: var(--white-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--dark-color);
        }
        
        /* Logo Preview */
        .logo-preview {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border: 1px solid var(--light-gray);
            border-radius: 4px;
            margin-top: 10px;
        }
        
        .current-logo {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .current-logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-right: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 4px;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .breadcrumb {
                margin-top: 10px;
            }
            
            .add-button {
                margin-top: 10px;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
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
                <a href="brands.php" class="menu-item active">
                    <i class="fas fa-copyright"></i>
                    <span>Markalar</span>
                </a>
                <a href="banners.php" class="menu-item">
                    <i class="fas fa-images"></i>
                    <span>Bannerlar</span>
                </a>
                <a href="references.php" class="menu-item ">
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
                <h1>Markalar</h1>
                <div class="breadcrumb">
                    <span>Ana Sayfa</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Markalar</span>
                </div>
            </div>

            <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Marka Listesi</h2>
                <button class="add-button" onclick="openModal()">
                    <i class="fas fa-plus"></i> Yeni Marka Ekle
                </button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Marka Adı</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Markaları getir
                    $sql = "SELECT * FROM brands ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td><img src='../uploads/" . $row["logo"] . "' alt='" . $row["name"] . "' style='width: 50px; height: 50px; object-fit: contain;'></td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='openEditModal(" . $row["id"] . ", \"" . $row["name"] . "\", \"" . $row["logo"] . "\")'>";
                            echo "<i class='fas fa-edit'></i> Düzenle";
                            echo "</button>";
                            echo "<a href='brands.php?delete=" . $row["id"] . "' class='delete-btn' onclick='return confirm(\"Bu markayı silmek istediğinizden emin misiniz?\")'>";
                            echo "<i class='fas fa-trash'></i> Sil";
                            echo "</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>Henüz marka eklenmemiş.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Add Brand Modal -->
    <div id="addBrandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Yeni Marka Ekle</h2>
                <button class="close-button" onclick="closeModal()">&times;</button>
            </div>
            <form action="brand_process.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Marka Adı</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="logo">Marka Logosu</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*" onchange="previewImage(this)">
                    <img id="logoPreview" class="logo-preview" src="#" alt="Logo Önizleme" style="display: none;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">İptal</button>
                    <button type="submit" class="btn btn-primary" name="add_brand">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div id="editBrandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Marka Düzenle</h2>
                <button class="close-button" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="brand_process.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_name">Marka Adı</label>
                    <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label>Mevcut Logo</label>
                    <div class="current-logo">
                        <img id="current_logo" src="#" alt="Mevcut Logo">
                        <span id="current_logo_name"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_logo">Yeni Logo (Değiştirmek istemiyorsanız boş bırakın)</label>
                    <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*" onchange="previewEditImage(this)">
                    <img id="editLogoPreview" class="logo-preview" src="#" alt="Logo Önizleme" style="display: none;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">İptal</button>
                    <button type="submit" class="btn btn-primary" name="edit_brand">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal işlemleri
        function openModal() {
            document.getElementById("addBrandModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("addBrandModal").style.display = "none";
            document.getElementById("logoPreview").style.display = "none";
            document.getElementById("logo").value = "";
        }

        function openEditModal(id, name, logo) {
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_name").value = name;
            document.getElementById("current_logo").src = "../uploads/" + logo;
            document.getElementById("current_logo_name").textContent = logo;
            document.getElementById("editBrandModal").style.display = "block";
        }

        function closeEditModal() {
            document.getElementById("editBrandModal").style.display = "none";
            document.getElementById("editLogoPreview").style.display = "none";
            document.getElementById("edit_logo").value = "";
        }

        // Logo önizleme
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("logoPreview").src = e.target.result;
                    document.getElementById("logoPreview").style.display = "block";
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewEditImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("editLogoPreview").src = e.target.result;
                    document.getElementById("editLogoPreview").style.display = "block";
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Mobil menü toggle
        document.querySelector(".menu-toggle").addEventListener("click", function() {
            document.querySelector(".sidebar").classList.toggle("active");
        });

        // Sayfa yüklendiğinde modal kapalı olsun
        window.onload = function() {
            closeModal();
            closeEditModal();
        }
    </script>
</body>
</html>