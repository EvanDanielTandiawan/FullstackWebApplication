<?php
session_start();
require_once '../../Database.php';
$grupModel = new GrupModel();
$eventModel = new EventModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['idgrup']) || !is_numeric($_GET['idgrup'])) {
    header("Location: manage_group_dosen.php");
    exit;
}

$idgrup = (int) $_GET['idgrup'];
$username_dosen = $_SESSION['username'];

$message = "";
$group_name = "Grup Tidak Ditemukan";

$group = $grupModel->getGrupByPembuatAndId($username_dosen, $idgrup);

if (!$group) {
    header("Location: manage_group_dosen.php?status=akses_ditolak");
    exit;
}
$group_name = $group['nama'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $keterangan = $_POST['keterangan'];

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));

    if ($eventModel->createEvent($idgrup, $judul, $slug, $tanggal, $keterangan, $jenis)) {
        header("Location: manage_event.php?idgrup=$idgrup&status=sukses");
        exit;
    } else {
        $message = "Gagal menambahkan event. Cek koneksi database.";
    }
}

if (isset($_GET['action'], $_GET['idevent']) && $_GET['action'] === 'delete') {
    $idevent = (int) $_GET['idevent'];

    if ($eventModel->deleteEvent($idevent, $idgrup)) {
        header("Location: manage_event.php?idgrup=$idgrup&status=hapus_sukses");
        exit;
    } else {
        $message = "Gagal menghapus event.";
    }
}

$event_list = $eventModel->getAllEventsByGrup($idgrup);

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses') {
        $message = "Event berhasil ditambahkan ✅";
    } elseif ($_GET['status'] == 'hapus_sukses') {
        $message = "Event berhasil dihapus 🗑️";
    } elseif ($_GET['status'] == 'gagal') {
        $message = "Gagal memproses event ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event Grup: <?php echo htmlspecialchars($group_name); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <header>
        <h2>Kelola Event</h2>
        <nav>
            <a href="detail_group.php?idgrup=<?php echo $idgrup; ?>">Detail Grup</a>
            <a href="manage_group_dosen.php">Kelola Grup</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <a href="detail_group.php?idgrup=<?php echo $idgrup; ?>" class="link-back">
            &leftarrow; Kembali ke Detail Grup
        </a>

        <?php if ($message): ?>
            <div class="alert-box <?php echo (strpos($message, 'Gagal') !== false) ? 'alert-error' : 'alert-success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="management-grid">
            
            <div class="box">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                    + Tambah Event Baru
                </h3>
                <form method="POST">
                    <input type="hidden" name="action" value="insert">
                    
                    <div style="margin-bottom: 15px;">
                        <label for="judul">Judul Event</label>
                        <input type="text" id="judul" name="judul" required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="jenis">Jenis Event</label>
                        <select id="jenis" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Privat">Privat</option>
                            <option value="Publik">Publik</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-green btn-block">Simpan Event</button>
                </form>
            </div>

            <div class="box">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                    Daftar Event Grup
                </h3>
                
                <div class="table-responsive">
                    <?php if (!empty($event_list)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Jenis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($event_list as $event): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($event['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['judul']); ?></td>
                                        <td>
                                            <span class="thread-status status-open" style="font-size: 0.85em;">
                                                <?php echo htmlspecialchars($event['jenis']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-blue btn-sm"
                                                href="edit_event.php?idgrup=<?php echo $idgrup; ?>&idevent=<?php echo $event['idevent']; ?>">
                                                Edit
                                            </a>

                                            <a class="btn btn-red btn-sm"
                                                href="manage_event.php?idgrup=<?php echo $idgrup; ?>&action=delete&idevent=<?php echo $event['idevent']; ?>"
                                                onclick="return confirm('Yakin ingin menghapus event ini?');">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-muted); font-style: italic; text-align: center;">
                            Belum ada Event yang terdaftar untuk Grup ini.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

</body>
</html>