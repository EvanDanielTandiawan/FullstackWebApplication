<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Dosen</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h2>Dashboard Dosen</h2>
    <nav>
        <a href="../logout.php" class="logout">Logout</a>
    </nav>
</header>

<main>
    <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Anda login sebagai <b>Dosen</b>.</p>
    
    <a class="menu-button" href="../change_password.php">Change Password</a>
    <a class="menu-button" href="grup/manage_group_dosen.php">Kelola Grup yang Saya Buat</a>
    <a class="menu-button" href="grup/add_group.php">+ Tambah Grup Baru</a>
</main>

</body>
</html>