<?php
session_start();
require_once '../Database.php';
$grupModel = new GrupModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];

$joinedGroups = $grupModel->getJoinedGroups($username);
$publicGroups = $grupModel->getAvailableGroups($username);
?>

<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Home Mahasiswa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h2>Dashboard Mahasiswa</h2>
    <nav>
        <a href="../logout.php" class="logout">Logout</a>
    </nav>
</header>

<main>
    <h1>Selamat datang, <?php echo $_SESSION['username']; ?>!</h1>
    <p>Anda login sebagai <b>Mahasiswa</b>.</p>
    <a class="btn" href="../change_password.php">Change Password</a>

    <br><br>

    <div class="box">
        <h2>Group yang Anda Ikuti</h2>

        <?php if (empty($joinedGroups)) { ?>
            <p>Anda belum mengikuti group apapun.</p>
        <?php } else { ?>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Nama Grup</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                    <?php foreach ($joinedGroups as $row) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                            <td>
                                <a class="btn" href="grup/mhs_detail_grup.php?id=<?php echo $row['idgrup']; ?>">Detail</a>
                                <a class="btn btn-red" href="grup/mhs_keluar.php?id=<?php echo $row['idgrup']; ?>"
                                   onclick="return confirm('Yakin ingin keluar dari grup ini?')">Keluar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </div>

    <div class="box">
        <h2>Group Publik yang Bisa Anda Join</h2>

        <?php if (empty($publicGroups)) { ?>
            <p>Tidak ada group publik yang tersedia.</p>
        <?php } else { ?>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Nama Grup</th>
                        <th>Pembuat</th>
                        <th>Aksi</th>
                    </tr>
                    <?php foreach ($publicGroups as $row) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['username_pembuat']); ?></td>
                            <td>
                                <form method="POST" action="grup/mhs_join.php" style="display:flex; gap:5px;">
                                    <input type="hidden" name="idgrup" value="<?php echo $row['idgrup']; ?>">
                                    <input type="text" name="kode" placeholder="Kode Pendaftaran" required>
                                    <button class="btn btn-green">Join</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </div>
</main>

</body>
</html>
