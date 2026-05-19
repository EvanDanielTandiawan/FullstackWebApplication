<?php
session_start();

require_once '../../Database.php';
$grupModel = new GrupModel();
$eventModel = new EventModel();
$threadModel = new ThreadModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../../login.php");
    exit;
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID Grup tidak valid.");
}
$id = (int) $_GET['id'];

//AMBIL DATA GRUP
$grup = $grupModel->getGrupDetailWithCreator($id);
$username_pembuat = $grup["username_pembuat"];

if (!$grup) {
    die("Grup tidak ditemukan.");
}

//QUERY PEMBUAT GRUP
$members = $grupModel->getMembersWithDetails($id);

// QUERY EVENT
$events = $eventModel->getAllEventsByGrup($id);

// AMBIL THREAD
$threads = $threadModel->getThreadsByGrup($id);
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Grup</title>
    <link rel="stylesheet" href="../../css/style.css">

</head>

<body>

    <header>
        <h2>Detail Grup</h2>
        <nav>
            <a class="btn" href="../mahasiswa_home.php">Kembali ke Home</a>
            <a class="btn btn-thread" href="../../thread/thread_list.php?idgrup=<?php echo $id; ?>">
                📝 Diskusi & Chat
            </a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="box">
            <h2><?php echo htmlspecialchars($grup['nama']); ?></h2>
            <p><b>Deskripsi:</b> <?php echo htmlspecialchars($grup['deskripsi']); ?></p>
            <p><b>Pembuat:</b> <?php echo htmlspecialchars($username_pembuat); ?></p>
            <p><b>Tanggal dibuat:</b> <?php echo date('d-m-Y', strtotime($grup['tanggal_pembentukan'])); ?></p>
            <p><b>Jenis:</b> <?php echo htmlspecialchars($grup['jenis']); ?></p>

            <div class="btn-container">
                <a class="btn btn-thread" href="../../thread/thread_list.php?idgrup=<?php echo $id; ?>">
                    📝 Lihat Semua Thread
                </a>
                <a class="btn btn-thread" href="../../thread/create_thread.php?idgrup=<?php echo $id; ?>">
                    ➕ Buat Thread Baru
                </a>
            </div>
        </div>

        <div class="box">
            <h3>Daftar Thread Terbaru</h3>
            <?php
            // !empty() untuk cek array
            if (!empty($threads)): ?>
                <ul class="thread-list">
                    <?php
                    $counter = 0;
                    foreach ($threads as $thread):
                        if ($counter >= 3)
                            break; // Limit 3
                        $counter++;
                        ?>
                        <li class="thread-item">
                            <div class="thread-info">
                                <div class="thread-title">Thread #<?php echo $thread['idthread']; ?></div>
                                <div class="thread-meta">
                                    Dibuat pada: <?php echo date('d-m-Y H:i', strtotime($thread['tanggal_pembuatan'])); ?>
                                    <span
                                        class="thread-status <?php echo $thread['status'] == 'Open' ? 'status-open' : 'status-close'; ?>">
                                        <?php echo $thread['status']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="thread-actions">
                                <a class="btn btn-thread" href="../../thread/chat.php?idthread=<?php echo $thread['idthread']; ?>">
                                    Masuk Chat
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php
                // count() untuk hitung array
                if (count($threads) > 3): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <a class="btn btn-thread" href="../../thread/thread_list.php?idgrup=<?php echo $id; ?>">
                            📚 Lihat Semua Thread (<?php echo count($threads); ?>)
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Belum ada thread diskusi di grup ini.</p>
                <div class="btn-container">
                    <a class="btn btn-thread" href="../../thread/create_thread.php?idgrup=<?php echo $id; ?>">
                        ➕ Buat Thread Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="box">
            <h3>Daftar Member</h3>
            <?php if (!empty($members)): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['username']); ?></td>
                                    <td><?php echo htmlspecialchars($member['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($member['role']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Tidak ada member lain di grup ini.</p>
            <?php endif; ?>
        </div>

        <div class="box">
            <h3>Daftar Event</h3>
            <?php if (!empty($events)): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Event</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['judul']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($event['tanggal'])); ?></td>
                                    <td><?php echo htmlspecialchars($event['keterangan']); ?></td>
                                    <td><?php echo htmlspecialchars($event['jenis']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Belum ada event di grup ini.</p>
            <?php endif; ?>
        </div>

        <div class="box">
            <h3>Daftar Event</h3>
            <?php if (!empty($events)): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Event</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['judul']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($event['tanggal'])); ?></td>
                                    <td><?php echo htmlspecialchars($event['keterangan']); ?></td>
                                    <td><?php echo htmlspecialchars($event['jenis']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Belum ada event di grup ini.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>