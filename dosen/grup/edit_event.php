<?php
session_start();
require_once '../../Database.php';
$eventModel = new EventModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['idgrup']) || !is_numeric($_GET['idgrup']) || !isset($_GET['idevent']) || !is_numeric($_GET['idevent'])) {
    header("Location: manage_group_dosen.php");
    exit;
}

$idgrup = (int) $_GET['idgrup'];
$idevent = (int) $_GET['idevent'];
$username_dosen = $_SESSION['username'];
$message = "";

$event = $eventModel->getEventDetail($idevent, $idgrup, $username_dosen);

if (!$event) {
    // Event tidak ditemukan atau bukan milik dosen yang sedang login
    header("Location: manage_event.php?idgrup=$idgrup&msg=Event tidak ditemukan atau akses ditolak.");
    exit;
}
$group_name = $event['group_name'];

// UPDATE
// 2. Proses Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul']);
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);
    $jenis = $_POST['jenis'];

    // Panggil method update dari Class
    if ($eventModel->updateEvent($idevent, $judul, $tanggal, $keterangan, $jenis)) {
        $message = "Event " . htmlspecialchars($judul) . " berhasil diperbarui! ✅";

        // Update data di variabel lokal agar tampilan form langsung berubah tanpa refresh
        $event['judul'] = $judul;
        $event['tanggal'] = $tanggal;
        $event['keterangan'] = $keterangan;
        $event['jenis'] = $jenis;
    } else {
        $message = "Gagal update event. Silakan coba lagi. ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event: <?php echo htmlspecialchars($event['judul']); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <header>
        <h2>Edit Event</h2>
        <nav>
            <a href="manage_event.php?idgrup=<?php echo $idgrup; ?>">Kelola Event</a>
            <a href="detail_group.php?idgrup=<?php echo $idgrup; ?>">Detail Grup</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="box box-medium">
            
            <a href="manage_event.php?idgrup=<?php echo $idgrup; ?>" class="link-back">
                &leftarrow; Kembali ke Daftar Event
            </a>

            <h2 class="form-title">
                Edit: <?php echo htmlspecialchars($event['judul']); ?>
                <br>
                <small style="font-size: 0.6em; color: var(--text-muted); font-weight: normal;">
                    (Grup: <?php echo htmlspecialchars($group_name); ?>)
                </small>
            </h2>

            <?php if ($message): ?>
                <div class="alert-box <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                
                <div style="margin-bottom: 15px;">
                    <label for="judul">Judul Event</label>
                    <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($event['judul']); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d', strtotime($event['tanggal'])); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="jenis">Jenis Event</label>
                    <select id="jenis" name="jenis" required>
                        <option value="Privat" <?php echo $event['jenis'] == 'Privat' ? 'selected' : ''; ?>>Privat</option>
                        <option value="Publik" <?php echo $event['jenis'] == 'Publik' ? 'selected' : ''; ?>>Publik</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="4" required><?php echo htmlspecialchars($event['keterangan']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-green btn-full-mobile">Simpan Perubahan Event</button>
            </form>
            
        </div>
    </main>

</body>
</html>