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

// Tambah data waktu
if (isset($_POST['add'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // Validasi input
    if (empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // SQL untuk menyisipkan data waktu
        $sql = "INSERT INTO waktu (hari, jam_mulai, jam_selesai) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("sss", $hari, $jam_mulai, $jam_selesai);
        if ($stmt->execute()) {
            echo "<script>alert('Data waktu berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data waktu: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Hapus data waktu
if (isset($_POST['delete'])) {
    $waktu_id = $_POST['waktu_id'];

    // Validasi ID waktu
    if (is_numeric($waktu_id)) {
        $sql = "DELETE FROM waktu WHERE waktu_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("i", $waktu_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data waktu berhasil dihapus!');</script>";
        } else {
            echo "<script>alert('Gagal menghapus data waktu: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak valid.');</script>";
    }
}

// Ambil semua data waktu
$result = $conn->query("SELECT * FROM waktu");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Waktu</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Manajemen Data Waktu</h1>

    <!-- Form untuk menambah data waktu -->
    <form method="POST">
        <h2>Tambah Data Waktu</h2>
        <label>Hari: <input type="text" name="hari" required></label><br>
        <label>Jam Mulai: <input type="time" name="jam_mulai" required></label><br>
        <label>Jam Selesai: <input type="time" name="jam_selesai" required></label><br>
        <button type="submit" name="add">Tambah Waktu</button>
    </form>

    <h2>Daftar Waktu</h2>
    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
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
                        <td><?php echo htmlspecialchars($row['hari']); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_mulai']); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_selesai']); ?></td>
                        <td>
                            <!-- Menghapus Waktu dengan form POST untuk menghindari penggunaan GET -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="waktu_id" value="<?php echo $row['waktu_id']; ?>">
                                <!-- Tombol Edit akan mengarahkan ke halaman edit dengan ID waktu -->
                                <a href="edit_waktu.php?id=<?php echo $row['waktu_id']; ?>">
                                    <button type="button">Edit</button>
                                </a>
                                <button type="submit" name="delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data waktu yang ditemukan.</td></tr>";
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
