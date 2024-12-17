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

// Ambil ID waktu dari URL (query string)
if (isset($_GET['id'])) {
    $waktu_id = $_GET['id'];

    // Ambil data waktu berdasarkan ID
    $sql = "SELECT * FROM waktu WHERE waktu_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $waktu_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $waktu = $result->fetch_assoc();
    } else {
        echo "<script>alert('Data waktu tidak ditemukan!'); window.location.href = 'Waktu.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID waktu tidak valid!'); window.location.href = 'Waktu.php';</script>";
    exit;
}

// Update data waktu
if (isset($_POST['update'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // Validasi input
    if (empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // SQL untuk memperbarui data waktu
        $sql_update = "UPDATE waktu SET hari = ?, jam_mulai = ?, jam_selesai = ? WHERE waktu_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $hari, $jam_mulai, $jam_selesai, $waktu_id);

        if ($stmt_update->execute()) {
            echo "<script>alert('Data waktu berhasil diperbarui!'); window.location.href = 'Waktu.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data waktu: " . $conn->error . "');</script>";
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
    <title>Edit Data Waktu</title>
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

<h1>Edit Data Waktu</h1>

<!-- Form untuk mengedit data waktu -->
<form method="POST">
    <label>Hari: <input type="text" name="hari" value="<?php echo htmlspecialchars($waktu['hari']); ?>" required></label><br>
    <label>Jam Mulai: <input type="time" name="jam_mulai" value="<?php echo htmlspecialchars($waktu['jam_mulai']); ?>" required></label><br>
    <label>Jam Selesai: <input type="time" name="jam_selesai" value="<?php echo htmlspecialchars($waktu['jam_selesai']); ?>" required></label><br>
    <button type="submit" name="edit">Simpan Perubahan</button>
</form>
</body>
</html>
