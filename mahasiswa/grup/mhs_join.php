<?php
session_start();
require_once '../../Database.php';
$grupModel = new GrupModel();

if (!isset($_POST['idgrup']) || !isset($_POST['kode'])) {
    echo "<script>alert('Data tidak lengkap!'); history.back();</script>";
    exit;
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

$idgrup = (int) $_POST['idgrup'];
$kode = $_POST['kode'];

if ($grupModel->checkCode($idgrup, $kode)) {
    
    // Cek apakah user SUDAH member
    if ($grupModel->isMember($idgrup, $username)) {
        echo "<script>
                alert('Anda sudah tergabung dalam grup ini sebelumnya!');
                location.href='../mahasiswa_home.php';
              </script>";
    } else {
        // jika belum member, Lakukan insert
        if ($grupModel->addMember($idgrup, $username)) {
            echo "<script>
                    alert('Berhasil join grup!');
                    location.href='../mahasiswa_home.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal bergabung ke grup. Silakan coba lagi.');
                    history.back();
                  </script>";
        }
    }

} else {
    // Jika Salah
    echo "<script>
            alert('Kode salah! Tidak bisa join.');
            history.back();
          </script>";
}
?>