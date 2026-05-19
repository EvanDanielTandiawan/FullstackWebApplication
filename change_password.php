<?php
session_start();
require_once 'Database.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$accountModel = new AccountModel();

$conn = new mysqli("localhost", "root", "", "fullstack");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $user = $_SESSION['username'];

    $oldPwd = $accountModel->checkLogin($user, $old_pass);

    if ($oldPwd) {
        $newPwd = $accountModel->updatePassword($user, $new_pass);
        echo "<script>alert('Password berhasil diubah'); window.location='". $_SESSION['role'] . "/" . $_SESSION['role'] . "_home.php';</script>";
    } else {
        echo "<script>alert('Password lama salah');</script>";
    }
}
?>

<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Ganti Password</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E0D9D9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2F5755;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            background-color: #2F5755;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background-color: white;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.3);
            width: 350px;
            text-align: center;
        }

        h2 {
            color: #2F5755;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            background-color: #2F5755;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            width: 100%;
        }

        .back-btn {
            background-color: #005a9e;
            margin-top: 10px;
        }
        /* MOBILE */
@media (max-width: 768px) {

    header {
        padding: 15px;
    }

    header h2 {
        font-size: 1.2em;
    }

    nav a {
        padding: 8px 12px;
        font-size: 0.9em;
    }

    .management-section,
    .content-container,
    .member-container {
        flex-direction: column;
    }

    .box,
    .form-box,
    .list-box,
    .member-list-box,
    .add-member-box {
        width: 100%;
    }

    .table-container,
    .list-box,
    .member-list-box {
        overflow-x: auto;
    }

    table {
        min-width: 600px;
    }

    .menu-button,
    .submit-button,
    .add-button,
    .remove-button {
        width: 100%;
        text-align: center;
        padding: 12px;
        font-size: 1em;
    }

    input[type="text"],
    input[type="date"],
    textarea,
    select {
        font-size: 1em;
        padding: 10px;
    }
}

/* EXTRA SMALL DEVICE */
@media (max-width: 480px) {
    header h2 {
        font-size: 1em;
    }

    nav a {
        font-size: 0.85em;
    }

    h1, h2, h3 {
        font-size: 1.1em;
    }
}
    </style> -->
</head>

<body>

    <header>
        <h2>Ganti Password</h2>
        <nav>
            <a href="<?php echo $_SESSION['role']."/".$_SESSION['role']; ?>_home.php">Kembali</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Ubah Password</h2>
            <form method="post">
                <input type="password" name="old_password" placeholder="Password Lama" required><br>
                <input type="password" name="new_password" placeholder="Password Baru" required><br>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </main>

</body>

</html>