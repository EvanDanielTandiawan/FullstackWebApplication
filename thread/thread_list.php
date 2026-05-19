<?php
session_start();
require_once '../Database.php';
$threadModel = new ThreadModel();
$grupModel = new GrupModel();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['idgrup'])) {
    header("Location: ../mahasiswa/mahasiswa_home.php");
    exit;
}

$idgrup = (int) $_GET['idgrup'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

if (!$grupModel->isMember($idgrup, $username)) {
    die("Anda bukan member grup ini.");
}

$grup = $grupModel->getGrupByID($idgrup);
$threads = $threadModel->getThreadsByGrup($idgrup);

if (!$grup) {
    die("Grup tidak ditemukan.");
}
if ($role === 'mahasiswa') {
    $backLink = "../mahasiswa/grup/mhs_detail_grup.php?id=" . $idgrup;
} elseif ($role === 'dosen') {
    $backLink = "../dosen/grup/detail_group.php?id=" . $idgrup;
} else {
    $backLink = "login.php";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thread Grup - <?php echo htmlspecialchars($grup['nama']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <header>
        <h2>Thread Diskusi</h2>
        <nav>
            <a href="<?php echo $backLink; ?>">&larr; Kembali</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="box">
            <h2 class="form-title"><?php echo htmlspecialchars($grup['nama']); ?></h2>
            <div class="group-info">
                <?php echo htmlspecialchars($grup['deskripsi']); ?> •
                <?php echo $grup['jenis']; ?> •
                Dibuat: <?php echo date('d/m/Y', strtotime($grup['tanggal_pembentukan'])); ?>
            </div>

            <div style="margin-top: 20px;">
                <a href="create_thread.php?idgrup=<?php echo $idgrup; ?>" class="btn">
                    ➕ Buat Thread Baru
                </a>
            </div>
        </div>

        <?php if (!empty($threads)): ?>
            <div class="thread-list">
                <?php foreach ($threads as $thread): ?>
                    <div class="thread-item">
                        
                        <div class="thread-info">
                            <div class="thread-title">
                                <?php echo htmlspecialchars($thread['judul'] ?? 'Thread #' . $thread['idthread']); ?>
                            </div>
                            <div class="thread-meta">
                                👤 <?php echo htmlspecialchars($thread['nama_pembuat']); ?> • 
                                📅 <?php echo date('d/m/Y H:i', strtotime($thread['tanggal_pembuatan'])); ?>
                            </div>
                        </div>

                        <div class="thread-actions">
                            <span class="thread-status <?php echo $thread['status'] == 'Open' ? 'status-open' : 'status-close'; ?>">
                                <?php echo $thread['status']; ?>
                            </span>

                            <?php if ($thread['status'] == 'Open'): ?>
                                <a href="chat.php?idthread=<?php echo $thread['idthread']; ?>" class="btn btn-thread">
                                    💬 Masuk
                                </a>
                            <?php else: ?>
                                <a href="chat.php?idthread=<?php echo $thread['idthread']; ?>" class="btn-cancel">
                                    Lihat Chat
                                </a>
                            <?php endif; ?>

                            <?php if ($thread['username_pembuat'] == $username && $thread['status'] == 'Open'): ?>
                                <a href="close_thread.php?idthread=<?php echo $thread['idthread']; ?>&idgrup=<?php echo $idgrup; ?>"
                                   class="btn-red"
                                   onclick="return confirm('Apakah Anda yakin ingin menutup thread ini?')">
                                   🔒 Tutup
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="box" style="text-align: center;">
                <p style="color: var(--text-muted);">📭 Belum ada thread di grup ini.</p>
                <a href="create_thread.php?idgrup=<?php echo $idgrup; ?>" class="btn">
                    ➕ Buat Thread Pertama
                </a>
            </div>
        <?php endif; ?>
        
    </main>
</body>
</html>