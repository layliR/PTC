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

// Ambil data ruangan berdasarkan ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $ruangan_id = $_GET['id'];
    $sql = "SELECT * FROM ruangan WHERE ruangan_id=?";
    $stmt = $conn->prepare($sql);

    // Periksa apakah prepare berhasil
    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("i", $ruangan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $ruangan = $result->fetch_assoc();
    } else {
        die("Data ruangan tidak ditemukan.");
    }

    $stmt->close();
} else {
    die("ID ruangan tidak ditemukan.");
}

// Edit data ruangan
if (isset($_POST['edit'])) {
    $id = $_POST['ruangan_id'];
    $kode_ruangan = $_POST['kode_ruangan'];
    $nama_ruangan = $_POST['nama_ruangan'];
    $kapasitas = (int)$_POST['kapasitas'];
    $lokasi = $_POST['lokasi'];

    $sql = "UPDATE ruangan SET kode_ruangan=?, nama_ruangan=?, kapasitas=?, lokasi=? WHERE ruangan_id=?";
    $stmt = $conn->prepare($sql);

    // Periksa apakah prepare berhasil
    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("ssisi", $kode_ruangan, $nama_ruangan, $kapasitas, $lokasi, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data ruangan berhasil diperbarui!'); window.location.href='Ruangan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data ruangan: " . $conn->error . "');</script>";
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
    <title>Edit Data Ruangan</title>
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
    <h1>Edit Data Ruangan</h1>

    <form method="POST">
        <input type="hidden" name="ruangan_id" value="<?php echo $ruangan['ruangan_id']; ?>">

        <label>Kode Ruangan: <input type="text" name="kode_ruangan" value="<?php echo htmlspecialchars($ruangan['kode_ruangan']); ?>" required></label><br>

        <label>Nama Ruangan: <input type="text" name="nama_ruangan" value="<?php echo htmlspecialchars($ruangan['nama_ruangan']); ?>" required></label><br>

        <label>Kapasitas: <input type="number" name="kapasitas" value="<?php echo htmlspecialchars($ruangan['kapasitas']); ?>" required></label><br>

        <label>Lokasi: <input type="text" name="lokasi" value="<?php echo htmlspecialchars($ruangan['lokasi']); ?>" required></label><br>

        <button type="submit" name="edit">Simpan Perubahan</button>
    </form>
</body>
</html>
