<?php
session_start();
require_once '../../Database.php';
$mahasiswaModel = new MahasiswaModel();
$accountModel = new AccountModel();

if (!isset($_GET['nrp'])) {
    die("NRP tidak ditemukan.");
}

$original_nrp = $_GET['nrp'];
$mahasiswa = $mahasiswaModel->getMahasiswaWithAccount($original_nrp);

if (!$mahasiswa) {
    die("Data mahasiswa tidak ditemukan.");
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nrp = $_POST['nrp'];
    $nama = strtolower($_POST['nama']);
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $foto_baru = $_FILES['foto'];
    $ext_lama = $mahasiswa['foto_extention'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if ($nama == '') $errors[] = "Nama wajib diisi.";
    if ($gender == '') $errors[] = "Gender wajib dipilih.";
    if ($tanggal_lahir == '') $errors[] = "Tanggal lahir wajib diisi.";
    if ($angkatan == '') $errors[] = "Angkatan wajib diisi.";
    if ($username == '') $errors[] = "Username wajib diisi.";
    if ($password == '') $errors[] = "Password wajib diisi.";

    if (count($errors) == 0) {
        $foto_ext_to_store = $ext_lama;
        $old_path = "../../foto_mahasiswa/" . $mahasiswa['nrp'] . "." . $ext_lama;
        $new_path = "../../foto_mahasiswa/" . $nrp . "." . $ext_lama;

        if (!empty($foto_baru['name'])) {
            $ext_baru = strtolower(pathinfo($foto_baru['name'], PATHINFO_EXTENSION));
            if (!in_array($ext_baru, $allowed)) {
                $errors[] = "Format foto tidak valid. Gunakan JPG, JPEG, PNG, atau GIF.";
            } else {
                if (file_exists($old_path)) unlink($old_path);
                move_uploaded_file($foto_baru['tmp_name'], "../../foto_mahasiswa/" . $nrp . "." . $ext_baru);
                $foto_ext_to_store = $ext_baru;
            }
        } else {
            if ($nrp !== $original_nrp && file_exists($old_path)) {
                rename($old_path, $new_path);
            }
        }

        $ok1 = $mahasiswaModel->updateMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_ext_to_store, $original_nrp);
        $akunExist = $accountModel->getAccountByMahasiswaNRP($original_nrp);

        if ($akunExist) {
            $ok2 = $accountModel->updateAccountMahasiswa($username, $password, $nrp, $original_nrp);
        } else {
            $ok2 = $accountModel->createAccountMahasiswa($username, $password, $nrp);
        }

        if ($ok1 && $ok2) {
            $success = true;
            header("Location: admin_tabelmahasiswa.php");
            exit();
        } else {
            $errors[] = "Gagal update data mahasiswa atau akun.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <header>
        <h2>Dashboard Admin</h2>
        <nav>
            <a href="admin_tabelmahasiswa.php">Data Mahasiswa</a>
            <a href="admin_home.php">Home</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="box box-medium">
            <a href="admin_tabelmahasiswa.php" class="link-back">
                &leftarrow; Kembali ke Data Mahasiswa
            </a>

            <h2 class="form-title">Edit Data Mahasiswa</h2>

            <?php if (count($errors) > 0): ?>
                <div class="alert-box alert-error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert-box alert-success">Data berhasil diperbarui.</div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                
                <div style="margin-bottom: 15px;">
                    <label>NRP (9 digit)</label>
                    <input type="text" name="nrp" value="<?php echo htmlspecialchars($mahasiswa['nrp']); ?>" maxlength="9" readonly style="background-color: var(--bg-body); cursor: not-allowed;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Nama</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($mahasiswa['nama']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">-- Pilih --</option>
                        <option value="Pria" <?php if ($mahasiswa['gender'] == 'Pria') echo 'selected'; ?>>Pria</option>
                        <option value="Wanita" <?php if ($mahasiswa['gender'] == 'Wanita') echo 'selected'; ?>>Wanita</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($mahasiswa['tanggal_lahir']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Angkatan</label>
                    <input type="text" name="angkatan" value="<?php echo htmlspecialchars($mahasiswa['angkatan']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Username Akun</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($mahasiswa['username']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Password Akun</label>
                    <input type="text" name="password" value="<?php echo htmlspecialchars($mahasiswa['password']); ?>" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Foto Profil</label>
                    
                    <div class="current-photo-container">
                        <?php if (!empty($mahasiswa['foto_extention'])): ?>
                            <?php $foto_url = "../../foto_mahasiswa/" . $mahasiswa['nrp'] . "." . $mahasiswa['foto_extention']; ?>
                            <img src="<?php echo $foto_url; ?>" class="img-thumbnail" width="150" alt="Foto Mahasiswa">
                            <p>Foto saat ini</p>
                        <?php else: ?>
                            <p>Tidak ada foto yang tersimpan.</p>
                        <?php endif; ?>
                    </div>

                    <input type="file" name="foto" accept="image/*">
                    <p style="font-size: 0.85em; color: var(--text-muted); margin-top: 5px;">*Biarkan kosong jika tidak ingin mengganti foto.</p>
                </div>

                <button type="submit" class="btn btn-block" style="background-color: #3271d6;">Simpan Perubahan</button>
            </form>
        </div>
    </main>
</body>
</html>