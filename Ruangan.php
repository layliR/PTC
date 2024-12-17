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

// Tambah data ruangan
if (isset($_POST['add'])) {
    $kode_ruangan = $_POST['kode_ruangan'];
    $nama_ruangan = $_POST['nama_ruangan'];
    $kapasitas = $_POST['kapasitas'];
    $lokasi = $_POST['lokasi'];

    // Validasi input (bisa ditambah lebih lanjut sesuai kebutuhan)
    if (empty($kode_ruangan) || empty($nama_ruangan) || empty($kapasitas) || empty($lokasi)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // SQL untuk menyisipkan data ruangan
        $sql = "INSERT INTO ruangan (kode_ruangan, nama_ruangan, kapasitas, lokasi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("ssis", $kode_ruangan, $nama_ruangan, $kapasitas, $lokasi);
        if ($stmt->execute()) {
            echo "<script>alert('Data ruangan berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data ruangan: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Hapus data ruangan
if (isset($_POST['delete'])) {
    $ruangan_id = $_POST['ruangan_id'];

    // Pastikan ID valid
    if (is_numeric($ruangan_id)) {
        $sql = "DELETE FROM ruangan WHERE ruangan_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("i", $ruangan_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data ruangan berhasil dihapus!');</script>";
        } else {
            echo "<script>alert('Gagal menghapus data ruangan: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak valid.');</script>";
    }
}

// Ambil semua data ruangan
$result = $conn->query("SELECT * FROM ruangan");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Ruangan</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Manajemen Data Ruangan</h1>

    <!-- Form untuk menambah data ruangan -->
    <form method="POST">
        <h2>Tambah Data Ruangan</h2>
        <label>Kode Ruangan: <input type="text" name="kode_ruangan" required></label><br>
        <label>Nama Ruangan: <input type="text" name="nama_ruangan" required></label><br>
        <label>Kapasitas: <input type="number" name="kapasitas" required></label><br>
        <label>Lokasi: <input type="text" name="lokasi" required></label><br>
        <button type="submit" name="add">Tambah Ruangan</button>
    </form>

    <h2>Daftar Ruangan</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Ruangan</th>
                <th>Nama Ruangan</th>
                <th>Kapasitas</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Periksa apakah ada data yang diambil
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['kode_ruangan']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_ruangan']); ?></td>
                        <td><?php echo htmlspecialchars($row['kapasitas']); ?></td>
                        <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                        <td>
                            <!-- Menghapus Ruangan dengan form POST untuk menghindari penggunaan GET -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="ruangan_id" value="<?php echo $row['ruangan_id']; ?>">
                                <!-- Tombol Edit akan mengarahkan ke halaman edit dengan ID ruangan -->
                                <a href="edit_ruangan.php?id=<?php echo $row['ruangan_id']; ?>">
                                    <button type="button">Edit</button>
                                </a>
                                <button type="submit" name="delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='5'>Tidak ada data ruangan yang ditemukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>
