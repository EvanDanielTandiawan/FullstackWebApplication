<?php
session_start();
require_once '../../Database.php';
$dosenModel = new DosenModel();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

if (isset($_GET['npk'])) {
    $npk = $_GET['npk'];

    $dosen = $dosenModel->getDosenByNPK($npk);

    if ($dosen) {
        $nama_file = $dosen['nama'];
        $foto_path = "../../foto_dosen/" . $nama_file . "_" . $npk . "." . $dosen['foto_extension'];

        if (file_exists($foto_path)) {
            unlink($foto_path); //hapus di folder
        }

        $dosenModel->deleteDosen($npk);
    }
}

header("Location: admin_tabeldosen.php");
exit();
