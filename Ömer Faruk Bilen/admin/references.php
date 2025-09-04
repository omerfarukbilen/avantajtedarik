<?php

require_once 'config.php';

// Kullanıcı girişi kontrolü
if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

// Referansları getir
$sql = "SELECT * FROM `references` ORDER BY id DESC"; // Added backticks around 'references' as it's a reserved word
$result = mysqli_query($conn, $sql);

if (!$result) {
    // Handle query error
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referanslar - Avantaj Tedarik Yönetim Paneli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }

        .page-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.7rem;
        }

        .add-btn {
            padding: 10px 20px;
            background-color: var(--success-color);
            color: var(--white-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }

        .add-btn i {
            margin-right: 8px;
        }

        .add-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
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

        .reference-logo {
            width: 60px;
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
            border: 1px solid rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }

        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
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
    .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding-bottom: 15px;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
    position: relative;
}

.modal-header h2 {
    margin: 0;
    color: var(--dark-color);
    font-size: 1.5rem;
}

.close-button {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 24px;
    font-weight: bold;
    color: #666;
    border: none;
    background: none;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.close-button:hover {
    color: var(--dark-color);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
    font-weight: 500;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: var(--dark-color);
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    color: var(--dark-color);
    background-color: #fff;
    border-color: var(--primary-color);
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
}

.modal-footer {
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    cursor: pointer;
}

.btn-primary {
    color: #fff;
    background-color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border: 1px solid #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.current-logo {
    margin: 1rem 0;
    text-align: center;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}

.current-logo img {
    max-width: 200px;
    height: auto;
    border-radius: 0.25rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            <nav class="sidebar-nav">
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
                <a href="banners.php" class="menu-item">
                    <i class="fas fa-images"></i>
                    <span>Bannerlar</span>
                </a>
                <a href="references.php" class="menu-item active">
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
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Çıkış Yap</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="page-header">
                <div>
                    <h1>Referanslar</h1>
                    <div class="breadcrumb">
                        <span>Ana Sayfa</span>
                        <i class="fas fa-chevron-right"></i>
                        <span>Referanslar</span>
                    </div>
                </div>
                <a href="#" class="add-btn" id="addReferenceBtn">
                    <i class="fas fa-plus"></i> Yeni Referans Ekle
                </a>
            </div>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success_message']); ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_SESSION['error_message']); ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Referans Adı</th>
                            <th>Eklenme Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']); ?></td>
                                <td>
                                    <img src="../uploads/references/<?= htmlspecialchars($row['logo']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" class="reference-logo">
                                </td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="action-btns">
                                    <button class="edit-btn" onclick="openEditModal(<?= htmlspecialchars($row['id']); ?>, '<?= htmlspecialchars($row['name']); ?>', '<?= htmlspecialchars($row['logo']); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="reference_process.php?delete=<?= htmlspecialchars($row['id']); ?>" class="delete-btn" onclick="return confirm('Bu referansı silmek istediğinizden emin misiniz?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Yeni Referans Ekleme Modal -->
    <div class="modal" id="addReferenceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Yeni Referans Ekle</h2>
                <button type="button" class="close-button" onclick="closeAddModal()" aria-label="Kapat">&times;</button>
            </div>
            <form action="reference_process.php" method="post" enctype="multipart/form-data" id="addReferenceForm" onsubmit="return validateForm(this)">
                <div class="form-group">
                    <label for="name">Referans Adı</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="logo">Logo</label>
                    <input type="file" id="logo" name="logo" class="form-control" accept="image/*" required onchange="previewImage(this, 'logoPreview')">
                    <div class="invalid-feedback"></div>
                    <small>İzin verilen formatlar: JPG, JPEG, PNG, GIF (Max. 5MB)</small>
                    <img id="logoPreview" src="#" alt="Logo Önizleme" style="display: none; max-width: 200px; margin-top: 10px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">İptal</button>
                    <button type="submit" name="add_reference" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Referans Düzenleme Modal -->
    <div class="modal" id="editReferenceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Referans Düzenle</h2>
                <button class="close-button" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="reference_process.php" method="post" enctype="multipart/form-data" id="editReferenceForm" onsubmit="return validateForm(this)">
                <input type="hidden" id="editId" name="id">
                <input type="hidden" id="currentLogo" name="current_logo">
                <div class="form-group">
                    <label for="editName">Referans Adı</label>
                    <input type="text" id="editName" name="name" class="form-control" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label>Mevcut Logo</label>
                    <div class="current-logo">
                        <img id="current_logo" src="" alt="Mevcut Logo" style="max-width: 200px;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="editLogo">Yeni Logo</label>
                    <input type="file" id="editLogo" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'editLogoPreview')">
                    <div class="invalid-feedback"></div>
                    <small>Yeni bir logo yüklemek için seçin (İsteğe bağlı)</small>
                    <img id="editLogoPreview" src="#" alt="Logo Önizleme" style="display: none; max-width: 200px; margin-top: 10px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">İptal</button>
                    <button type="submit" name="edit_reference" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function openAddModal() {
            document.getElementById('addReferenceModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeAddModal() {
            document.getElementById('addReferenceModal').style.display = 'none';
            document.getElementById('addReferenceForm').reset();
            document.getElementById('logoPreview').style.display = 'none';
            document.body.style.overflow = 'auto';
            clearErrorMessages();
        }

        function openEditModal(id, name, logo) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('currentLogo').value = logo;
            document.getElementById('current_logo').src = '../uploads/references/' + logo;
            document.getElementById('editReferenceModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editReferenceModal').style.display = 'none';
            document.getElementById('editReferenceForm').reset();
            document.getElementById('editLogoPreview').style.display = 'none';
            document.body.style.overflow = 'auto';
            clearErrorMessages();
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }

        function validateForm(form) {
            clearErrorMessages();
            let isValid = true;

            // Referans Adı Kontrolü
            const nameInput = form.querySelector('input[name="name"]');
            if (!nameInput.value.trim()) {
                showError(nameInput, 'Referans adı boş bırakılamaz.');
                isValid = false;
            }

            // Logo Kontrolü (Sadece yeni referans eklerken zorunlu)
            const logoInput = form.querySelector('input[type="file"]');
            if (form.id === 'addReferenceForm' && !logoInput.files[0]) {
                showError(logoInput, 'Logo seçilmesi zorunludur.');
                isValid = false;
            }

            if (logoInput.files[0]) {
                const file = logoInput.files[0];
                const fileType = file.type;
                const fileSize = file.size / 1024 / 1024; // MB cinsinden

                if (!['image/jpeg', 'image/jpg', 'image/png', 'image/gif'].includes(fileType)) {
                    showError(logoInput, 'Geçersiz dosya formatı. Lütfen JPG, JPEG, PNG veya GIF formatında bir dosya seçin.');
                    isValid = false;
                }

                if (fileSize > 5) {
                    showError(logoInput, 'Dosya boyutu çok büyük. Maksimum 5MB olmalıdır.');
                    isValid = false;
                }
            }

            return isValid;
        }

        function showError(input, message) {
            const errorDiv = input.nextElementSibling;
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            input.classList.add('is-invalid');
        }

        function clearErrorMessages() {
            const errorDivs = document.getElementsByClassName('invalid-feedback');
            const inputs = document.querySelectorAll('.form-control');

            for (let errorDiv of errorDivs) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }

            for (let input of inputs) {
                input.classList.remove('is-invalid');
            }
        }

        // Mobil menü geçişi
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Modal dışına tıklandığında kapatma
        window.addEventListener('click', (event) => {
            const addModal = document.getElementById('addReferenceModal');
            const editModal = document.getElementById('editReferenceModal');

            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        });

        // ESC tuşuna basıldığında modalı kapatma
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const addModal = document.getElementById('addReferenceModal');
                const editModal = document.getElementById('editReferenceModal');

                if (addModal.style.display === 'block') {
                    closeAddModal();
                }
                if (editModal.style.display === 'block') {
                    closeEditModal();
                }
            }
        });

        // Yeni Referans Ekle butonuna tıklama olayı
        document.getElementById('addReferenceBtn').addEventListener('click', function(e) {
            e.preventDefault();
            openAddModal();
        });
    </script>
</body>
</html>