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

// Ambil data pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = $search ? 
    "SELECT * FROM dosen WHERE nama_dosen LIKE '%$search%' OR nip LIKE '%$search%'" :
    "SELECT * FROM dosen";

// Tambah data dosen
if (isset($_POST['add'])) {
    $nama_dosen = $_POST['nama_dosen'];
    $nip = $_POST['nip'];
    $email = $_POST['email'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $sql = "INSERT INTO dosen (nama_dosen, nip, email, telp, alamat) VALUES ('$nama_dosen', '$nip', '$email', '$telp', '$alamat')";
    if ($conn->query($sql)) {
        echo "<script>alert('Data dosen berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data dosen: " . $conn->error . "');</script>";
    }
}

// Hapus data dosen
if (isset($_GET['delete'])) {
    $dosen_id = $_GET['delete'];

    // Pastikan ID valid
    if (is_numeric($dosen_id)) {
        $sql = "DELETE FROM dosen WHERE dosen_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("i", $dosen_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data dosen berhasil dihapus!');</script>";
        } else {
            echo "<script>alert('Gagal menghapus data dosen: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak valid.');</script>";
    }
}

// Edit data dosen
if (isset($_POST['edit'])) {
    // Pastikan ID tersedia sebelum digunakan
    if (isset($_POST['dosen_id']) && !empty($_POST['dosen_id'])) {
        $id = $_POST['dosen_id'];
        $nama = $_POST['nama_dosen'];
        $nip = $_POST['nip'];
        $email = $_POST['email'];
        $telp = $_POST['telp'];
        $alamat = $_POST['alamat'];

        $sql = "UPDATE dosen SET nama_dosen='$nama_dosen', nip='$nip', email='$email', telp='$telp', alamat='$alamat' WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $dosen_id, $nama_dosen, $nip, $email, $telp, $alamat);
        if ($stmt->execute()) {
            echo "<script>alert('Data dosen berhasil diperbarui!');</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data dosen: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak ditemukan, tidak dapat memperbarui data.');</script>";
    }
}

// Ambil semua data dosen
$result = $conn->query("SELECT * FROM dosen");
?>

<link href="vendor/font-awesome.css" rel="stylesheet">
    <link href="vendor/ionicons.css" rel="stylesheet">
    <link href="vendor/perfect-scrollbar.css" rel="stylesheet">
    <link href="vendor/rickshaw.min.css" rel="stylesheet">
    <link href="vendor/github.css" rel="stylesheet">
    <link href="vendor/jquery.dataTables.css" rel="stylesheet">
    <link href="vendor/select2.min.css" rel="stylesheet">
    <link href="vendor/sweetalert.css" rel="stylesheet">
    <link href="vendor/starlight-ith.css" rel="stylesheet">
<link rel="stylesheet" href="Dosen.css">

<!DOCTYPE html>
<html lang="en">

<body class="collapsed-menu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Dosen | Institut Teknologi Bacharuddin Jusuf Habibie</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Manajemen Data Dosen</h1>

    <form method="POST">
        <h2>Tambah/Edit Data Dosen</h2>
        <input type="hidden" name="id" id="id" value="">
        <label>Nama Dosen: <input type="text" name="nama_dosen" id="nama_dosen" required></label><br>
        <label>NIP: <input type="text" name="nip" id="nip" required></label><br>
        <label>Email: <input type="email" name="email" id="email" required></label><br>
        <label>Telp: <input type="text" name="telp" id="telp"></label><br>
        <label>Alamat: <textarea name="alamat" id="alamat"></textarea></label><br>
        <button type="submit" name="add" id="add-btn">Tambah Dosen</button>
        <button type="submit" name="edit" id="edit-btn" style="display: none;">Simpan Perubahan</button>
    </form>

    <!-- Form Pencarian -->
    <form method="GET" action="">
        <label>Cari Data: 
            <input type="text" name="search" placeholder="Masukkan nama atau NIP" value="<?php echo htmlspecialchars($search); ?>">
        </label>
        <button type="submit">Cari</button>
    </form>
    
    <h2>Daftar Dosen</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Dosen</th>
                <th>NIP</th>
                <th>Email</th>
                <th>Telp</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['nama_dosen']; ?></td>
                <td><?php echo $row['nip']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['telp']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['dosen_id']; ?>">Edit</a>
                    <a href="?delete=<?php echo $row['dosen_id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
