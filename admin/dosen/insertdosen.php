<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Tambah Akun Dosen</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <header>
        <h2>Dashboard Admin</h2>
        <nav>
            <a href="admin_tabeldosen.php">Data Dosen</a>
            <a href="../../admin/admin_home.php">Home</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="box box-medium">
            <a href="admin_tabeldosen.php" class="link-back">
                &leftarrow; Kembali ke Data Dosen
            </a>

            <h2 class="form-title">Tambah Akun Dosen Baru</h2>

            <?php if (isset($_SESSION["error_message"])): ?>
                <div class="alert-box alert-error">
                    <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="insertdosen_proses.php">
                
                <div style="margin-bottom: 15px;">
                    <label for="npk">NPK Dosen</label>
                    <input type="text" id="npk" name="npk_dosen" required placeholder="Masukkan NPK...">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Username untuk login...">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Password...">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Nama lengkap dosen...">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="foto">Foto Profil</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg, image/png, image/gif" required>
                    <p style="font-size: 0.85em; color: var(--text-muted); margin-top: 5px;">Format: JPG, PNG, GIF</p>
                </div>

                <input type="submit" name="submit" value="Insert Data Dosen" class="btn btn-green btn-block">
            </form>

        </div>
    </main>
</body>
</html>