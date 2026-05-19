<?php
session_start();
require_once '../../Database.php';
$grupModel = new GrupModel();
$eventModel = new EventModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['idgrup']) || !is_numeric($_GET['idgrup'])) {
    header("Location: manage_group_dosen.php");
    exit;
}

$idgrup = (int) $_GET['idgrup'];
$username_dosen = $_SESSION['username'];
$success_message = "";

// Cek apakah ada notifikasi setelah pembuatan grup
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Grup berhasil dibuat dan Anda telah ditambahkan sebagai member!";
}

// SELECT
$group = $grupModel->getGrupByPembuatAndId($username_dosen, $idgrup);
if (!$group) {
    // Grup tidak ditemukan
    header("Location: manage_group_dosen.php");
    exit;
}

$recentEvents = $eventModel->getRecentEvents($idgrup, 3)
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Grup: <?php echo htmlspecialchars($group['nama']); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <header>
        <h2>Detail Grup</h2>
        <nav>
            <a href="dosen/grup/manage_group_dosen.php">Kelola Grup</a>
            <a href="../../thread/thread_list.php?idgrup=<?php echo $idgrup; ?>" class="btn-blue" style="padding: 8px 15px; border-radius: 5px;">
                📝 Diskusi
            </a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <a href="dosen/grup/manage_group_dosen.php" class="link-back">
            &leftarrow; Kembali ke Daftar Grup
        </a>

        <?php if ($success_message): ?>
            <div class="alert-box alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <div class="box">
            <h2 class="form-title">
                <?php echo htmlspecialchars($group['nama']); ?> 
                <span class="thread-status status-open" style="font-size: 0.6em; vertical-align: middle;">
                    <?php echo htmlspecialchars($group['jenis']); ?>
                </span>
            </h2>
            
            <p><strong>Dibuat:</strong> <?php echo date('d M Y', strtotime($group['tanggal_pembentukan'])); ?></p>
            <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($group['deskripsi']); ?></p>

            <div class="alert-box alert-info">
                Kode Pendaftaran Grup: <span class="highlight-text"><?php echo htmlspecialchars($group['kode_pendaftaran']); ?></span>
                <br>
                <small>(Berikan kode ini kepada Mahasiswa untuk bergabung)</small>
            </div>
        </div>

        <div class="management-grid">
            
            <div class="box" id="event-management">
                <h3>🗓️ Kelola Event Grup</h3>
                <p>Di sini Anda dapat menambahkan, mengubah, atau menghapus kegiatan/pertemuan Grup.</p>
                
                <a class="btn btn-green" href="manage_event.php?idgrup=<?php echo $idgrup; ?>">
                    Kelola Event
                </a>

                <h4 style="margin-top: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 5px;">
                    Daftar Event (3 Terbaru)
                </h4>
                
                <?php if (!empty($recentEvents)): ?>
                    <ul class="event-list">
                        <?php foreach ($recentEvents as $event): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($event['judul']); ?></strong> 
                                <br>
                                <small class="text-muted">📅 <?php echo date('d/m/Y', strtotime($event['tanggal'])); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-style: italic;">Belum ada Event yang ditambahkan.</p>
                <?php endif; ?>
            </div>

            <div class="box" id="member-management">
                <h3>👥 Kelola Anggota Grup</h3>
                <p>Tambahkan atau hapus Mahasiswa/Dosen dari Grup ini.</p>
                
                <a class="btn" href="manage_member.php?idgrup=<?php echo $idgrup; ?>">
                    Kelola Anggota
                </a>

                <h4 style="margin-top: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 5px;">
                    Statistik
                </h4>
                
                <?php $total_members = $grupModel->getMemberCount($idgrup); ?>
                <div style="text-align: center; margin-top: 15px;">
                    <span style="font-size: 3em; font-weight: bold; color: var(--title-color);">
                        <?php echo $total_members; ?>
                    </span>
                    <p>Anggota Bergabung</p>
                </div>
            </div>
        </div>

        <div class="btn-container" style="justify-content: center; margin-top: 30px;">
            <a href="../../thread/thread_list.php?idgrup=<?php echo $idgrup; ?>" class="btn btn-blue">
                📝 Akses Thread Diskusi
            </a>
            <a href="../../thread/create_thread.php?idgrup=<?php echo $idgrup; ?>" class="btn btn-green">
                ➕ Buat Thread Baru
            </a>
        </div>

    </main>
</body>
</html>