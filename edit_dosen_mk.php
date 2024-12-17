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

// Ambil ID dosen_mk dari URL (query string)
if (isset($_GET['id'])) {
    $dosen_mk_id = $_GET['id'];

    // Validasi ID dosen_mk apakah valid (berupa angka)
    if (!is_numeric($dosen_mk_id)) {
        echo "<script>alert('ID tidak valid!'); window.location.href = 'dosen_mk.php';</script>";
        exit;
    }

    // Ambil data dosen_mk berdasarkan ID
    $sql = "SELECT * FROM dosen_mk WHERE dosen_mk_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dosen_mk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $dosen_mk = $result->fetch_assoc();
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href = 'dosen_mk.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location.href = 'dosen_mk.php';</script>";
    exit;
}

// Ambil daftar dosen, mata kuliah, dan program studi untuk dropdown
$result_dosen = $conn->query("SELECT dosen_id, nama_dosen FROM dosen");
$result_matkul = $conn->query("SELECT matkul_id, nama_matkul FROM matakuliah");
$result_prodi = $conn->query("SELECT prodi_id, nama_prodi FROM prodi");

// Update data dosen_mk
if (isset($_POST['update'])) {
    $dosen_id = $_POST['dosen_id'];
    $matkul_id = $_POST['matkul_id'];
    $prodi_id = $_POST['prodi_id'];
    $jurusan = $_POST['jurusan'];

    // Validasi input
    if (empty($dosen_id) || empty($matkul_id) || empty($prodi_id) || empty($jurusan)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // SQL untuk memperbarui data dosen_mk
        $sql_update = "UPDATE dosen_mk SET dosen_id = ?, matkul_id = ?, prodi_id = ?, jurusan = ? WHERE dosen_mk_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iiisi", $dosen_id, $matkul_id, $prodi_id, $jurusan, $dosen_mk_id);

        if ($stmt_update->execute()) {
            echo "<script>alert('Data berhasil diperbarui!'); window.location.href = 'dosen_mk.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data: " . $conn->error . "');</script>";
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
    <title>Edit Data Dosen-MK</title>
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
        select, input[type="text"] {
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
    <h1>Edit Data Dosen-MK</h1>

    <!-- Form untuk mengedit data dosen_mk -->
    <form method="POST">
        <label>Dosen:</label>
        <select name="dosen_id" required>
            <option value="">Pilih Dosen</option>
            <?php
            while ($row_dosen = $result_dosen->fetch_assoc()) {
                $selected = ($dosen_mk['dosen_id'] == $row_dosen['dosen_id']) ? "selected" : "";
                echo "<option value='" . $row_dosen['dosen_id'] . "' $selected>" . $row_dosen['nama_dosen'] . "</option>";
            }
            ?>
        </select>

        <label>Mata Kuliah:</label>
        <select name="matkul_id" required>
            <option value="">Pilih Mata Kuliah</option>
            <?php
            while ($row_matkul = $result_matkul->fetch_assoc()) {
                $selected = ($dosen_mk['matkul_id'] == $row_matkul['matkul_id']) ? "selected" : "";
                echo "<option value='" . $row_matkul['matkul_id'] . "' $selected>" . $row_matkul['nama_matkul'] . "</option>";
            }
            ?>
        </select>

        <label>Program Studi:</label>
        <select name="prodi_id" required>
            <option value="">Pilih Program Studi</option>
            <?php
            while ($row_prodi = $result_prodi->fetch_assoc()) {
                $selected = ($dosen_mk['prodi_id'] == $row_prodi['prodi_id']) ? "selected" : "";
                echo "<option value='" . $row_prodi['prodi_id'] . "' $selected>" . $row_prodi['nama_prodi'] . "</option>";
            }
            ?>
        </select>

        <label>Jurusan:</label>
        <input type="text" name="jurusan" value="<?php echo htmlspecialchars($dosen_mk['jurusan']); ?>" required>

        <button type="submit" name="update">Update Data</button>
    </form>
</body>
</html>
