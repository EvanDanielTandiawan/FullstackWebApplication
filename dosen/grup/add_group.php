<?php
session_start();
require_once '../../Database.php';

// Cek apakah user sudah login dan role-nya dosen
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../../login.php");
    exit;
}

$grupModel = new GrupModel();
$message = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $jenis = $_POST['jenis'];
    $username_pembuat = $_SESSION['username'];

    // Validasi sederhana
    if (empty($nama) || empty($deskripsi) || empty($jenis)) {
        $error_msg = "Semua field harus diisi.";
    } else {
        // INSERT
        $newGroupId = $grupModel->createGrup($username_pembuat, $nama, $deskripsi, $jenis);

        if ($newGroupId) {
            // Tambahkan pembuat sebagai member
            $addMemberSuccess = $grupModel->addMember($newGroupId, $username_pembuat);

            if ($addMemberSuccess) {
                // Redirect jika sukses
                header("Location: detail_group.php?idgrup=$newGroupId&success=1");
                exit;
            } else {
                $error_msg = "Grup berhasil dibuat, namun gagal menambahkan Anda sebagai member.";
            }
        } else {
            $error_msg = "Gagal membuat grup. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Grup Baru</title>
    <link rel="stylesheet" href="../../css/style.css"> 
</head>

<body>

    <header>
        <h2>Dashboard Dosen</h2>
        <nav>
            <a href="manage_group_dosen.php">Kelola Grup</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="box box-medium">
            
            <a href="manage_group_dosen.php" class="link-back">
                &leftarrow; Kembali ke Daftar Grup
            </a>

            <h2 class="form-title">Buat Grup Baru</h2>

            <?php if (!empty($error_msg)): ?>
                <div class="error-msg">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div style="margin-bottom: 15px;">
                    <label for="nama">Nama Grup</label>
                    <input type="text" id="nama" name="nama" placeholder="Contoh: Pemrograman Web A" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan tujuan grup ini..." required></textarea>
                </div>

                <div style="margin-bottom: 25px;">
                    <label for="jenis">Jenis Grup</label>
                    <select id="jenis" name="jenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Privat">Privat (Hanya undangan)</option>
                        <option value="Publik">Publik (Semua bisa gabung)</option>
                    </select>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Buat Grup</button>
            </form>
        </div>
    </main>

</body>
</html>