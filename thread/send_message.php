<?php
session_start();
require_once '../Database.php';
$threadModel = new ThreadModel();
$grupModel   = new GrupModel();
$chatModel   = new ChatModel();

if (!isset($_SESSION['username']) || !isset($_POST['idthread']) || !isset($_POST['message'])) {
    echo "Data tidak lengkap";
    exit;
}

$idthread = (int)$_POST['idthread'];
$message = trim($_POST['message']);
$username = $_SESSION['username'];

if (empty($message)) {
    echo "Pesan tidak boleh kosong";
    exit;
}

if ($idthread <= 0) {
    echo "ID Thread tidak valid";
    exit;
}

$thread = $threadModel->getThreadById($idthread);

if (!$thread) {
    echo "Thread tidak ditemukan";
    exit;
}

if ($thread['status'] !== 'Open') {
    echo "Thread sudah ditutup. Tidak dapat mengirim pesan.";
    exit;
}

if (!$grupModel->isMember($thread['idgrup'], $username)) {
    echo "Anda tidak dapat mengirim pesan di thread ini (Bukan anggota grup)";
    exit;
}

$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$success = $chatModel->sendMessage($idthread, $username, $message);

if ($success) {
    echo "OK";
} else {
    echo "Gagal mengirim pesan. Silakan coba lagi.";
}
?>