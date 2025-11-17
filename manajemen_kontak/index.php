<?php
session_start();
if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
    }
}
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
// Handle delete contact
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (isset($_SESSION['contacts'][$id])) {
        unset($_SESSION['contacts'][$id]);
    }
    header("Location: index.php");
    exit();
}
$contacts = $_SESSION['contacts'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Manajemen Kontak</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <!-- Login Page -->
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Sistem Manajemen Kontak</h1>
                    <p>Masuk untuk mengelola kontak Anda</p>
                </div>
                <?php if (isset($_POST['login']) && !$_SESSION['logged_in']): ?>
                    <div class="alert alert-error">
                        Username atau password salah!
                    </div>
                <?php endif; ?>
                <form method="POST" class="login-form">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" name="login" class="btn-login">Masuk</button>
                </form>
                <div class="login-footer">
                    <p>Demo: username = <strong>admin</strong>, password = <strong>admin123</strong></p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Dashboard -->
        <div class="dashboard">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-header">
                    <h2>Kontak Manager</h2>
                    <p>Kelola kontak dengan mudah</p>
                </div>
                <nav class="sidebar-nav">
                    <a href="#" class="nav-item active">
                        <span>Dashboard</span>
                    </a>
                    <a href="#contacts" class="nav-item">
                        <span>Daftar Kontak</span>
                    </a>
                </nav>
                <div class="sidebar-footer">
                    <a href="?logout=true" class="logout-btn">
                        <span>Keluar</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Header -->
                <header class="header">
                    <div class="header-left">
                        <h1>Dashboard Kontak</h1>
                        <p>Kelola daftar kontak dengan mudah dan efisien</p>
                    </div>
                    <div class="header-right">
                        <div class="user-info">
                            <div class="user-avatar">A</div>
                            <div class="user-details">
                                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                                <span>Login: <?php echo date('H:i', strtotime($_SESSION['login_time'])); ?></span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-content">
                        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                    </div>
                    <div class="welcome-actions">
                        <a href="add-contact.php" class="btn-primary">
                            Tambah Kontak Baru
                        </a>
                    </div>
                </div>

                <!-- Contacts List -->
                <div id="contacts" class="contacts-section">
                    <div class="section-header">
                        <h2>Daftar Kontak</h2>
                        <div class="section-actions">
                            <a href="add-contact.php" class="btn-secondary">Tambah Kontak</a>
                        </div>
                    </div>

                    <?php if (empty($contacts)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <h3>Belum Ada Kontak</h3>
                            <p>Mulai dengan menambahkan kontak pertama Anda</p>
                            <a href="add-contact.php" class="btn-primary">Tambah Kontak Pertama</a>
                        </div>
                    <?php else: ?>
                        <div class="contacts-table">
                            <div class="table-header">
                                <div class="col-name">Nama</div>
                                <div class="col-phone">Telepon</div>
                                <div class="col-email">Email</div>
                                <div class="col-actions">Aksi</div>
                            </div>
                            <div class="table-body">
                                <?php foreach ($contacts as $id => $contact): ?>
                                    <div class="table-row">
                                        <div class="col-name">
                                            <div class="contact-avatar">
                                                <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                                            </div>
                                            <span><?php echo htmlspecialchars($contact['name']); ?></span>
                                        </div>
                                        <div class="col-phone">
                                            <?php echo htmlspecialchars($contact['phone']); ?>
                                        </div>
                                        <div class="col-email">
                                            <?php echo htmlspecialchars($contact['email']); ?>
                                        </div>
                                        <div class="col-actions">
                                            <a href="edit-contact.php?id=<?php echo $id; ?>" class="btn-action btn-edit">Edit</a>
                                            <a href="?delete=<?php echo $id; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus kontak ini?')">Hapus</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>