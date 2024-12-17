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

// Ambil data matakuliah berdasarkan ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $matkul_id = $_GET['id'];
    $sql = "SELECT * FROM matakuliah WHERE matkul_id=?";
    $stmt = $conn->prepare($sql);

    // Periksa apakah prepare berhasil
    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("i", $matkul_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $matkul = $result->fetch_assoc();
    } else {
        die("Data matkul tidak ditemukan.");
    }

    $stmt->close();
} else {
    die("ID matkul tidak ditemukan.");
}

// Ambil daftar prodi untuk dropdown
$result_prodi = $conn->query("SELECT prodi_id, nama_prodi FROM prodi");

// Edit data matkul
if (isset($_POST['edit'])) {
    $id = $_POST['matkul_id'];
    $nama_matkul = $_POST['nama_matkul'];
    $kode_matkul = $_POST['kode_matkul'];
    $sks = (int)$_POST['sks'];
    $semester = (int)$_POST['semester'];
    $prodi_id = (int)$_POST['prodi_id'];

    $sql = "UPDATE matakuliah SET nama_matkul=?, kode_matkul=?, sks=?, semester=?, prodi_id=? WHERE matkul_id=?";
    $stmt = $conn->prepare($sql);

    // Periksa apakah prepare berhasil
    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("ssiiii", $nama_matkul, $kode_matkul, $sks, $semester, $prodi_id, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data matkul berhasil diperbarui!'); window.location.href='Matkul.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data matkul: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Matakuliah</title>
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
    <h1>Edit Profil Matakuliah</h1>

    <form method="POST">
        <input type="hidden" name="matkul_id" value="<?php echo $matkul['matkul_id']; ?>">

        <label>Nama MataKuliah: <input type="text" name="nama_matkul" value="<?php echo htmlspecialchars($matkul['nama_matkul']); ?>" required></label><br>

        <label>Kode MataKuliah: <input type="text" name="kode_matkul" value="<?php echo htmlspecialchars($matkul['kode_matkul']); ?>" required></label><br>

        <label>SKS: <input type="number" name="sks" value="<?php echo htmlspecialchars($matkul['sks']); ?>" required></label><br>

        <!-- Dropdown untuk Semester -->
        <label>Semester:
            <select name="semester" required>
                <option value="">Pilih Semester</option>
                <?php
                for ($i = 1; $i <= 8; $i++) {  // Asumsi maksimal semester 8
                    $selected = ($i == $matkul['semester']) ? 'selected' : '';
                    echo "<option value='$i' $selected>Semester $i</option>";
                }
                ?>
            </select>
        </label><br>

        <label>Prodi:
            <select name="prodi_id" required>
                <option value="">Pilih Program Studi</option>
                <?php
                while ($row_prodi = $result_prodi->fetch_assoc()) {
                    $selected = ($row_prodi['prodi_id'] == $matkul['prodi_id']) ? 'selected' : '';
                    echo "<option value='" . $row_prodi['prodi_id'] . "' $selected>" . $row_prodi['nama_prodi'] . "</option>";
                }
                ?>
            </select>
        </label><br>

        <button type="submit" name="edit">Simpan Perubahan</button>
    </form>
</body>
</html>
