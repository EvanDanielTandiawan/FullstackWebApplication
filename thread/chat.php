<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../Database.php';

if (!isset($_GET['idthread'])) {
    header("Location: ../mahasiswa/mahasiswa_home.php");
    exit;
}

$idthread = (int)$_GET['idthread'];
$username = $_SESSION['username'];

$chatModel = new ChatModel();
$threadModel = new ThreadModel();
$grupModel = new GrupModel();

$thread = $threadModel->getThreadById($idthread);

if (!$thread) {
    die("Thread tidak ditemukan.");
}

$is_member = $grupModel->isMember($thread['idgrup'], $username);

if (!$is_member) {
    die("Anda bukan member grup ini.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - <?php echo htmlspecialchars($thread['judul']); ?></title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body class="chat-page">
    <div class="chat-container">
        <div class="chat-header">
            <a href="thread_list.php?idgrup=<?php echo $thread['idgrup']; ?>" class="back-link">&larr; Kembali ke Thread</a>
            <h2>
                Thread #<?php echo $thread['idthread']; ?>
                <span class="thread-status <?php echo $thread['status'] == 'Open' ? 'status-open' : 'status-close'; ?>">
                    <?php echo $thread['status']; ?>
                </span>
            </h2>
            <div class="grup-info">Grup: <?php echo htmlspecialchars($thread['grup_nama']); ?></div>
        </div>
        
        <?php if ($thread['status'] == 'Close'): ?>
            <div class="chat-messages" id="chatMessages">
                </div>
            
            <div class="chat-input-container">
                <div class="disabled-input">
                    Thread ini sudah ditutup. Tidak dapat mengirim pesan baru.
                </div>
            </div>
        <?php else: ?>
            <div class="chat-messages" id="chatMessages">
                </div>
            
            <div class="chat-input-container">
                <form id="messageForm" class="chat-input-form">
                    <input type="text" id="messageInput" placeholder="Ketik pesan..." autocomplete="off" required>
                    <button type="submit" id="sendButton">➤</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    let lastMessageId = 0;
    const username = "<?= $_SESSION['username'] ?>";
    const idthread = <?= (int)$idthread ?>;
    const threadStatus = "<?= $thread['status'] ?>";

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function loadMessages() {
        $.ajax({
            url: 'get_messages.php',
            type: 'POST',
            dataType: 'json',
            data: {
                idthread: idthread,
                last_id: lastMessageId
            },
            success: function(messages) {
                if (messages.length > 0) {
                    messages.forEach(function(msg) {
                        addMessageToChat(msg);
                        lastMessageId = msg.idchat;
                    });
                    scrollToBottom();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    function addMessageToChat(msg) {
        const isSent = msg.username_pembuat === username;
        const messageClass = isSent ? 'sent' : 'received';

        const time = new Date(msg.tanggal_pembuatan).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const html = `
            <div class="message ${messageClass}">
                <div class="message-bubble">
                    ${escapeHtml(msg.isi)}
                </div>
                <div class="message-info">
                    ${msg.username_pembuat} • ${time}
                </div>
            </div>
        `;

        $('#chatMessages').append(html);
    }

    function sendMessage() {
        const message = $('#messageInput').val().trim();
        if (message === '') return;
        
        if (threadStatus === 'Close') {
            alert('Thread ini sudah ditutup. Tidak dapat mengirim pesan baru.');
            return;
        }
        
        $.post('send_message.php', {
            idthread: idthread,
            message: message
        }, function(res) {
            if (res === 'OK') {
                $('#messageInput').val('');
                setTimeout(loadMessages, 300);
            } else {
                alert(res);
            }
        });
    }

    function scrollToBottom() {
        const box = document.getElementById('chatMessages');
        box.scrollTop = box.scrollHeight;
    }

    $(document).ready(function() {
        loadMessages();
        setInterval(loadMessages, 2000);

        $('#messageForm').submit(function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        if (threadStatus === 'Close') {
            $('#messageInput').prop('disabled', true);
            $('#messageInput').attr('placeholder', 'Thread ditutup - tidak dapat mengirim pesan');
            $('#sendButton').prop('disabled', true);
        }
    });
    </script>
</body>
</html>