<?php
session_start();
require_once '../Database.php';
$threadModel = new ThreadModel();
$grupModel   = new GrupModel();
$chatModel   = new ChatModel();

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || !isset($_POST['idthread'])) {
    echo json_encode([]);
    exit;
}

$idthread = (int)$_POST['idthread'];
$last_id  = isset($_POST['last_id']) ? (int)$_POST['last_id'] : 0;
$username = $_SESSION['username'];

$db = new Database();

$thread = $threadModel->getThreadById($idthread);

if (!$thread) {
    echo json_encode([]);
    exit;
}

// Cek membership
if (!$grupModel->isMember($thread['idgrup'], $username)) {
    echo json_encode([]);
    exit;
}

// Get message
$messages = $chatModel->getMessages($idthread, $last_id);

echo json_encode($messages);
exit;
?>