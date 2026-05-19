<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Insert Data Dosen</title>

    <script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
</head>

<body>
    <?php
    session_start();
    require_once '../../Database.php';
    
    $dosenModel = new DosenModel();
    $accountModel = new AccountModel();

    //assign variable
    $npk = $_POST['npk_dosen'];
    $nama = strtolower($_POST['nama']);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $foto = $_FILES['foto'];
    $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    //PENGECEKAN
    // 1. Cek INPUT
    if (empty($npk)) {
        $_SESSION['error_message'] = "NPK wajib diisi.";
        header("Location: insertdosen.php");
        exit();
    }
    if (strlen($npk) != 6) {
        $_SESSION['error_message'] = "NPK harus berupa 6 digit angka.";
        header("Location: insertdosen.php");
        exit();
    }
    if (empty($username)) {
        $_SESSION['error_message'] = "Username wajib diisi.";
        header("Location: insertdosen.php");
        exit();
    }

    // 2. Cek Duplikat
    $cekDosen = $dosenModel->getDosenByNPK($npk);
    if (!empty($cekDosen)) {
        $_SESSION['error_message'] = "NPK $npk sudah terdaftar. Silahkan menggunakan NPK lain.";
        header('location: insertdosen.php');
        exit();
    }
    if ($accountModel->isUsernameExist($username)) {
        $_SESSION['error_message'] = "Username $username sudah terdaftar. Silahkan menggunakan Username lain.";
        header('location: insertdosen.php');
        exit();
    }

    // Cek format foto
    if (!in_array($ext, $allowed)) {
        $_SESSION['error_message'] = "Format foto hanya JPG, JPEG, PNG, GIF.";
        header("Location: insertdosen.php");
        exit();
    }

    // Insert tabel Dosen
    $insertDosen = $dosenModel->insertDosen($npk, $nama, $ext);

    // Insert tabel Akun
    $insertAkun = $accountModel->createAccountDosen($npk, $username, $password);

    // Saving foto di folder 
    if ($insertDosen && $insertAkun) {
        $dst = "../../foto_dosen/" . $nama . "_" . $npk . "." . $ext;

        if (move_uploaded_file($foto['tmp_name'], $dst)) {
            echo "Insert Sukses.<br>";
            echo '<a href="admin_tabeldosen.php">Kembali ke Halaman Mengelola Data Dosen</a>';
        } else {
            echo "Data berhasil disimpan, namun Gagal upload gambar.";
        }
    } else {
        echo "Insert Database Gagal. Silakan coba lagi.";
    }
    ?>
</body>

</html>