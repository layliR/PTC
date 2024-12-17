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

// Tambah data mata kuliah
if (isset($_POST['add'])) {
    $nama_matkul = $_POST['nama_matkul'];
    $kode_matkul = $_POST['kode_matkul'];
    $sks = $_POST['sks'];
    $semester = $_POST['semester'];
    $prodi_id = $_POST['prodi_id'];  // Ganti nama_prodi dengan prodi_id

    $sql = "INSERT INTO matakuliah (nama_matkul, kode_matkul, sks, semester, prodi_id) VALUES ('$nama_matkul', '$kode_matkul', '$sks', '$semester', '$prodi_id')";
    if ($conn->query($sql)) {
        echo "<script>alert('Data mata kuliah berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data mata kuliah: " . $conn->error . "');</script>";
    }
}

// Hapus data mata kuliah
if (isset($_GET['delete'])) {
    $matkul_id = $_GET['delete'];

    // Pastikan ID valid
    if (is_numeric($matkul_id)) {
        $sql = "DELETE FROM matakuliah WHERE matkul_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Query Prepare Gagal: ' . $conn->error);
        }
        $stmt->bind_param("i", $matkul_id);
        if ($stmt->execute()) {
            echo "<script>alert('Data mata kuliah berhasil dihapus!');</script>";
        } else {
            echo "<script>alert('Gagal menghapus data mata kuliah: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak valid.');</script>";
    }
}

// Edit data mata kuliah
if (isset($_POST['edit'])) {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $nama_matkul = $_POST['nama_matkul'];
        $kode_matkul = $_POST['kode_matkul'];
        $sks = $_POST['sks'];
        $semester = $_POST['semester'];
        $prodi_id = $_POST['prodi_id'];  // Ganti nama_prodi dengan prodi_id

        $sql = "UPDATE matakuliah SET nama_matkul=?, kode_matkul=?, sks=?, semester=?, prodi_id=? WHERE matkul_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiii", $nama_matkul, $kode_matkul, $sks, $semester, $prodi_id, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Data mata kuliah berhasil diperbarui!');</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data mata kuliah: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('ID tidak ditemukan, tidak dapat memperbarui data.');</script>";
    }
}

// Ambil semua data mata kuliah
$result = $conn->query("SELECT * FROM matakuliah");

// Ambil daftar prodi untuk dropdown
$result_prodi = $conn->query("SELECT prodi_id, nama_prodi FROM prodi");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Mata Kuliah</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Manajemen Data Mata Kuliah</h1>

    <form method="POST">
        <h2>Tambah/Edit Data Mata Kuliah</h2>
        <input type="hidden" name="id" id="id" value="">
        <label>Nama Mata Kuliah: <input type="text" name="nama_matkul" id="nama_matkul" required></label><br>
        <label>Kode Mata Kuliah: <input type="text" name="kode_matkul" id="kode_matkul" required></label><br>
        <label>SKS: <input type="number" name="sks" id="sks" required></label><br>
        <label>Semester: 
            <select name="semester" id="semester" required>
                <option value="">Pilih Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
                <option value="5">Semester 5</option>
                <option value="6">Semester 6</option>
                <option value="7">Semester 7</option>
                <option value="8">Semester 8</option>
            </select>
        </label><br>
        
        <!-- Dropdown untuk memilih Prodi berdasarkan prodi_id -->
        <label>Prodi: 
            <select name="prodi_id" id="prodi_id" required>
                <option value="">Pilih Program Studi</option>
                <?php
                while ($row_prodi = $result_prodi->fetch_assoc()) {
                    echo "<option value='" . $row_prodi['prodi_id'] . "'>" . $row_prodi['nama_prodi'] . "</option>";
                }
                ?>
            </select>
        </label><br>
        
        <button type="submit" name="add" id="add-btn">Tambah Mata Kuliah</button>
        <button type="submit" name="edit" id="edit-btn" style="display: none;">Simpan Perubahan</button>
    </form>

    <h2>Daftar Mata Kuliah</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Mata Kuliah</th>
                <th>Kode Mata Kuliah</th>
                <th>SKS</th>
                <th>Semester</th>
                <th>Prodi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['nama_matkul']; ?></td>
                <td><?php echo $row['kode_matkul']; ?></td>
                <td><?php echo $row['sks']; ?></td>
                <td><?php echo $row['semester']; ?></td>
                <td>
                    <?php 
                    // Mengambil nama prodi berdasarkan prodi_id
                    $prodi_id = $row['prodi_id'];
                    $prodi_result = $conn->query("SELECT nama_prodi FROM prodi WHERE prodi_id = $prodi_id");
                    $prodi_row = $prodi_result->fetch_assoc();
                    echo $prodi_row['nama_prodi']; 
                    ?>
                </td>
                <td>
                    <a href="edit_matkul.php?id=<?php echo $row['matkul_id']; ?>">Edit</a>
                    <a href="?delete=<?php echo $row['matkul_id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
