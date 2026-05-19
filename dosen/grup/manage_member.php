<?php
session_start();
require_once '../../Database.php';
$grupModel      = new GrupModel();
$accountModel   = new AccountModel();
$mahasiswaModel = new MahasiswaModel();

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
$alert_class = "";

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    
    if ($status == 'sukses_tambah') {
        $message = "Member berhasil ditambahkan! ✅";
        $alert_class = "alert-success";
    } 
    elseif ($status == 'sukses_hapus') {
        $message = "Member berhasil dikeluarkan. 🗑️";
        $alert_class = "alert-success";
    } 
    elseif ($status == 'sudah_ada') {
        $message = "Pengguna sudah menjadi anggota grup ini.";
        $alert_class = "alert-warning";
    } 
    elseif ($status == 'tidak_ditemukan') {
        $message = "Pengguna tidak ditemukan (Cek Username/NRP/NPK).";
        $alert_class = "alert-error";
    } 
    elseif ($status == 'hapus_diri') {
        $message = "Anda tidak bisa menghapus diri sendiri.";
        $alert_class = "alert-error";
    } 
    elseif ($status == 'gagal_db') {
        $message = "Terjadi kesalahan database.";
        $alert_class = "alert-error";
    }
    elseif ($status == 'akses_ditolak') {
        $message = "Anda tidak berhak mengelola grup ini.";
        $alert_class = "alert-error";
    }
}

$group = $grupModel->getGrupByPembuatAndId($username_dosen, $idgrup);

if (!$group) {
    header("Location: manage_group_dosen.php?status=akses_ditolak");
    exit;
}
$group_name = $group['nama'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_member') {
    $input_id = trim($_POST['new_username']); 
    
    $user_data = $accountModel->findUserByInput($input_id);

    if ($user_data) {
        $new_username = $user_data['username']; 

        if ($grupModel->isMember($idgrup, $new_username)) {
            header("Location: manage_member.php?idgrup=$idgrup&status=sudah_ada");
            exit;
        } else {
            if ($grupModel->addMember($idgrup, $new_username)) {
                header("Location: manage_member.php?idgrup=$idgrup&status=sukses_tambah");
                exit;
            } else {
                header("Location: manage_member.php?idgrup=$idgrup&status=gagal_db");
                exit;
            }
        }
    } else {
        header("Location: manage_member.php?idgrup=$idgrup&status=tidak_ditemukan");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'remove_member') {
    $member_to_remove = trim($_POST['member_username']);

    if ($member_to_remove == $username_dosen) {
        header("Location: manage_member.php?idgrup=$idgrup&status=hapus_diri");
        exit;
    } else {
        if ($grupModel->removeMember($idgrup, $member_to_remove)) {
             header("Location: manage_member.php?idgrup=$idgrup&status=sukses_hapus");
            exit;
        } else {
            header("Location: manage_member.php?idgrup=$idgrup&status=gagal_db");
            exit;
        }
    }
}
$member_list = $grupModel->getMembersWithRole($idgrup);

$mahasiswa_list = [];
if (isset($_GET['search']) && trim($_GET['search']) != '') {
    $mahasiswa_list = $mahasiswaModel->searchMahasiswa($_GET['search']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member Grup: <?php echo htmlspecialchars($group_name); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <header>
        <h2>Kelola Member</h2>
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
            <div class="alert-box <?php echo $alert_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="management-grid">
            
            <div class="box">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                    👥 Daftar Anggota Grup
                </h3>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Username (NRP/NPK)</th>
                                <th>Peran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($member_list as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['username']); ?></td>
                                    <td><?php echo htmlspecialchars($member['role']); ?></td>
                                    <td>
                                        <?php if ($member['username'] != $username_dosen): ?>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin mengeluarkan member ini?');">
                                                <input type="hidden" name="action" value="remove_member">
                                                <input type="hidden" name="member_username" value="<?php echo htmlspecialchars($member['username']); ?>">
                                                <button type="submit" class="btn btn-red btn-sm">Keluarkan</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: 0.9em; color: var(--text-muted);">(Pembuat)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                    ➕ Tambah Mahasiswa
                </h3>
                <p style="font-size: 0.9em; color: var(--text-muted);">Cari berdasarkan NRP atau Nama:</p>
                
                <form method="GET" style="margin-bottom: 20px;">
                    <input type="hidden" name="idgrup" value="<?php echo $idgrup; ?>">
                    <div class="search-group">
                        <input type="text" name="search" placeholder="Cari NRP/Nama..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn">Cari</button>
                    </div>
                </form>

                <?php if (isset($_GET['search']) && trim($_GET['search']) != '' && !empty($mahasiswa_list)): ?>
                    <h4>Hasil Pencarian:</h4>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>NRP</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mahasiswa_list as $mhs): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($mhs['nrp']); ?></td>
                                        <td><?php echo htmlspecialchars($mhs['nama']); ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="add_member">
                                                <input type="hidden" name="new_username" value="<?php echo htmlspecialchars($mhs['nrp']); ?>">
                                                <button type="submit" class="btn btn-green btn-sm">Tambah</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (isset($_GET['search']) && trim($_GET['search']) != ''): ?>
                    <div class="alert-box alert-warning">
                        Tidak ditemukan Mahasiswa dengan kata kunci "<?php echo htmlspecialchars($_GET['search']); ?>".
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
                    <h4>Tambah Dosen/Manual:</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_member">
                        <div class="search-group">
                            <input type="text" name="new_username" placeholder="Masukkan NPK Dosen" required>
                            <button type="submit" class="btn btn-green">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </main>
</body>
</html>