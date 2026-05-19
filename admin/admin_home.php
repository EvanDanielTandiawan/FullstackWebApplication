<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Home - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h2>Dashboard Admin</h2>
    <nav>
        <a href="../logout.php" class="logout">Logout</a>
    </nav>
</header>

<main class="container text-center">
    <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Anda login sebagai <b>ADMIN</b>.</p>
    <p>Pilih menu di bawah untuk mengelola data dosen dan mahasiswa.</p>
    
    <div class="btn-container" style="justify-content: center;">
        <a href="dosen/admin_tabeldosen.php" class="menu-button">Kelola Data Dosen</a>
        <a href="mahasiswa/admin_tabelmahasiswa.php" class="menu-button">Kelola Data Mahasiswa</a>
    </div>
</main>
</body>
</html>