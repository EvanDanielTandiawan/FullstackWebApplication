<?php
session_start();
require_once '../../Database.php';
$grupModel = new GrupModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['idgrup']) || !is_numeric($_GET['idgrup'])) {
    header("Location: manage_group_dosen.php");
    exit;
}

$idgrup = (int)$_GET['idgrup'];
$username_dosen = $_SESSION['username'];
$message = "";

$group = $grupModel->getGrupByPembuatAndId($username_dosen, $idgrup);

if (!$group) {
    header("Location: manage_group_dosen.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama       = trim($_POST['nama']);
    $deskripsi  = trim($_POST['deskripsi']);
    $jenis      = $_POST['jenis'];

    if ($grupModel->updateGrup($idgrup, $nama, $deskripsi, $jenis, $username_dosen)) {
        $message = "Grup " . htmlspecialchars($nama) . " berhasil diperbarui! ✅";
        
        $group['nama'] = $nama;
        $group['deskripsi'] = $deskripsi;
        $group['jenis'] = $jenis;
    } else {
        $message = "Gagal memperbarui grup. ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Grup: <?php echo htmlspecialchars($group['nama']); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <header>
        <h2>Edit Grup</h2>
        <nav>
            <a href="manage_group_dosen.php">Kelola Grup</a>
            <a href="detail_group.php?idgrup=<?php echo $idgrup; ?>">Detail Grup</a>
            <a href="../../thread/thread_list.php?idgrup=<?php echo $idgrup; ?>" class="btn-thread">📝 Diskusi</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="box box-medium">
            <a href="manage_group_dosen.php" class="link-back">
                &leftarrow; Kembali ke Daftar Grup
            </a>

            <h2 class="form-title">Ubah Data Grup</h2>
            
            <?php if ($message): ?>
                <div class="alert-box <?php echo strpos($message, 'berhasil') !== false ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="edit_group.php?idgrup=<?php echo $idgrup; ?>">
                <div style="margin-bottom: 15px;">
                    <label for="nama">Nama Grup</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($group['nama']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($group['deskripsi']); ?></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="jenis">Jenis Grup</label>
                    <select id="jenis" name="jenis" required>
                        <option value="Privat" <?php echo $group['jenis'] == 'Privat' ? 'selected' : ''; ?>>Privat</option>
                        <option value="Publik" <?php echo $group['jenis'] == 'Publik' ? 'selected' : ''; ?>>Publik</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-block">Simpan Perubahan</button>
            </form>
            
            <a href="../../thread/thread_list.php?idgrup=<?php echo $idgrup; ?>" class="btn btn-blue btn-block mt-20">
                📝 Akses Thread Diskusi Grup Ini
            </a>
        </div>
    </main>

</body>
</html>