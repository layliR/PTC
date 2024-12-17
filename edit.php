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

// Ambil data dosen berdasarkan ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $dosen_id = $_GET['id'];
    $sql = "SELECT * FROM dosen WHERE dosen_id=?";  // Ubah 'id' menjadi 'dosen_id'
    $stmt = $conn->prepare($sql);

    // Periksa apakah prepare berhasil
    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("i", $dosen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $dosen = $result->fetch_assoc();
    } else {
        die("Data dosen tidak ditemukan.");
    }

    $stmt->close();
} else {
    die("ID dosen tidak ditemukan.");
}

// Edit data dosen
if (isset($_POST['edit'])) {
    $id = $_POST['dosen_id'];  // Ubah 'id' menjadi 'dosen_id'
    $nama = $_POST['nama_dosen'];
    $nip = $_POST['nip'];
    $email = $_POST['email'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $sql = "UPDATE dosen SET nama_dosen=?, nip=?, email=?, telp=?, alamat=? WHERE dosen_id=?";  // Ubah 'id' menjadi 'dosen_id'
    $stmt = $conn->prepare($sql);

    // Periksa apakah prepare berhasil
    if ($stmt === false) {
        die('Query Prepare Gagal: ' . $conn->error);
    }

    $stmt->bind_param("sssssi", $nama, $nip, $email, $telp, $alamat, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data dosen berhasil diperbarui!'); window.location.href='Dosen.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data dosen: " . $conn->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Dosen</title>
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
    <h1>Edit Profil Dosen</h1>

    <form method="POST">
        <input type="hidden" name="dosen_id" value="<?php echo $dosen['dosen_id']; ?>"> <!-- Ubah 'id' menjadi 'dosen_id' -->

        <label>Nama Dosen: <input type="text" name="nama_dosen" value="<?php echo $dosen['nama_dosen']; ?>" required></label><br>
        <label>NIP: <input type="text" name="nip" value="<?php echo $dosen['nip']; ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?php echo $dosen['email']; ?>" required></label><br>
        <label>Telp: <input type="text" name="telp" value="<?php echo $dosen['telp']; ?>"></label><br>
        <label>Alamat: <textarea name="alamat"><?php echo $dosen['alamat']; ?></textarea></label><br>

        <button type="submit" name="edit">Simpan Perubahan</button>
    </form>

</body>
</html>
