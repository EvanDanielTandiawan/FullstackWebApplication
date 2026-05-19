<?php
session_start();
require_once '../../Database.php';
$dosenModel = new DosenModel();
$accountModel = new AccountModel();

if (!isset($_GET['npk'])) {
    die("NPK tidak ditemukan.");
}

$npk = $_GET['npk'];
$dosen = $dosenModel->getDosenByNPK($npk);

if (!$dosen) {
    die("Data dosen tidak ditemukan.");
}

$akun = $accountModel->getAccountByDosenNPK($npk);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $nama_file_baru = strtolower($nama);
    $ext_lama = strtolower($dosen['foto_extension']);
    $foto_baru = $_FILES['foto'];
    $ext = pathinfo($foto_baru['name'], PATHINFO_EXTENSION);
    $nama_file_lama = strtolower($dosen['nama']);
    $path_lama = "../../foto_dosen/" . $nama_file_lama . "_" . $dosen['npk'] . "." . $ext_lama;
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!empty($foto_baru['name']) && in_array($ext, $allowed)) {
        $path_baru = "../../foto_dosen/" . $nama_file_baru . "_" . $npk . "." . $ext;

        if (file_exists($path_lama)) {
            unlink($path_lama);
        }

        move_uploaded_file($foto_baru['tmp_name'], $path_baru);

        $dosenModel->updateDosen($npk, $nama, $ext);
    } else {
        if (file_exists($path_lama)) {
            $path_rename = "../../foto_dosen/" . $nama_file_baru . "_" . $npk . "." . $ext_lama;
            rename($path_lama, $path_rename);
        }

        $dosenModel->updateDosenNameOnly($npk, $nama);
    }

    if ($akun) {
        $accountModel->updateAccountDosen($username, $password, $npk);
    }

    header("Location: admin_tabeldosen.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Edit Dosen</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <header>
        <h2>Dashboard Admin</h2>
        <nav>
            <a href="admin_tabeldosen.php">Data Dosen</a>
            <a href="../admin_home.php">Home</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="box box-medium">
            <a href="admin_tabeldosen.php" class="link-back">
                &leftarrow; Kembali ke Data Dosen
            </a>

            <h2 class="form-title">Edit Data Dosen</h2>

            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 15px;">
                    <label>NPK</label>
                    <input type="text" name="npk" value="<?php echo htmlspecialchars($dosen['npk']); ?>" readonly style="background-color: var(--bg-body); cursor: not-allowed;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($dosen['nama']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Username Akun</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($akun['username']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Password Akun</label>
                    <input type="text" name="password" placeholder="Isi untuk mengubah password baru..." value="<?php echo isset($akun['password']) ? htmlspecialchars($akun['password']) : ''; ?>" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Foto Profil</label>
                    
                    <div class="current-photo-container">
                        <?php
                        if (!empty($dosen['foto_extension'])) {
                            $foto_path = "../../foto_dosen/" . $dosen['nama'] . "_" . $dosen['npk'] . "." . $dosen['foto_extension'];
                            if (file_exists($foto_path)) {
                                echo '<img src="' . $foto_path . '?' . time() . '" class="img-thumbnail" width="150" alt="Foto Dosen">';
                                echo '<p>Foto saat ini</p>';
                            } else {
                                echo '<p class="error-msg" style="display:inline-block; margin:0;">File foto fisik tidak ditemukan.</p>';
                            }
                        } else {
                            echo '<p>Tidak ada foto yang tersimpan.</p>';
                        }
                        ?>
                    </div>

                    <input type="file" name="foto" accept="image/*">
                    <p style="font-size: 0.85em; color: var(--text-muted); margin-top: 5px;">*Biarkan kosong jika tidak ingin mengganti foto.</p>
                </div>

                <button type="submit" class="btn btn-green btn-block">Simpan Perubahan</button>
            </form>
        </div>
    </main>

</body>
</html>