<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../Database.php';
$grupModel = new GrupModel();
$threadModel = new ThreadModel();

if (!isset($_GET['idgrup'])) {
    header("Location: ../mahasiswa/mahasiswa_home.php");
    exit;
}

$idgrup = (int)$_GET['idgrup'];
$username = $_SESSION['username'];

$is_member = $grupModel->isMember($idgrup, $username);
if (!$is_member) {
    die("Anda bukan member grup ini.");
}

$grup = $grupModel->getGrupByID($idgrup);

if (!$grup) {
    die("Grup tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success = $threadModel->createThread($username, $idgrup);
    if ($success) {
        header("Location: thread_list.php?idgrup=" . $idgrup);
        exit;
    } else {
        $error = "Gagal membuat thread. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Buat Thread Baru</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Buat Thread Baru</h2>
        
        <div class="group-info">
            <p><strong>Grup:</strong> <?php echo htmlspecialchars($grup['nama']); ?></p>
            <p><strong>Jenis:</strong> <?php echo htmlspecialchars($grup['jenis']); ?></p>
            <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($grup['deskripsi']); ?></p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="button-group">
                <button type="submit">Buat Thread</button>
                <a href="thread_list.php?idgrup=<?php echo $idgrup; ?>" class="btn-cancel">Batal</a>
            </div>
        </form>
        
    </div>
</body>
</html>