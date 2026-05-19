<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Insert Data Mahasiswa</title>

    <script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
</head>

<body>
    <?php
    session_start();
    require_once '../../Database.php';

    $mahasiswaModel = new MahasiswaModel();
    $accountModel = new AccountModel();

    //assign variable
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nrp = $_POST['nrp'];
    $nama = strtolower($_POST['nama']);
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];
    $foto = $_FILES['foto'];
    $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    // PENGECEKAN
    // Cek Input
    if (empty($nrp)) {
        $_SESSION['error_message'] = "NRP wajib diisi.";
        header("Location: insertmahasiswa.php");
        exit();
    }
    if (strlen($nrp) != 9) {
        $_SESSION['error_message'] = "NRP harus berupa 9 digit angka.";
        header("Location: insertmahasiswa.php");
        exit();
    }
    if (empty($username)) {
        $_SESSION['error_message'] = "Username wajib diisi.";
        header("Location: insertmahasiswa.php"); // Perbaikan: Redirect ke mahasiswa bukan dosen
        exit();
    }

    // Cek NRP Duplikat 
    $cekMhs = $mahasiswaModel->getMahasiswaByNRP($nrp);
    if (!empty($cekMhs)) {
        $_SESSION['error_message'] = "NRP $nrp sudah terdaftar. Silahkan menggunakan NRP lain.";
        header('location: insertmahasiswa.php');
        exit();
    }
    if ($accountModel->isUsernameExist($username)) {
        $_SESSION['error_message'] = "Username $username sudah terdaftar. Silahkan menggunakan Username lain.";
        header('location: insertmahasiswa.php'); // Perbaikan: Redirect ke mahasiswa bukan dosen
        exit();
    }

    // Cek Format Foto
    if (!in_array($ext, $allowed)) {
        $_SESSION['error_message'] = "Format foto hanya JPG, JPEG, PNG, GIF.";
        header("Location: insertmahasiswa.php");
        exit();
    }

    //INSERT
    // Insert ke tabel Mahasiswa
    $insertMhsSukses = $mahasiswaModel->insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $ext);

    // Insert ke tabel Akun
    $insertAkunSukses = $accountModel->createAccountMahasiswa($username, $password, $nrp);

    if ($insertMhsSukses && $insertAkunSukses) {
        $dst = "../../foto_mahasiswa/" . $nrp . "." . $ext;

        if (move_uploaded_file($foto['tmp_name'], $dst)) {
            echo "Insert Sukses.<br>";
            echo '<a href="admin_tabelmahasiswa.php">Kembali ke Halaman Mengelola Data Mahasiswa</a>';
        } else {
            echo "Data berhasil disimpan di Database, namun Gagal upload gambar fisik.";
        }
    } else {
        echo "Insert Gagal. Terjadi kesalahan pada Database.";
    }
    ?>
</body>

</html>