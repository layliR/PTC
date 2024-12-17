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

// Ambil data dosen, matakuliah, dan prodi untuk dropdown
$dosen_result = $conn->query("SELECT dosen_id, nama_dosen FROM dosen");
$matkul_result = $conn->query("SELECT matkul_id, nama_matkul FROM matakuliah");
$prodi_result = $conn->query("SELECT prodi_id, nama_prodi FROM prodi");

// Ambil semua data dosen_mk
$result = $conn->query("SELECT * FROM dosen_mk");

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    
    // Cek apakah ID valid di database
    $stmt = $conn->prepare("SELECT dosen_mk_id FROM dosen_mk WHERE dosen_mk_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // ID valid, lakukan delete
        $stmt = $conn->prepare("DELETE FROM dosen_mk WHERE dosen_mk_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: dosen_mk.php");
            exit;
        } else {
            echo "Gagal menghapus data.";
        }
    } else {
        echo "ID tidak ditemukan di database.";
    }
    $stmt->close();
}

// Mendapatkan nama dosen, nama matkul, dan nama prodi berdasarkan ID
function getNamaDosen($dosen_id, $conn) {
    $stmt = $conn->prepare("SELECT nama_dosen FROM dosen WHERE dosen_id = ?");
    $stmt->bind_param("i", $dosen_id);
    $stmt->execute();
    $stmt->bind_result($nama_dosen);
    $stmt->fetch();
    $stmt->close();
    return $nama_dosen;
}

function getNamaMatkul($matkul_id, $conn) {
    $stmt = $conn->prepare("SELECT nama_matkul FROM matakuliah WHERE matkul_id = ?");
    $stmt->bind_param("i", $matkul_id);
    $stmt->execute();
    $stmt->bind_result($nama_matkul);
    $stmt->fetch();
    $stmt->close();
    return $nama_matkul;
}

function getNamaProdi($prodi_id, $conn) {
    $stmt = $conn->prepare("SELECT nama_prodi FROM prodi WHERE prodi_id = ?");
    $stmt->bind_param("i", $prodi_id);
    $stmt->execute();
    $stmt->bind_result($nama_prodi);
    $stmt->fetch();
    $stmt->close();
    return $nama_prodi;
}

$edit_data = null; // Menyimpan data yang akan diedit

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM dosen_mk WHERE dosen_mk_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    
    if ($result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
    } else {
        echo "<script>alert('ID tidak ditemukan!'); window.location.href = 'dosen_mk.php';</script>";
    }
    $stmt->close();
}

// Handle Update data dosen_mk
if (isset($_POST['edit'])) {
    // Pastikan ID tersedia sebelum digunakan
    if (isset($_POST['dosen_mk_id']) && !empty($_POST['dosen_mk_id'])) {
        $id = $_POST['dosen_mk_id'];
        // Lakukan update seperti biasa
    } else {
        // Tambah data baru jika tidak ada ID
        if (isset($_POST['dosen_id']) && isset($_POST['matkul_id']) && isset($_POST['prodi_id']) && isset($_POST['jurusan'])) {
            $dosen_id = $_POST['dosen_id'];
            $matkul_id = $_POST['matkul_id'];
            $prodi_id = $_POST['prodi_id'];
            $jurusan = $_POST['jurusan'];

            // Query insert untuk menambah data baru
            $sql = "INSERT INTO dosen_mk (dosen_id, matkul_id, prodi_id, jurusan) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $dosen_id, $matkul_id, $prodi_id, $jurusan);
            
            if ($stmt->execute()) {
                echo "<script>alert('Data dosen berhasil ditambahkan!'); window.location.href = 'dosen_mk.php';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan data dosen: " . $conn->error . "');</script>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Dosen-MK</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        form { margin: 20px 0; }
    </style>
    <script>
        function updateJurusan() {
            var prodi_id = document.getElementById('prodi_id').value;
            var jurusanSelect = document.getElementById('jurusan');
            jurusanSelect.innerHTML = "";

            fetch('get_jurusan.php?prodi_id=' + prodi_id)
                .then(response => response.json())
                .then(data => {
                    data.forEach(jurusan => {
                        var option = document.createElement("option");
                        option.value = jurusan;
                        option.textContent = jurusan;
                        jurusanSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching jurusan:', error));
        }
    </script>
</head>
<body>
    <h1>Manajemen Data Dosen-MK</h1>

    <form method="POST" action="">
        <h2><?php echo $edit_data ? 'Edit Data Dosen-MK' : 'Tambah Data Dosen-MK'; ?></h2>
        <input type="hidden" name="dosen_mk_id" value="<?php echo $edit_data['dosen_mk_id'] ?? ''; ?>">
        <label>Dosen: 
            <select name="dosen_id" required>
                <?php while ($dosen = $dosen_result->fetch_assoc()) { ?>
                    <option value="<?php echo $dosen['dosen_id']; ?>" 
                        <?php echo ($edit_data && $edit_data['dosen_id'] == $dosen['dosen_id']) ? 'selected' : ''; ?>>
                        <?php echo $dosen['nama_dosen']; ?>
                    </option>
                <?php } ?>
            </select>
        </label><br>
        <label>Matakuliah: 
            <select name="matkul_id" required>
                <?php while ($matkul = $matkul_result->fetch_assoc()) { ?>
                    <option value="<?php echo $matkul['matkul_id']; ?>" 
                        <?php echo ($edit_data && $edit_data['matkul_id'] == $matkul['matkul_id']) ? 'selected' : ''; ?>>
                        <?php echo $matkul['nama_matkul']; ?>
                    </option>
                <?php } ?>
            </select>
        </label><br>
        <label>Prodi: 
            <select name="prodi_id" id="prodi_id" required onchange="updateJurusan()">
                <?php while ($prodi = $prodi_result->fetch_assoc()) { ?>
                    <option value="<?php echo $prodi['prodi_id']; ?>" 
                        <?php echo ($edit_data && $edit_data['prodi_id'] == $prodi['prodi_id']) ? 'selected' : ''; ?>>
                        <?php echo $prodi['nama_prodi']; ?>
                    </option>
                <?php } ?>
            </select>
        </label><br>
        <label>Jurusan: 
            <select name="jurusan" id="jurusan" required>
                <option value="">Pilih Prodi terlebih dahulu</option>
            </select>
        </label><br>
        <button type="submit" name="edit">Update Data</button>
    </form>

    <!-- Tombol untuk "Buatkan Jadwal" -->
    <!-- Tombol untuk "Buatkan Jadwal" -->
    <div>
        <h3>Pengelolaan Jadwal</h3>
        <button onclick="window.location.href='buat_jadwal.php'">Buat Jadwal</button>
    </div>


    <h2>Daftar Dosen-MK</h2>
    <table>
        <thead>
            <tr>
                <th>Dosen</th>
                <th>Matakuliah</th>
                <th>Prodi</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo getNamaDosen($row['dosen_id'], $conn); ?></td>
                <td><?php echo getNamaMatkul($row['matkul_id'], $conn); ?></td>
                <td><?php echo getNamaProdi($row['prodi_id'], $conn); ?></td>
                <td><?php echo $row['jurusan']; ?></td>
                <td>
                    <a href="edit_dosen_mk.php?id=<?php echo $row['dosen_mk_id']; ?>">Edit</a>
                    <a href="?delete=<?php echo $row['dosen_mk_id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
