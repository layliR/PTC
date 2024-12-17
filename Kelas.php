<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "penjadwalan2";

$conn = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah data kelas
if (isset($_POST['add'])) {
    $kode_kelas = $_POST['kode_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $prodi_id = $_POST['prodi_id'];

    // Validasi input
    if (empty($kode_kelas) || empty($nama_kelas) || empty($prodi_id)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // SQL untuk menyisipkan data kelas
        $sql = "INSERT INTO kelas (kode_kelas, nama_kelas, prodi_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("ssi", $kode_kelas, $nama_kelas, $prodi_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data kelas berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data kelas: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Hapus data kelas
if (isset($_POST['delete'])) {
    $kelas_id = $_POST['kelas_id'];

    // Pastikan ID valid
    if (is_numeric($kelas_id)) {
        $sql = "DELETE FROM kelas WHERE kelas_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("i", $kelas_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data kelas berhasil dihapus!');</script>";
        } else {
            echo "<script>alert('Gagal menghapus data kelas: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak valid.');</script>";
    }
}

// Ambil semua data kelas
$result = $conn->query("SELECT * FROM kelas");

// Ambil daftar program studi (prodi) untuk dropdown
$result_prodi = $conn->query("SELECT prodi_id, nama_prodi FROM prodi");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Kelas</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Manajemen Data Kelas</h1>

    <!-- Form untuk menambah data kelas -->
    <form method="POST">
        <h2>Tambah Data Kelas</h2>
        <label>Kode Kelas: <input type="text" name="kode_kelas" required></label><br>
        <label>Nama Kelas: <input type="text" name="nama_kelas" required></label><br>

        <label>Program Studi:
            <select name="prodi_id" required>
                <option value="">Pilih Program Studi</option>
                <?php
                while ($row_prodi = $result_prodi->fetch_assoc()) {
                    echo "<option value='" . $row_prodi['prodi_id'] . "'>" . $row_prodi['nama_prodi'] . "</option>";
                }
                ?>
            </select>
        </label><br>

        <button type="submit" name="add">Tambah Kelas</button>
    </form>

    <h2>Daftar Kelas</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Kelas</th>
                <th>Nama Kelas</th>
                <th>Program Studi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Periksa apakah ada data yang diambil
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Ambil nama program studi berdasarkan ID
                    $prodi_query = $conn->query("SELECT nama_prodi FROM prodi WHERE prodi_id=" . $row['prodi_id']);
                    $prodi = $prodi_query->fetch_assoc();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['kode_kelas']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                        <td><?php echo htmlspecialchars($prodi['nama_prodi']); ?></td>
                        <td>
                            <!-- Menghapus Kelas dengan form POST untuk menghindari penggunaan GET -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="kelas_id" value="<?php echo $row['kelas_id']; ?>">
                                <a href="edit_kelas.php?id=<?php echo $row['kelas_id']; ?>">
                                    <button type="button">Edit</button>
                                </a>
                                <button type="submit" name="delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data kelas yang ditemukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

