<?php
// Yapılandırma dosyasını dahil et
require_once "config.php";

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
redirectToLogin();

// Kategori silme işlemi
if(isset($_GET["delete"]) && !empty($_GET["delete"])){
    $id = sanitize($_GET["delete"]);
    
    // Önce bu kategoriye ait ürünleri kontrol et
    $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_assoc($check_result);
    
    if($check_row["count"] > 0){
        // Bu kategoriye ait ürünler var, silme işlemi yapılmayacak
        header("location: categories.php?error=has_products");
        exit();
    } else {
        // Kategoriyi sil
        $sql = "DELETE FROM categories WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $id);
            if(mysqli_stmt_execute($stmt)){
                header("location: categories.php?success=deleted");
                exit();
            } else{
                header("location: categories.php?error=delete_failed");
                exit();
            }
        }
    }
}

// Başarı ve hata mesajlarını kontrol et
$success_message = $error_message = "";

if(isset($_GET["success"])){
    switch($_GET["success"]){
        case "added":
            $success_message = "Kategori başarıyla eklendi.";
            break;
        case "updated":
            $success_message = "Kategori başarıyla güncellendi.";
            break;
        case "deleted":
            $success_message = "Kategori başarıyla silindi.";
            break;
    }
}

if(isset($_GET["error"])){
    switch($_GET["error"]){
        case "has_products":
            $error_message = "Bu kategoriye ait ürünler bulunduğu için silinemez.";
            break;
        case "delete_failed":
            $error_message = "Kategori silinirken bir hata oluştu.";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoriler - Avantaj Tedarik Yönetim Paneli</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .page-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-sm {
            padding: 5px 15px;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: var(--dark-color);
        }

        .btn-warning:hover {
            background-color: #e0a800;
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

        .table .actions {
            display: flex;
            gap: 10px;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            z-index: 1050;
            overflow: auto;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: var(--white-color);
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            padding: 30px;
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
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

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 600px;
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
                <a href="categories.php" class="menu-item active">
                    <i class="fas fa-tags"></i>
                    <span>Kategoriler</span>
                </a>
                <a href="brands.php" class="menu-item">
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
                <h1>Kategoriler</h1>
                <div class="page-actions">
                    <button class="btn btn-primary" id="addCategoryBtn">
                        <i class="fas fa-plus"></i> Yeni Kategori Ekle
                    </button>
                </div>
            </div>

            <?php if(!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if(!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategori Adı</th>
                            <th>Ürün Sayısı</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Kategorileri ve ürün sayılarını al
                        $sql = "SELECT c.id, c.name, COUNT(p.id) as product_count 
                                FROM categories c 
                                LEFT JOIN products p ON c.id = p.category_id 
                                GROUP BY c.id 
                                ORDER BY c.name";
                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                echo "<td>" . $row["product_count"] . "</td>";
                                echo "<td class='actions'>";
                                echo "<button class='btn btn-sm btn-warning edit-category' data-id='" . $row["id"] . "' data-name='" . htmlspecialchars($row["name"]) . "'><i class='fas fa-edit'></i> Düzenle</button>";
                                echo "<a href='categories.php?delete=" . $row["id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu kategoriyi silmek istediğinizden emin misiniz?\");'><i class='fas fa-trash'></i> Sil</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align: center;'>Henüz kategori bulunmuyor.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Category Modal -->
    <div class="modal" id="categoryModal">
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            <div class="modal-header">
                <h2 id="modalTitle">Yeni Kategori Ekle</h2>
            </div>
            <form id="categoryForm" method="post" action="category_process.php">
                <input type="hidden" name="id" id="categoryId">
                <div class="form-group">
                    <label for="categoryName">Kategori Adı</label>
                    <input type="text" class="form-control" id="categoryName" name="name" required>
                    <div class="invalid-feedback" id="nameError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Modal functionality
        const modal = document.getElementById('categoryModal');
        const addCategoryBtn = document.getElementById('addCategoryBtn');
        const modalClose = document.querySelector('.modal-close');
        const cancelBtn = document.getElementById('cancelBtn');
        const categoryForm = document.getElementById('categoryForm');
        const modalTitle = document.getElementById('modalTitle');
        const categoryId = document.getElementById('categoryId');
        const categoryName = document.getElementById('categoryName');

        // Open modal for adding new category
        addCategoryBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Yeni Kategori Ekle';
            categoryId.value = '';
            categoryName.value = '';
            modal.classList.add('active');
        });

        // Close modal
        modalClose.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        cancelBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        // Edit category
        const editButtons = document.querySelectorAll('.edit-category');
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                
                modalTitle.textContent = 'Kategori Düzenle';
                categoryId.value = id;
                categoryName.value = name;
                modal.classList.add('active');
            });
        });
    </script>
</body>
</html>