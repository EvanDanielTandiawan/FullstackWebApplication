<?php
session_start();

require_once '../../Database.php';
$mahasiswaModel = new MahasiswaModel();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['nrp'])) {
    die("NRP tidak ditemukan.");
}

$nrp = $_GET['nrp'];

$mahasiswa = $mahasiswaModel->getMahasiswaByNRP($nrp);

if (!$mahasiswa) {
    die("Data mahasiswa tidak ditemukan.");
}

if (!empty($mahasiswa['foto_extention'])) {
    $fotoPath = "../../foto_mahasiswa/" . $mahasiswa['nrp'] . "." . $mahasiswa['foto_extention'];
    if (file_exists($fotoPath)) {
        unlink($fotoPath); //hapus foto
    }
}

if ($mahasiswaModel->deleteMahasiswa($nrp)) {
    header("Location: admin_tabelmahasiswa.php");
    exit();
} else {
    echo "Gagal menghapus data.";
}
?>