<?php
session_start();
require_once '../../Database.php';
$mahasiswaModel = new MahasiswaModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$_SESSION['role'] = 'admin';

$totaldata = $mahasiswaModel->getMahasiswaCount();

$perpage = 5;
$totalpage = ceil($totaldata / $perpage);
if (isset($_GET['cboPage'])) {
    $perpage = (int) $_GET['cboPage'];
}

if (isset($_GET['p'])) {
    $p = (int) $_GET['p'];
    if ($p < 1) {
        $p = 1;
    }
} else {
    $p = 1;
}

if ($p > $totalpage && $totaldata > 0) {
    $p = $totalpage;
}

$start = ($p - 1) * $perpage;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <header>
        <h2>Dashboard Admin</h2>
        <nav>
            <a href="../admin_home.php">Home</a>
            <a href="../../logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 style="margin: 0; color: var(--title-color);">Pengelolaan Data Mahasiswa</h2>
                <a href="insertmahasiswa.php" class="btn btn-green">+ Tambah Mahasiswa</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>NRP</th>
                            <th>Nama</th>
                            <th>Gender</th>
                            <th>Tanggal Lahir</th>
                            <th>Angkatan</th>
                            <th>Foto</th>
                            <th>Kelola Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mahasiswaList = $mahasiswaModel->getMahasiswaLimit($start, $perpage);

                        if (!empty($mahasiswaList)) {
                            foreach ($mahasiswaList as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nrp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tanggal_lahir']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['angkatan']) . "</td>";
                                echo "<td>";
                                if ($row['foto_extention']) {
                                    $foto_path = "../../foto_mahasiswa/" . $row['nrp'] . "." . $row['foto_extention'];
                                    echo "<img src='" . $foto_path . "?" . time() . "' class='img-thumbnail'>";
                                } else {
                                    echo "-";
                                }
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='edit_mahasiswa.php?nrp=" . $row['nrp'] . "' class='btn btn-blue btn-sm'>Edit</a> ";
                                echo "<a href='delete_mahasiswa.php?nrp=" . $row['nrp'] . "' class='btn btn-red btn-sm' onclick='return confirm(\"Yakin hapus mahasiswa?\")'>Hapus</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' style='padding: 20px;'>Tidak ada data mahasiswa.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php
                echo "<a href='?p=1&cboPage=$perpage'>&laquo; First</a>";

                if ($p == 1) {
                    echo "<span class='disabled'>Prev</span>";
                } else {
                    $x = $p - 1;
                    echo "<a href='?p=$x&cboPage=$perpage'>Prev</a>";
                }

                for ($i = 1; $i <= $totalpage; $i++) {
                    if ($i == $p) {
                        echo "<strong>$i</strong>";
                    } else {
                        echo "<a href='?p=$i&cboPage=$perpage'>$i</a>";
                    }
                }

                if ($p == $totalpage || $totalpage == 0) {
                    echo "<span class='disabled'>Next</span>";
                } else {
                    $x = $p + 1;
                    echo "<a href='?p=$x&cboPage=$perpage'>Next</a>";
                }
                echo "<a href='?p=$totalpage&cboPage=$perpage'>Last &raquo;</a>";
                ?>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="../admin_home.php" class="link-back">Kembali ke Halaman Utama</a>
            </div>
        </div>
    </main>
</body>
</html>