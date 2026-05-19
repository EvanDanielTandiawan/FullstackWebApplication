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
    <title>Insert Data Mahasiswa</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <header>
        <h2>Dashboard Admin</h2>
        <nav>
            <a href="admin_tabelmahasiswa.php">Data Mahasiswa</a>
            <a href="../../admin/admin_home.php">Home</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="box box-medium">
            <a href="admin_tabelmahasiswa.php" class="link-back">
                &leftarrow; Kembali ke Data Mahasiswa
            </a>

            <h2 class="form-title">Form Tambah Data Mahasiswa</h2>

            <?php if (isset($_SESSION["error_message"])): ?>
                <div class="alert-box alert-error">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="insertmahasiswa_proses.php">
                
                <div style="margin-bottom: 15px;">
                    <label>NRP (9 digit)</label>
                    <input type="text" name="nrp" maxlength="9" pattern="\d{9}" required placeholder="Contoh: 160420000">
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Nama Mahasiswa...">
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Gender</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gender" value="Pria" required> Pria
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Wanita" required> Wanita
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Angkatan</label>
                    <input type="number" name="angkatan" required placeholder="Contoh: 2023">
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Username Account</label>
                    <input type="text" name="username" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Password Account</label>
                    <input type="password" name="password" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Foto Profil</label>
                    <input type="file" name="foto" accept="image/jpeg, image/png, image/gif">
                </div>

                <button type="submit" class="btn btn-green btn-block">Simpan Data Mahasiswa</button>
            </form>
        </div>
    </main>
</body>
</html>