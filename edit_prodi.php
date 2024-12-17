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

// Ambil data prodi berdasarkan ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $prodi_id = $_GET['id'];
    $sql = "SELECT * FROM prodi WHERE prodi_id=?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("i", $prodi_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $prodi = $result->fetch_assoc();
    } else {
        die("Data prodi tidak ditemukan.");
    }

    $stmt->close();
} else {
    die("ID prodi tidak ditemukan.");
}

// Edit data prodi
if (isset($_POST['edit'])) {
    $id = $_POST['prodi_id'];
    $kode_prodi = $_POST['kode_prodi']; // Ambil kode_prodi dari form
    $nama_prodi = $_POST['nama_prodi'];
    $jurusan = $_POST['jurusan'];

    if (empty($nama_prodi) || empty($jurusan)) {
        echo "<script>alert('Nama prodi dan jurusan tidak boleh kosong.');</script>";
    } else {
        $sql = "UPDATE prodi SET kode_prodi=?, nama_prodi=?, jurusan=? WHERE prodi_id=?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }

        $stmt->bind_param("sssi", $kode_prodi, $nama_prodi, $jurusan, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Data prodi berhasil diperbarui!'); window.location.href='prodi.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data prodi: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prodi</title>
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
    <h1>Edit Data Prodi</h1>

    <form method="POST">
        <input type="hidden" name="prodi_id" value="<?php echo $prodi['prodi_id']; ?>">
        
        <!-- Tampilkan Kode Prodi di Formulir -->
        <label>Kode Prodi: <input type="text" name="kode_prodi" value="<?php echo htmlspecialchars($prodi['kode_prodi']); ?>" readonly></label><br>
        
        <label>Nama Prodi: <input type="text" name="nama_prodi" value="<?php echo htmlspecialchars($prodi['nama_prodi']); ?>" required></label><br>
        
        <label>Jurusan: <input type="text" name="jurusan" value="<?php echo htmlspecialchars($prodi['jurusan']); ?>" required></label><br>
        
        <button type="submit" name="edit">Perbarui Prodi</button>
    </form>

</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>
