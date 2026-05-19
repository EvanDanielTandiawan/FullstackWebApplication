<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../Database.php';
$threadModel = new ThreadModel();

if (!isset($_GET['idthread'], $_GET['idgrup'])) {
    header("Location: ../mahasiswa/mahasiswa_home.php");
    exit;
}

$idthread = (int) $_GET['idthread'];
$idgrup   = (int) $_GET['idgrup'];
$username = $_SESSION['username'];

$thread = $threadModel->getThreadById($idthread);

if (!$thread || $thread['username_pembuat'] !== $username) {
    $_SESSION['error'] = "Anda tidak memiliki izin untuk menutup thread ini.";
    header("Location: thread_list.php?idgrup=" . $idgrup);
    exit;
}

$success = $threadModel->closeThread($idthread, $username);

if ($success) {
    $_SESSION['success'] = "Thread berhasil ditutup.";
} else {
    $_SESSION['error'] = "Gagal menutup thread.";
}

header("Location: thread_list.php?idgrup=" . $idgrup);
exit;
