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

// Ambil ID kelas dari URL (query string)
if (isset($_GET['id'])) {
    $kelas_id = $_GET['id'];

    // Validasi ID kelas apakah valid (berupa angka)
    if (!is_numeric($kelas_id)) {
        echo "<script>alert('ID kelas tidak valid!'); window.location.href = 'kelas.php';</script>";
        exit;
    }

    // Ambil data kelas berdasarkan ID
    $sql = "SELECT * FROM kelas WHERE kelas_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kelas_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $kelas = $result->fetch_assoc();
    } else {
        echo "<script>alert('Data kelas tidak ditemukan!'); window.location.href = 'kelas.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID kelas tidak valid!'); window.location.href = 'kelas.php';</script>";
    exit;
}

// Ambil daftar program studi untuk dropdown
$result_prodi = $conn->query("SELECT prodi_id, nama_prodi FROM prodi");

// Update data kelas
if (isset($_POST['update'])) {
    $kode_kelas = $_POST['kode_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $prodi_id = $_POST['prodi_id'];

    // Validasi input
    if (empty($kode_kelas) || empty($nama_kelas) || empty($prodi_id)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // SQL untuk memperbarui data kelas
        $sql_update = "UPDATE kelas SET kode_kelas = ?, nama_kelas = ?, prodi_id = ? WHERE kelas_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssii", $kode_kelas, $nama_kelas, $prodi_id, $kelas_id);

        if ($stmt_update->execute()) {
            echo "<script>alert('Data kelas berhasil diperbarui!'); window.location.href = 'kelas.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data kelas: " . $conn->error . "');</script>";
        }
        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Kelas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Edit Data Kelas</h1>

    <!-- Form untuk mengedit data kelas -->
    <form method="POST">
        <label>Kode Kelas:</label>
        <input type="text" name="kode_kelas" value="<?php echo htmlspecialchars($kelas['kode_kelas']); ?>" required><br>

        <label>Nama Kelas:</label>
        <input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($kelas['nama_kelas']); ?>" required><br>

        <label>Program Studi:</label>
        <select name="prodi_id" required>
            <option value="">Pilih Program Studi</option>
            <?php
            // Menampilkan daftar program studi, dengan memilih program studi yang terkait dengan kelas
            while ($row_prodi = $result_prodi->fetch_assoc()) {
                $selected = ($kelas['prodi_id'] == $row_prodi['prodi_id']) ? "selected" : "";
                echo "<option value='" . $row_prodi['prodi_id'] . "' $selected>" . $row_prodi['nama_prodi'] . "</option>";
            }
            ?>
        </select><br>

        <button type="submit" name="update">Update Kelas</button>
    </form>
</body>
</html>
