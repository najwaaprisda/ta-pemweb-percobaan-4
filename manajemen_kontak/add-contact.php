<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

$errors = [];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi nama
    if (empty($_POST["name"])) {
        $errors[] = "Nama harus diisi";
    } else {
        $data['name'] = trim($_POST["name"]);
        if (!preg_match("/^[a-zA-Z\s]+$/", $data['name'])) {
            $errors[] = "Nama hanya boleh mengandung huruf dan spasi";
        }
    }

    // Validasi email
    if (!empty($_POST["email"])) {
        $data['email'] = trim($_POST["email"]);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }
    }
    if (empty($_POST["phone"])) {
        $errors[] = "Nomor telepon harus diisi";
    } else {
        $data['phone'] = trim($_POST["phone"]);

        $clean_phone = preg_replace('/[^0-9]/', '', $data['phone']);
        
        if (strlen($clean_phone) !== 13) {
            $errors[] = "Nomor telepon harus tepat 13 digit angka";
        } else if (!preg_match("/^[0-9]{13}$/", $clean_phone)) {
            $errors[] = "Format nomor telepon tidak valid";
        } else {
            $data['phone'] = $clean_phone;
        }
    }
    // Alamat (opsional)
    $data['address'] = trim($_POST["address"] ?? '');

    // Jika tidak ada error, simpan kontak
    if (empty($errors)) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = uniqid();
        $_SESSION['contacts'][$id] = $data;
        
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kontak - Kontak Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Kontak Manager</h2>
                <p>Kelola kontak dengan mudah</p>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item">
                    <span>Dashboard</span>
                </a>
                <a href="index.php#contacts" class="nav-item">
                    <span>Daftar Kontak</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="index.php?logout=true" class="logout-btn">
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h1>Tambah Kontak Baru</h1>
                    <p>Isi form berikut untuk menambahkan kontak baru</p>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <div class="user-avatar">A</div>
                        <div class="user-details">
                            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <span>Online</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Add Contact Form -->
            <div class="form-page-container">
                <div class="form-page-header">
                    <h2>Form Tambah Kontak</h2>
                    <a href="index.php" class="btn-back">Kembali ke Dashboard</a>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <h3>Terdapat kesalahan:</h3>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <form method="POST" class="contact-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name" class="form-label">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" 
                                       value="<?php echo isset($data['name']) ? htmlspecialchars($data['name']) : ''; ?>" 
                                       placeholder="Masukkan nama lengkap" 
                                       class="form-input"
                                       required>
                                <div class="form-hint">Contoh: Najwa Aprisda</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Nomor Telepon *</label>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo isset($data['phone']) ? htmlspecialchars($data['phone']) : ''; ?>" 
                                       placeholder="Contoh: 0812345678901" 
                                       class="form-input"
                                       pattern="[0-9]{13}"
                                       title="Nomor telepon harus tepat 13 digit angka"
                                       maxlength="13"
                                       required>
                                <div class="form-hint">Harus tepat 13 digit angka (contoh: 0812345678901)</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" 
                                       placeholder="nama@email.com"
                                       class="form-input">
                                <div class="form-hint">Opsional</div>
                            </div>

                            <div class="form-group full-width">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea id="address" name="address" rows="4" 
                                          placeholder="Masukkan alamat lengkap"
                                          class="form-textarea"><?php echo isset($data['address']) ? htmlspecialchars($data['address']) : ''; ?></textarea>
                                <div class="form-hint">Opsional</div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                Simpan Kontak
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validasi client-side untuk nomor telepon
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }

            const hint = this.parentElement.querySelector('.form-hint');
            const remaining = 13 - this.value.length;
            
            if (this.value.length === 13) {
                hint.innerHTML = '✓ Nomor telepon valid (13 digit)';
                hint.style.color = 'var(--success)';
            } else {
                hint.innerHTML = `Harus tepat 13 digit angka (sisa: ${remaining} digit)`;
                hint.style.color = 'var(--text-light)';
            }
        });
    </script>
</body>
</html>