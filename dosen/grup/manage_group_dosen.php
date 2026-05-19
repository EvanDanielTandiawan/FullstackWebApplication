<?php
session_start();
require_once '../../Database.php';
$grupModel = new GrupModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../../login.php");
    exit;
}

$username_dosen = $_SESSION['username'];

if (isset($_GET['action']) && $_GET['action'] == 'delete_group' && isset($_GET['idgrup'])) {
    $idgrup_to_delete = (int) $_GET['idgrup'];

    if ($grupModel->isOwner($idgrup_to_delete, $username_dosen)) {
        if ($grupModel->deleteFullGrup($idgrup_to_delete)) {
            header("Location: manage_group_dosen.php?status=sukses");
            exit;
        } else {
            header("Location: manage_group_dosen.php?status=gagal");
            exit;
        }
    } else {
        header("Location: manage_group_dosen.php");
        exit;
    }
}

$grup_list = $grupModel->getGrupByPembuat($username_dosen);

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses') {
        $message = "Grup berhasil dihapus, dan semua anggota serta event terkait telah dibersihkan.";
    } else if ($_GET['status'] == 'gagal') {
        $message = "Gagal menghapus grup.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Grup</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <header>
        <h2>Kelola Grup Saya</h2>
        <nav>
            <a href="../dosen_home.php">Home</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Daftar Grup</h2>
            <a href="create_group.php" class="btn btn-green">+ Buat Grup Baru</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert-box <?php echo ($_GET['status'] == 'sukses') ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="box">
            <div class="table-responsive">
                <?php if (!empty($grup_list)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Grup</th>
                                <th>Deskripsi</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grup_list as $row): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pembentukan'])); ?></td>
                                    <td style="white-space: nowrap;">
                                        <a class="btn btn-blue btn-sm"
                                            href="detail_group.php?idgrup=<?php echo $row['idgrup']; ?>">
                                            Detail
                                        </a>
                                        
                                        <a class="btn btn-green btn-sm" href="edit_group.php?idgrup=<?php echo $row['idgrup']; ?>">
                                            Edit
                                        </a>
                                        
                                        <a class="btn btn-red btn-sm"
                                            href="manage_group_dosen.php?action=delete_group&idgrup=<?php echo $row['idgrup']; ?>"
                                            onclick="return confirm('PERINGATAN! Menghapus grup akan menghapus SEMUA data anggota dan event terkait. Apakah Anda yakin?');">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: 20px;">
                        Anda belum membuat Grup apa pun.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>