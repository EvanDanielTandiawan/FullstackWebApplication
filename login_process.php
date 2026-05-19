<?php
session_start();
require_once 'Database.php';

$accountModel = new AccountModel();

$username = $_POST['username'];
$password = $_POST['password'];

$user = $accountModel->checkLogin($username, $password);


if ($user) {

    $_SESSION['username'] = $user['username'];

    if ((int) $user['isadmin'] === 1) {
        $_SESSION['role'] = "admin";
        header("Location: admin/admin_home.php");
        exit();
    }
    // Cek Dosen
    elseif (!empty($user['npk_dosen'])) {
        $_SESSION['role'] = "dosen";
        $_SESSION['id_user'] = $user['npk_dosen'];
        header("Location: dosen/dosen_home.php");
        exit();
    }
    // Cek Mahasiswa
    elseif (!empty($user['nrp_mahasiswa'])) {
        $_SESSION['role'] = "mahasiswa";
        $_SESSION['id_user'] = $user['nrp_mahasiswa'];
        header("Location: mahasiswa/mahasiswa_home.php");
        exit();
    }
    // Jika tidak masuk kriteria manapun
    else {
        $_SESSION['error'] = "Role akun tidak valid. IsAdmin: " . $user['isadmin'];
        header("Location: login.php"); // Kembalikan ke login agar user tahu errornya
        exit();
    }
} else {
    $_SESSION['error_message'] = "Username atau password salah!";
    header("Location: login.php");
}
?>