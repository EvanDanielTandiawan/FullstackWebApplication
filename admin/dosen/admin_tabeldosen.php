<?php
session_start();
require_once '../../Database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$dosenModel = new DosenModel();

if (isset($_GET['cboPage'])) {
    $perpage = (int) $_GET['cboPage'];
} else {
    $perpage = 5;
}

if (isset($_GET['p'])) {
    $p = (int) $_GET['p'];
    if ($p < 1)
        $p = 1;
} else {
    $p = 1;
}

$totaldata = $dosenModel->getDosenCount();
$totalpage = ceil($totaldata / $perpage);

if ($p > $totalpage && $totaldata > 0) {
    $p = $totalpage;
}

$start = ($p - 1) * $perpage;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Data Dosen</title>
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
                <h2 style="margin: 0; color: var(--title-color);">Pengelolaan Data Dosen</h2>
                <a href="insertdosen.php" class="btn btn-green">+ Tambah Dosen</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>NPK</th>
                            <th>Nama</th>
                            <th>Foto</th>
                            <th>Kelola Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dosenList = $dosenModel->getDosenLimit($start, $perpage);
                        if (!empty($dosenList)) {
                            foreach ($dosenList as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['npk']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                echo "<td>";

                                if ($row['foto_extension']) {
                                    $ext = strtolower($row['foto_extension']);
                                    // Adjust path based on your folder structure
                                    $path_foto = "../../foto_dosen/" . $row['nama'] . "_" . $row['npk'] . "." . $ext;

                                    if (file_exists($path_foto)) {
                                        echo "<img src='$path_foto?" . time() . "' class='img-thumbnail'>";
                                    } else {
                                        echo "<span class='text-muted'>(Foto tidak ditemukan)</span>";
                                    }
                                } else {
                                    echo "-";
                                }

                                echo "</td>";
                                echo "<td>";
                                echo "<a href='edit_dosen.php?npk=" . $row['npk'] . "' class='btn btn-blue btn-sm'>Edit</a> ";
                                echo "<a href='delete_dosen.php?npk=" . $row['npk'] . "' class='btn btn-red btn-sm' onclick='return confirm(\"Yakin hapus dosen ini?\")'>Hapus</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='padding: 20px;'>Belum ada data dosen.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php
                // First Button
                echo "<a href='?p=1&cboPage=$perpage'>&laquo; First</a>";

                // Prev Button
                if ($p == 1) {
                    echo "<span class='disabled'>Prev</span>";
                } else {
                    $x = $p - 1;
                    echo "<a href='?p=$x&cboPage=$perpage'>Prev</a>";
                }

                // Page Numbers
                for ($i = 1; $i <= $totalpage; $i++) {
                    if ($i == $p) {
                        echo "<strong>$i</strong>";
                    } else {
                        echo "<a href='?p=$i&cboPage=$perpage'>$i</a>";
                    }
                }

                // Next Button
                if ($p == $totalpage || $totalpage == 0) {
                    echo "<span class='disabled'>Next</span>";
                } else {
                    $x = $p + 1;
                    echo "<a href='?p=$x&cboPage=$perpage'>Next</a>";
                }

                // Last Button
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