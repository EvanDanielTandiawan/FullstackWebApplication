<?php
session_start();
require_once '../../Database.php';
$grupModel = new GrupModel();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
$username = $_SESSION['username'];

$idgrup = (int)$_GET['id'];

// DELETE
if ($grupModel->removeMember($idgrup, $username)) {
    echo "<script>alert('Anda berhasil keluar dari grup.');location.href='../mahasiswa_home.php';</script>";
} else {
    echo "<script>alert('Gagal keluar dari grup.');location.href='../mahasiswa_home.php';</script>";
}
?>