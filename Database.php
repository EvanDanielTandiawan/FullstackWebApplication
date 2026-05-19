<?php
class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "fullstack";
    private $connection;

    public function __construct()
    {
        $this->connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($this->connection->connect_errno) {
            die("Koneksi database gagal: " . $this->connection->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    public function escapeString($string)
    {
        return $this->connection->real_escape_string($string);
    }

    public function close()
    {
        $this->connection->close();
    }
}

class AccountModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function initiateAdmin()
    {
        $password = "password";
        $username = "admin";
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE akun SET password=? WHERE username=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $password, $username);
        return $stmt->execute();
    }

    public function checkLogin($username, $password)
    {
        $sql = "SELECT * FROM akun WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // cek password user yg telah dihashing
            if (password_verify($password, $row['password'])) { 
                return $row; // Login Sukses
            }
        }
        return false;
    }

    public function isUsernameExist($username)
    {
        $sql = "SELECT username FROM akun WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function createAccountDosen($npk, $username, $password)
    {
        //HASHING for security
        $password = password_hash($password, PASSWORD_DEFAULT);

        $isAdmin = 0;
        $sql = "INSERT INTO akun (npk_dosen, username, password, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $npk, $username, $password, $isAdmin);
        return $stmt->execute();
    }

    public function createAccountMahasiswa($username, $password, $nrp)
    {
        //HASHING for security
        $password = password_hash($password, PASSWORD_DEFAULT);

        $isAdmin = 0;
        $sql = "INSERT INTO akun (username, password, nrp_mahasiswa, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $nrp, $isAdmin);
        return $stmt->execute();
    }

    //CHANGE PWD
    public function updatePassword($username, $newPassword)
    {
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE akun SET password=? WHERE username=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $newPassword, $username);
        return $stmt->execute();
    }

    //EDIT MHS
    public function getAccountByMahasiswaNRP($nrp)
    {
        $sql = "SELECT nrp_mahasiswa FROM akun WHERE nrp_mahasiswa=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateAccountMahasiswa($username, $password, $new_nrp, $original_nrp)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE akun SET username=?, password=?, nrp_mahasiswa=? WHERE nrp_mahasiswa=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $username, $password, $new_nrp, $original_nrp);
        return $stmt->execute();
    }

    public function getAccountByDosenNPK($npk)
    {
        $sql = "SELECT username, password FROM akun WHERE npk_dosen = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $npk);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateAccountDosen($username, $password, $npk)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($password)) {
            $sql = "UPDATE akun SET username=?, password=? WHERE npk_dosen=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sss", $username, $password, $npk);
        } else {
            $sql = "UPDATE akun SET username=? WHERE npk_dosen=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $username, $npk);
        }
        return $stmt->execute();
    }

    public function findUserByInput($input)
    {
        $sql = "SELECT username FROM akun WHERE username = ? OR nrp_mahasiswa = ? OR npk_dosen = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $input, $input, $input);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

class DosenModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function insertDosen($npk, $nama, $ext)
    {
        $sql = "INSERT INTO dosen(npk, nama, foto_extension) VALUES(?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sss', $npk, $nama, $ext);
        return $stmt->execute();
    }
    public function getDosenByNPK($npk)
    {
        $sql = "SELECT * FROM dosen WHERE npk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $npk);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteDosen($npk)
    {
        $sql = "DELETE FROM dosen WHERE npk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $npk);
        return $stmt->execute();
    }

    public function getAllDosen()
    {
        $sql = "SELECT * FROM dosen ORDER BY nama";
        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function updateDosen($npk, $nama, $ext)
    {
        $sql = "UPDATE dosen SET nama=?, foto_extension=? WHERE npk=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $nama, $ext, $npk);
        return $stmt->execute();
    }
    public function updateDosenNameOnly($npk, $nama)
    {
        $sql = "UPDATE dosen SET nama=? WHERE npk=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nama, $npk);
        return $stmt->execute();
    }

    // PAGING
    public function getDosenCount()
    {
        $sql = "SELECT COUNT(npk) AS total FROM dosen";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }
    public function getDosenLimit($start, $limit)
    {
        $sql = "SELECT * FROM dosen ORDER BY npk LIMIT ?, ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}

class MahasiswaModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getMahasiswaByNRP($nrp)
    {
        $sql = "SELECT * FROM mahasiswa WHERE nrp = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function searchMahasiswa($keyword)
    {
        $search_query = "%" . $keyword . "%";
        $sql = "SELECT nrp, nama FROM mahasiswa WHERE nrp LIKE ? OR nama LIKE ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $search_query, $search_query);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getAllMahasiswa()
    {
        $sql = "SELECT * FROM mahasiswa ORDER BY nama";
        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $ext)
    {
        $sql = "INSERT INTO mahasiswa(nrp, nama, gender, tanggal_lahir, angkatan, foto_extention) VALUES(?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssis', $nrp, $nama, $gender, $tanggal_lahir, $angkatan, $ext);
        return $stmt->execute();
    }

    //PAGING
    public function getMahasiswaCount()
    {
        $sql = "SELECT COUNT(nrp) AS total FROM mahasiswa";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }

    public function getMahasiswaLimit($start, $limit)
    {
        $sql = "SELECT * FROM mahasiswa ORDER BY nrp LIMIT ?, ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getMahasiswaWithAccount($nrp)
    {
        $sql = "SELECT m.*, a.username, a.password 
                FROM mahasiswa m 
                LEFT JOIN akun a ON m.nrp = a.nrp_mahasiswa 
                WHERE m.nrp = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_ext, $original_nrp)
    {
        $sql = "UPDATE mahasiswa SET nrp=?, nama=?, gender=?, tanggal_lahir=?, angkatan=?, foto_extention=? 
                WHERE nrp=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssssiss",
            $nrp,
            $nama,
            $gender,
            $tanggal_lahir,
            $angkatan,     
            $foto_ext,
            $original_nrp
        );
        return $stmt->execute();
    }

    public function deleteMahasiswa($nrp)
    {
        $sql = "DELETE FROM mahasiswa WHERE nrp = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nrp);
        return $stmt->execute();
    }
}

class GrupModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    public function createGrup($username, $nama, $deskripsi, $jenis)
    {
        $kode = 'GRP' . date('ymd') . strtoupper(substr(uniqid(), -4));
        $tanggal = date('Y-m-d H:i:s');

        $sql = "INSERT INTO grup (username_pembuat, nama, deskripsi, tanggal_pembentukan, jenis, kode_pendaftaran) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssss", $username, $nama, $deskripsi, $tanggal, $jenis, $kode);

        if ($stmt->execute()) {
            $insertId = $this->db->getConnection();
            return $insertId->insert_id;
        } else {
            return false;
        }
    }

    public function addMember($idgrup, $username)
    {
        $sql = "INSERT INTO member_grup (idgrup, username) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        return $stmt->execute();
    }

    public function getGrupByID($idgrup)
    {
        $sql = "SELECT * FROM grup WHERE idgrup = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getGrupByPembuat($username)
    {
        $sql = "SELECT * FROM grup WHERE username_pembuat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result(); // Simpan result dulu

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data; // Return setelah loop selesai
    }

    public function getGrupByPembuatAndId($username, $idgrup)
    {
        $sql = "SELECT * FROM grup WHERE idgrup = ? AND username_pembuat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function isMember($idgrup, $username)
    {
        $sql = "SELECT username FROM member_grup WHERE idgrup = ? AND username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    public function getMemberCount($idgrup)
    {
        $sql = "SELECT COUNT(*) AS total FROM member_grup WHERE idgrup = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function updateGrup($idgrup, $nama, $deskripsi, $jenis, $username)
    {
        $sql = "UPDATE grup SET nama = ?, deskripsi = ?, jenis = ? WHERE idgrup = ? AND username_pembuat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssis", $nama, $deskripsi, $jenis, $idgrup, $username);
        return $stmt->execute();
    }

    public function deleteFullGrup($idgrup)
    {
        // Hapus Event 
        $sqlEvent = "DELETE FROM event WHERE idgrup = ?";
        $stmtEvent = $this->db->prepare($sqlEvent);
        $stmtEvent->bind_param("i", $idgrup);
        $stmtEvent->execute();

        // Hapus Member 
        $sqlMember = "DELETE FROM member_grup WHERE idgrup = ?";
        $stmtMember = $this->db->prepare($sqlMember);
        $stmtMember->bind_param("i", $idgrup);
        $stmtMember->execute();

        // Hapus Thread/Chat 
        $sqlThread = "DELETE FROM thread WHERE idgrup = ?";
        $stmtThread = $this->db->prepare($sqlThread);
        $stmtThread->bind_param("i", $idgrup);
        $stmtThread->execute();

        // Hapus Grup Utama
        $sqlGrup = "DELETE FROM grup WHERE idgrup = ?";
        $stmtGrup = $this->db->prepare($sqlGrup);
        $stmtGrup->bind_param("i", $idgrup);
        return $stmtGrup->execute();
    }

    public function isOwner($idgrup, $username)
    {
        $sql = "SELECT idgrup FROM grup WHERE idgrup = ? AND username_pembuat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function removeMember($idgrup, $username)
    {
        $sql = "DELETE FROM member_grup WHERE idgrup = ? AND username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        return $stmt->execute();
    }
    public function getMembersWithRole($idgrup)
    {
        $sql = "SELECT mg.username,
                CASE 
                    WHEN a.npk_dosen IS NOT NULL THEN 'Dosen'
                    WHEN a.nrp_mahasiswa IS NOT NULL THEN 'Mahasiswa'
                    ELSE 'Admin'
                END AS role
                FROM member_grup mg
                JOIN akun a ON mg.username = a.username
                WHERE mg.idgrup = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getGrupDetailWithCreator($idgrup)
    {
        $sql = "SELECT g.*, 
                CASE 
                    WHEN d.nama IS NOT NULL THEN CONCAT(d.nama, ' (Dosen)')
                    WHEN m.nama IS NOT NULL THEN CONCAT(m.nama, ' (Mahasiswa)')
                    ELSE g.username_pembuat
                END AS nama_pembuat_lengkap
                FROM grup g
                LEFT JOIN akun a ON g.username_pembuat = a.username
                LEFT JOIN dosen d ON a.npk_dosen = d.npk
                LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                WHERE g.idgrup = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getMembersWithDetails($idgrup)
    {
        $sql = "SELECT mg.username,
                CASE 
                    WHEN d.nama IS NOT NULL THEN d.nama
                    WHEN m.nama IS NOT NULL THEN m.nama
                    ELSE mg.username
                END AS nama_lengkap,
                CASE 
                    WHEN d.nama IS NOT NULL THEN 'Dosen'
                    WHEN m.nama IS NOT NULL THEN 'Mahasiswa'
                    ELSE 'Admin'
                END AS role
                FROM member_grup mg
                JOIN akun a ON mg.username = a.username
                LEFT JOIN dosen d ON a.npk_dosen = d.npk
                LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                WHERE mg.idgrup = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function checkCode($idgrup, $kode)
    {
        $sql = "SELECT idgrup FROM grup WHERE idgrup = ? AND kode_pendaftaran = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idgrup, $kode);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getJoinedGroups($username)
    {
        $sql = "SELECT g.* FROM member_grup mg 
                JOIN grup g ON mg.idgrup = g.idgrup 
                WHERE mg.username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function getAvailableGroups($username)
    {
        $sql = "SELECT g.* FROM grup g 
                WHERE g.jenis = 'Publik' 
                AND g.idgrup NOT IN (SELECT idgrup FROM member_grup WHERE username = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
class EventModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getRecentEvents($idgrup, $limit = 3)
    {
        $sql = "SELECT * FROM event WHERE idgrup = ? ORDER BY tanggal DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $idgrup, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getEventDetail($idevent, $idgrup, $username)
    {
        $sql = "SELECT e.*, g.nama AS group_name 
                FROM event e 
                JOIN grup g ON e.idgrup = g.idgrup 
                WHERE e.idevent = ? AND e.idgrup = ? AND g.username_pembuat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $idevent, $idgrup, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function getAllEventsByGrup($idgrup)
    {
        $sql = "SELECT * FROM event WHERE idgrup = ? ORDER BY tanggal DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function createEvent($idgrup, $judul, $slug, $tanggal, $keterangan, $jenis)
    {
        $sql = "INSERT INTO event (idgrup, judul, `judul-slug`, tanggal, keterangan, jenis)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssss", $idgrup, $judul, $slug, $tanggal, $keterangan, $jenis);
        return $stmt->execute();
    }

    public function updateEvent($idevent, $judul, $tanggal, $keterangan, $jenis)
    {
        $sql = "UPDATE event SET judul = ?, tanggal = ?, keterangan = ?, jenis = ? WHERE idevent = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $judul, $tanggal, $keterangan, $jenis, $idevent);
        return $stmt->execute();
    }
    public function deleteEvent($idevent, $idgrup)
    {
        $sql = "DELETE FROM event WHERE idevent = ? AND idgrup = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $idevent, $idgrup);
        return $stmt->execute();
    }
}

class ThreadModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createThread($username, $idgrup)
    {
        $sql = "INSERT INTO thread (username_pembuat, idgrup, tanggal_pembuatan, status) 
                VALUES (?, ?, NOW(), 'Open')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $username, $idgrup);
        return $stmt->execute();
    }

    public function getThreadsByGrup($idgrup)
    {
        $sql = "SELECT t.*, 
                CASE 
                    WHEN a.npk_dosen IS NOT NULL THEN d.nama
                    WHEN a.nrp_mahasiswa IS NOT NULL THEN m.nama
                    ELSE a.username
                END AS nama_pembuat
                FROM thread t
                JOIN akun a ON t.username_pembuat = a.username
                LEFT JOIN dosen d ON a.npk_dosen = d.npk
                LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                WHERE t.idgrup = ?
                ORDER BY t.tanggal_pembuatan DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result(); // Simpan Result dulu

        $threads = [];
        while ($row = $result->fetch_assoc()) {
            $threads[] = $row;
        }
        return $threads; // Return setelah loop
    }

    public function closeThread($idthread, $username)
    {
        $sql = "UPDATE thread SET status = 'Close' 
                WHERE idthread = ? AND username_pembuat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $idthread, $username);
        return $stmt->execute();
    }

    public function getThreadById($idthread)
    {
        $sql = "SELECT t.*, g.nama as grup_nama FROM thread t 
                JOIN grup g ON t.idgrup = g.idgrup 
                WHERE t.idthread = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idthread);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

class ChatModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function sendMessage($idthread, $username, $isi)
    {
        $sql = "INSERT INTO chat (idthread, username_pembuat, isi, tanggal_pembuatan) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $idthread, $username, $isi);
        return $stmt->execute();
    }

    public function getMessages($idthread, $last_id = 0)
    {
        $sql = "SELECT c.*, a.username as username_pembuat 
                FROM chat c
                JOIN akun a ON c.username_pembuat = a.username
                WHERE c.idthread = ? AND c.idchat > ?
                ORDER BY c.tanggal_pembuatan ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $idthread, $last_id);
        $stmt->execute();
        $result = $stmt->get_result(); // Simpan Result dulu

        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        return $messages; // Return setelah loop
    }

    public function getLastMessageId($idthread)
    {
        $sql = "SELECT MAX(idchat) as last_id FROM chat WHERE idthread = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idthread);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['last_id'] ?? 0;
    }
}
?>