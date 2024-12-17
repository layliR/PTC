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

// Tambah data prodi
if (isset($_POST['add'])) {
    $nama_prodi = $_POST['nama_prodi'];
    $jurusan = $_POST['jurusan'];

    // Validasi input (bisa ditambah lebih lanjut sesuai kebutuhan)
    if (empty($nama_prodi) || empty($jurusan)) {
        echo "<script>alert('Nama Prodi dan Jurusan tidak boleh kosong!');</script>";
    } else {
        // Generate kode_prodi secara otomatis (misal: kode prodi berdasarkan huruf pertama dan nomor urut)
        $kode_prodi = strtoupper(substr($nama_prodi, 0, 2)) . rand(100, 999);

        // SQL untuk menyisipkan data prodi
        $sql = "INSERT INTO prodi (kode_prodi, nama_prodi, jurusan) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("sss", $kode_prodi, $nama_prodi, $jurusan);
        if ($stmt->execute()) {
            echo "<script>alert('Data prodi berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data prodi: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Hapus data prodi
if (isset($_POST['delete'])) {
    $prodi_id = $_POST['prodi_id'];

    // Pastikan ID valid
    if (is_numeric($prodi_id)) {
        $sql = "DELETE FROM prodi WHERE prodi_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("i", $prodi_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data prodi berhasil dihapus!');</script>";
        } else {
            echo "<script>alert('Gagal menghapus data prodi: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak valid.');</script>";
    }
}

// Ambil semua data prodi
$result = $conn->query("SELECT * FROM prodi");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Prodi</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Manajemen Data Prodi</h1>

    <!-- Form untuk menambah data prodi -->
    <form method="POST">
        <h2>Tambah Data Prodi</h2>
        <label>Kode Prodi: <input type="text" name="kode_prodi" required></label><br>
        <label>Nama Prodi: <input type="text" name="nama_prodi" required></label><br>
        <label>Jurusan: <input type="text" name="jurusan" required></label><br>
        <button type="submit" name="add">Tambah Prodi</button>
    </form>

    <h2>Daftar Prodi</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Prodi</th>
                <th>Nama Prodi</th>
                <th>Jurusan</th>
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
                        <td><?php echo htmlspecialchars($row['kode_prodi']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_prodi']); ?></td>
                        <td><?php echo htmlspecialchars($row['jurusan']); ?></td>
                        <td>
                            <!-- Mengedit Prodi, mengarahkan ke edit_prodi.php -->
                            <a href="edit_prodi.php?id=<?php echo $row['prodi_id']; ?>">Edit</a>
                            <!-- Menghapus Prodi dengan form POST untuk menghindari penggunaan GET -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="prodi_id" value="<?php echo $row['prodi_id']; ?>">
                                <button type="submit" name="delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data prodi yang ditemukan.</td></tr>";
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
